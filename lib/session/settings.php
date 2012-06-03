<?php

/**
 * @file settings.php
 * @author giorno
 * @package Chassis
 * @subpackage Session
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\session;

require_once CHASSIS_CFG . 'class.Config.php';
require_once CHASSIS_LIB . 'session/repo.php';

/**
 * Class responsible for accessing and manipulating variable scope settings. It
 * is configured only by scope of the settings. Security identifiers are
 * extracted internaly from \io\creat\chassis\session instance.
 */
abstract class settings extends \Config
{
	// Settings stored for whole namespace, e.g. application.
	const SCOPE_GLOBAL		= 'G';
	
	// Settings stored for user only.
	const SCOPE_USER		= 'U';
	
	// Session settings. Unused.
	const SCOPE_SESSION		= 'S';

	/**
	 * Internal cache for settings.
	 * @var array
	 */
	protected $table = NULL;
	
	/**
	 * PDO instance providing access to the backend.
	 * @var PDO
	 */
	protected $pdo = NULL;
	
	/**
	 * Prepared statement to speedup table updates.
	 * @var PDOStatement
	 */
	protected $writer = NULL;
	
	/**
	 * Prepared statement to speedup table loading.
	 * @var PDOStatement
	 */
	protected $reader = NULL;

	/**
	 * Constructor.
	 *
	 * @param char $scope defines scope of instance
	 * @param string $ns namespace (e.g. solution)
	 */
	public function __construct ( $scope, $ns, $id = '', $pdo = NULL )
	{
		$this->pdo = is_null( $pdo )
						? repo::getInstance( )->get( repo::PDO )
						: $pdo;
		
		if ( !is_null( $this->pdo ) )
		{
			// Statement to write single setting.
			$this->writer = $this->pdo->prepare( "INSERT INTO `" . self::T_SETTINGS . "`
													( `" . self::F_KEY . "`, `" . self::F_VALUE . "`, `" . self::F_ID . "`, `" . self::F_NS . "`, `" . self::F_SCOPE . "` )
													VALUES ( :key, :val, :id, :ns, :scope )
													ON DUPLICATE KEY UPDATE `" . self::F_VALUE . "` = :val " );
			
			$this->writer->bindValue( ':id', $id );
			$this->writer->bindValue( ':ns', $ns );
			$this->writer->bindValue( ':scope', $scope );
			
			// Statement to load whole table.
			if ( $scope == self::SCOPE_USER )
				$this->reader = $this->pdo->prepare( "SELECT `" . self::F_KEY . "`,`" . self::F_VALUE . "`
														FROM `" . self::T_SETTINGS . "`
														WHERE ( ( `" . self::F_SCOPE . "` = :scope AND `" . self::F_ID . "` = :id )
																OR ( `" . self::F_SCOPE . "` = \"" . self::SCOPE_GLOBAL . "\" ) )
															AND ( `" . self::F_NS . "` = :ns )" );
			else
				$this->reader = $this->pdo->prepare( "SELECT `" . self::F_KEY . "`,`" . self::F_VALUE . "`
														FROM `" . self::T_SETTINGS . "`
														WHERE ( `" . self::F_SCOPE . "` = :scope AND `" . self::F_ID . "` = :id )
															AND ( `" . self::F_NS . "` = :ns )" );

			$this->reader->bindValue( ':id', $id );
			$this->reader->bindValue( ':ns', $ns );
			$this->reader->bindValue( ':scope', $scope );
		}
		
		$this->load( );
	}

	/**
	 * Load all settings to internal array.
	 */
	public function load ( )
	{	
		$this->reader->execute( );
		while ( $record = $this->reader->fetch( \PDO::FETCH_NUM ) )
			$this->table[$record[0]] = $record[1];
	}

	/**
	 * Setter of a setting property.
	 * @deprecated
	 * @param string $key identifier of setting entry
	 * @param mixed $value value of setting
	 */
	public function saveOne ( $key, $value ) { $this->set( $key, $value ); }

	/**
	 * Setter of a setting property. No need to protect single SQL with
	 * a transaction context.
	 * @param string $key identifier of setting entry
	 * @param mixed $value value of setting
	 */
	private function set ( $key, $value )
	{
		$this->writer->bindValue( 'key', $key );
		$this->writer->bindValue( 'val', $value );
		$this->writer->execute( );
		$this->table[$key] = $value;
	}

	/**
	 * Read access interface. Returns kery referenced value from internal array.
	 * @param string $key
	 * @return mixed
	 */
	public function get ( $key )
	{
		if ( ( is_array( $this->table ) ) && ( array_key_exists( $key, $this->table ) ) )
			return $this->table[$key];
		else
			return NULL;
	}
}

?>