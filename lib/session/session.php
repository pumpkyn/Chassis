<?php

/**
 * @file _session.php
 * @author giorno
 * @package Chassis
 * @subpackage Session
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis;

require_once CHASSIS_CFG . 'class.Config.php';
require_once CHASSIS_LIB . 'libfw.php';
require_once CHASSIS_LIB . 'libpdo.php';
require_once CHASSIS_LIB . 'session/repo.php';

/**
 * Singleton providing session tracking object responsible for authentication
 * and persistence of user session.
 * 
 * @todo remove IP field from tables
 * @todo is 'enabled' field still necessary?
 */
class session extends \Config
{
	/**
	 * Singleton instance.
	 * @var \io\creat\chassis\session
	 */
	protected static $instance = NULL;
	
	/**
	 * Authentication backend used for verification of username and password. If
	 * NULL, default (code_users) table is used.
	 * @var \io\creat\chassis\authbe
	 */
	protected $authbe = NULL;
	
	/**
	 * User Id. Integer number from users table.
	 * @var int
	 */
	private $uid = NULL;

	/**
	 * User's nickname (=login).
	 * @var string
	 */
	private $nick = NULL;
	
	/**
	 * User's e-mail address.
	 * @var string
	 */
	private $email = NULL;

	/**
	 * Indicated if session exists and user is authenticated.
	 * @var bool
	 */
	private $signed = FALSE;

	/**
	 * Cookie. Client identifier.
	 * @var string
	 */
	private $cid;

	/**
	 * Cookie. Session identifier.
	 * @var string
	 */
	private $sid;

	/**
	 * Cookie. Autologin token identifier.
	 * @var string
	 */
	private $alToken;
	
	/**
	 * Reference to global repository PDO instance.
	 * @var PDO
	 */
	private $pdo = NULL;

	/**
	 * Concealed constructor. This class uses PDO registered in global
	 * repository Singleton.
	 */
	protected function __construct ( )
	{
		$this->pdo = \io\creat\chassis\session\repo::getInstance( )->get( \io\creat\chassis\session\repo::PDO );
		
		// Load client Id from cookies.
		$this->handleClientId( );

		// Session Id from cookies is invalid.
		if ( !$this->validateSessionId( ) )
			$this->validateAutologin();
	}
	
	/**
	 * Concealed copy constructor.
	 */
	protected function __clone() { }
	
	/**
	 * Singleton interface.
	 * @return \io\creat\chassis\session
	 */
	public static function getInstance ( )
	{
		if ( is_null ( static::$instance ) )
			static::$instance = new session( );

		return static::$instance;
	}
	
	/**
	 * Set (replace) authentication backend used for verification of username
	 * and password.
	 * @param \io\creat\chassis\authbe $authbe reference to authentication backend instance
	 */
	public function setAuthBe ( &$authbe ) { $this->authbe = $authbe; }

	/**
	 * If there is client ID in cookies, return its value and renew expiration datetime, if ID is not
	 * set in cookies, it shall be created and its value returned.
	 */
	private function handleClientId ( )
	{
		$this->cid = '';

		if ( array_key_exists( static::COOKIE_CLIENTID, $_COOKIE ) )
			$this->cid = (string)$_COOKIE[static::COOKIE_CLIENTID];

		/**
		 * There is no cookie for client id or one we got from client side is
		 * malformed. It means it is either expired or solution was never used
		 * from this browser instance.
		 */
		if ( ( $this->cid == '' ) || ( strlen( $this->cid ) > 32 ) )
			$this->cid = substr( _fw_rand_hash( ), 0, 32 );
		
		_fw_set_cookie( self::COOKIE_CLIENTID, $this->cid, time( ) + static::COOKIE_EXPIRATION * 24 * 60 * 60 );
	}

	/**
	 * Validates session using cookie values from the browser.
	 * @return bool
	 */
	private function validateSessionId ( )
	{
		$this->wipe( );
		
		/*
		 * try SID from cookies
		 */
		$this->sid = '';
		if ( array_key_exists(self::COOKIE_SESSIONID, $_COOKIE) )
			$this->sid = (string)$_COOKIE[self::COOKIE_SESSIONID];

		/**
		 * Check if session Id from cookies is valid.
		 */
		if ( $this->sid != '' )
		{
			$sql = $this->pdo->prepare( "SELECT `" . self::F_UID . "`
					FROM `" . self::T_SESSIONS . "`
					WHERE `" . self::F_SID . "` = :sid
					AND `" . self::F_CLID . "` = :cid" );
			
			$sql->bindValue( ':sid', $this->sid );
			$sql->bindValue( ':cid', $this->cid );
			
			if ( $this->uid = (int)pdo1f( $sql ) )
			{
				 $this->signed = true;
				 
				 $this->pdo->prepare( "UPDATE `" . self::T_SESSIONS . "`
					SET `" . self::F_VALID . "` = (NOW() + INTERVAL " . self::SESSIONEXPIRATION * 60 . " MINUTE)
					WHERE `" . self::F_SID . "` = ?
					AND `" . self::F_CLID . "` = ?" )->execute( array( $this->sid, $this->cid ) );
				 
				_fw_set_cookie( self::COOKIE_SESSIONID, $this->sid, 0 );
				
				$this->load( );

				return true;
			}
			$this->uid = NULL;
		}
		return false;
	}

	/**
	 * Validates autologin cookies and tokens.
	 * @return bool
	 */
	private function validateAutologin ( )
	{
		/*
		 * try autologin token from cookies
		 */
		$this->alToken = '';
		if ( array_key_exists(self::COOKIE_TOKENID, $_COOKIE) )
			$this->alToken = (string)$_COOKIE[self::COOKIE_TOKENID];

		if ( $this->alToken != '' )
		{
			$sql = $this->pdo->prepare( "SELECT `" . self::F_UID . "`
					FROM `" . self::T_SIGNTOKENS . "`
					WHERE `" . self::F_CLID . "` = :cid
					AND `" . self::F_TOKEN . "` = :altoken" );
			
			$sql->bindValue( ':cid', $this->cid );
			$sql->bindValue( ':altoken', $this->alToken );
			
			if ( $this->uid = (int)pdo1f( $sql ) )
			{
				$this->pdo->prepare( "UPDATE `" . self::T_SIGNTOKENS . "`
					SET `" . self::F_VALID . "` = (NOW() + INTERVAL " . self::COOKIE_EXPIRATION * 24 * 60 * 60 . " SECOND)
					WHERE `" . self::F_TOKEN . "` = ? AND
							`" . self::F_UID . "` = ? AND
							`" . self::F_CLID . "` = ?" )->execute( array( $this->alToken, $this->uid, $this->cid ) );

				_fw_set_cookie( self::COOKIE_TOKENID, $this->alToken, time( ) + self::COOKIE_EXPIRATION * 24 * 60 * 60 );

				$this->create( );
				$this->load( );

				return true;
			}
			$this->uid = NULL;
		}

		return false;
	}
	
	/**
	 * Checks if password for the signed user is correct or not.
	 * @param string $password plain password
	 * @return bool 
	 */
	public function checkPassword ( $password )
	{
		if ( is_null( $this->uid ) )
			return false;

		$sql = $this->pdo->prepare( "SELECT `" . self::F_UID . "`
				FROM `" . self::T_USERS . "`
				WHERE `" . self::F_UID . "` = :uid
				AND `" . self::F_PASSWD . "` = :password" );

		$sql->bindValue( ':uid', $this->uid );
		$sql->bindValue( ':password', _fw_hash_passwd( $password ) );
		$match = (int)pdo1f( $sql );
		
		return ( $this->uid === $match );
	}
	
	/**
	 * Sets new password for signed user.
	 * @param string $password plain new password
	 */
	public function setPassword ( $password )
	{
		$this->pdo->prepare( "UPDATE `" . self::T_USERS . "`
			SET `" . self::F_PASSWD . "` = ?
			WHERE `" . self::F_UID . "` = ?" )->execute( array( _fw_hash_passwd( $password ), $this->uid ) );
	}

	/**
	 * Performs login operation.
	 * @param string $ns namespace,identifier of user solution
	 * @param string $username login
	 * @param string $password password
	 * @param bool $auto autologin flag
	 * @return bool
	 */
	public function login ( $ns, $username, $password, $auto = FALSE )
	{
		$hash =  _fw_hash_passwd( $password );
		$cache = $this->uid;

		$this->uid = FALSE;
		
		// First, if configured, plugin is used.
		if ( !is_null( $this->authbe ) )
		{
			$sql = $this->pdo->prepare( "SELECT `" . self::F_UID . "`
					FROM `" . self::T_USERS . "`
					WHERE `" . self::F_LOGIN . "` = :login" );
			
			$sql->bindValue( ':login', $username );
			
			// Skip authentication using the plugin for root username (uid=1).
			if ( (int)pdo1f( $sql ) != 1 )
				$this->uid = $this->authbe->validate( $username, $password );
		}
		
		// Login using plugin has failed, root user is trying to connect, or
		// plugin is not configured. This will match also any table record
		// created before plugin has been configured.
		if ( (int)$this->uid < 1 )
		{
			$sql = $this->pdo->prepare( "SELECT `" . self::F_UID . "`
										FROM `" . self::T_USERS . "`
										WHERE `" . self::F_LOGIN . "` = :login
										AND `" . self::F_PASSWD . "` = :password
										AND `" . self::F_ENABLED . "` = '1'" );
			
			$sql->bindValue( ':login', $username );
			$sql->bindValue( ':password', $hash );
			$this->uid = (int)pdo1f( $sql );
		}
		
		// Any of authentication methods was successful.
		if ( ( $this->uid > 0 ) )
		{
			/**
			 * Create session record.
			 */
			if ( !$this->create( ) )
				return false;

			/**
			 * Create record in autologin tokens table.
			 */
			if ( $auto )
			{
				$this->alToken = _fw_rand_hash( );
				
				$this->pdo->prepare( "INSERT INTO `" . self::T_SIGNTOKENS . "`
					SET `" . self::F_TOKEN . "` = ?,
					`" . self::F_UID . "` = ?,
					`" . self::F_CLID . "` = ?,
					`" . self::F_VALID . "` = (NOW() + INTERVAL " . self::COOKIE_EXPIRATION * 24 * 60 * 60 . " SECOND)" )->execute( array( $this->alToken, $this->uid, $this->cid ) );
				
				_fw_set_cookie( self::COOKIE_TOKENID, $this->alToken, time( ) + self::COOKIE_EXPIRATION * 24 * 60 * 60 );
			}

			$this->pdo->prepare( "INSERT INTO `" . self::T_LOGINS . "`
				SET `" . self::F_UID . "` = ?,
				`" . self::F_NS . "` = ?,
				`" . self::F_STAMP . "` = NOW()" )->execute( array( $this->uid, $ns ) );
			
			return true;
		}
		else
		{
			/*
			 * Revert from cache.
			 */
			$this->uid = $cache;
			return false;
		}
	}

	/**
	 * Perform logout and erase all session, token and cookies.
	 */
	public function logout ( )
	{
		$this->pdo->prepare( "DELETE FROM `" . self::T_SESSIONS . "`
			WHERE `" . self::F_SID . "` = ?
				AND `" . self::F_CLID . "` = ?
				AND `" . self::F_UID . "` = ?" )->execute( array( $this->sid, $this->cid, $this->uid ) );
		
		$this->pdo->prepare( "DELETE FROM `" . self::T_SIGNTOKENS . "`
					WHERE `" . self::F_CLID . "` = ?" )->execute( array( $this->cid ) );

		_fw_set_cookie( self::COOKIE_SESSIONID, '', 0);
		_fw_set_cookie( self::COOKIE_TOKENID, '', time() - self::COOKIE_EXPIRATION * 24 * 60 * 60 );
		_fw_set_cookie( self::COOKIE_CLIENTID, '', time() - self::COOKIE_EXPIRATION * 24 * 60 * 60 );
		$this->signed = FALSE;

		return !$this->validateSessionId( );
	}

	/**
	 * Create session for user.
	 * @return bool
	 */
	private function create ( )
	{
		$this->wipe( );
		
		$ipAddr = $_SERVER['REMOTE_ADDR'];

		/**
		 * Creates session records in the table.
		 */
		$this->sid = _fw_rand_hash( );
		$this->pdo->prepare( "INSERT INTO `" . self::T_SESSIONS . "`
			SET `" . self::F_SID . "` = ?,
				`" . self::F_UID . "` = ?,
				`" . self::F_CLID . "` = ?,
				`" . self::F_VALID . "` = (NOW() + INTERVAL " . self::SESSIONEXPIRATION * 60 . " SECOND) " )->execute( array( $this->sid, $this->uid, $this->cid ) );

		$sql = $this->pdo->prepare( "SELECT `" . self::F_SID . "`
							FROM `" . self::T_SESSIONS . "`
							WHERE `" . self::F_SID . "` = ?" );
		
		$sql->bindValue( 1, $this->sid );
		
		// Session has been successfuly created.
		if ( is_array( pdo1l( $sql, \PDO::FETCH_NUM ) ) )
		{
			_fw_set_cookie( self::COOKIE_SESSIONID, $this->sid, 0/*time() + self::COOKIE_EXPIRATION * 24 * 60  * 60*/ );

			$this->signed = true;
			return true;
		}
	}

	/**
	 * Wipe all expired sessions.
	 */
	private function wipe ( ) { $this->pdo->exec( "DELETE FROM `" . self::T_SESSIONS . "` WHERE `" . self::F_VALID . "` < NOW()" ); }

	/**
	 * Loads user entry fields from the table.
	 */
	private function load ( )
	{
		$sql = $this->pdo->prepare( "SELECT `" . self::F_LOGIN . "`,`" . self::F_EMAIL . "`
				FROM `" . self::T_USERS . "`
				WHERE `" . self::F_UID . "` = ?" );
		
		$sql->bindValue( 1, $this->uid );
		if ( is_array( $entry = pdo1l( $sql, \PDO::FETCH_ASSOC ) ) )
		{
			$this->nick = $entry[self::F_LOGIN];
			$this->email = $entry[self::F_EMAIL];
		}
	}

	/**
	 * Queries status of session.
	 * @return bool
	 */
	public function isSigned ( ) { return $this->signed === TRUE; }

	/**
	 * Returns login name of the user.
	 * @return string
	 */
	public function getNick ( ) { return $this->nick; }

	/**
	 * Returns user identifer.
	 * @return int
	 */
	public function getUid ( ) { return $this->uid; }

	/**
	 * Returns session identifier.
	 * @return string
	 */
	public function getSid ( ) { return $this->sid; }
	
	/**
	 * Read interface for user e-mail address.
	 * @return string
	 */
	public function getEmail ( ) { return $this->email; }
}

?>