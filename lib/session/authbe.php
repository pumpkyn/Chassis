<?php

/**
 * @file authbe.php
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis;

require_once CHASSIS_LIB . 'libpdo.php';

/**
 * Stub, a common parent of all authentication plugins. Uses global repository
 * PDO.
 * 
 * BE stands for backend.
 */
abstract class authbe
{
	/**
	 * Passwords are modifiable. If this flags is set, method passwd() must be
	 * implemented in derived class.
	 */
	const ABE_MODPASSWD = 1;
	
	/**
	 * Backend provides method useradd() to create new user. It can be then used
	 * by administration UI's and their server logic to create new users.
	 * Derived class must provide body for the method.
	 */
	const ABE_CREATE = 2;

	/**
	 * Instance of settings object used for access to global scope configuration
	 * of the beackend (e.g. hostnames or connect details).
	 * @var \io\creat\chassis\session\settings
	 */
	protected $sett = NULL;
	
	/**
	 * Capabilities of the plugin.
	 * @var int
	 */
	protected $flags = 0;
	
	/**
	 * PDO instance referenced in global repository.
	 * @var PDO
	 */
	protected $pdo = NULL;
	
	/**
	 * Constructor.
	 * @param _settings $sett reference to settings instance for access to plugin config
	 */
	public function __construct ( &$sett, $flags = 0 )
	{
		$this->pdo = session\repo::getInstance()->get( session\repo::PDO );
		$this->sett = $sett;
		$this->flags = $flags;
	}
	
	/**
	 * Sets flag(s) by given mask
	 * @param int $mask flag bits
	 */
	public function setFlag( $mask ) { $this->flags |= $mask; }
	
	/**
	 * Checks if given flag is set for the instance.
	 * @param int $flag flag bit
	 * @return bool
	 */
	public function hasFlag( $flag ) { return ( $this->flags & $flag ) > 0; }
	
	/**
	 * This signature must be implemented in the derived class in order to
	 * perform validation of login and password pairs. This method must assure
	 * that user ID was created for user by calling mkid() member.
	 */
	public abstract function validate ( $login, $password );
	
	/**
	 * Member is supposed to provide name (host/domain name) of authorization
	 * authority in the form of string. If plugin does not use such an
	 * authority, return NULL.
	 */
	public abstract function authority ( );
	
	/**
	 * Empty (stub) method. When implemented, should provide change password
	 * feature.
	 * @param string $newpass plain new password
	 * @return bool
	 */
	public function passwd ( $newpass ) { }
	
	/**
	 * Empty (stub) method defining interface for adding new users to the
	 * backend storage (whatever it is).
	 * @param string $name name of the user
	 * @param string $password plain password for the user
	 * @param bool $enabled account is enabled
	 * @return bool
	 */
	public function useradd ( $name, $password, $enabled = TRUE ) { }
	
	/**
	 * Checks if the login does not exist and creates it, thus giving the user
	 * local user ID. Method fails if given login is already assigned to root
	 * user (uid=1).
	 * @param string $login 
	 * @return mixed false on failure, otherwise ID of the user
	 */
	protected function mkid ( $login )
	{
		$sql = $this->pdo->prepare( "SELECT `" . \Config::F_UID . "`,`" . \Config::F_ENABLED . "`
				FROM `" . \Config::T_USERS . "`
				WHERE `" . \Config::F_LOGIN . "` = ?" );
		
		$sql->bindValue( 1, $login );
		$user = pdo1l( $sql, \PDO::FETCH_NUM );
		
		// Record does exist, but user was disabled. Treated as failure to
		// create record, therefore failure to login.
		if ( is_array( $user ) && ( (int)$user[1] == 0 ) )
			return FALSE;
		
		$uid = $user[0];
		if ( (int)$uid == 1 )
			return FALSE;
		elseif ( (int)$uid > 1 )
			return $uid;
		else // == 0
		{
			$this->pdo->prepare( "INSERT INTO `" . \Config::T_USERS . "`
				SET `" . \Config::F_LOGIN . "` = ?,
					`" . \Config::F_ENABLED . "` = \"1\"" )->execute( array( $login ) );
	
			$uid = (int)$this->pdo->lastInsertId();
			
			if ( (int)$uid > 1 )
				return $uid;
			else
				return FALSE;
		}
	}
}

?>