<?php

/**
 * @file repo.php
 * @author giorno
 * @package Chassis
 * @subpackage Session
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\session;

/**
 * Object serving as global repository for instances, de-facto replacement for
 * global variables. Extendable by client code.
 */
abstract class repo
{
	// Key for global PDO instance. Client code is supposed to provide a PDO
	// instance prior using any framework objects accessing database without
	// providing them with a separate PDO instance.
	const PDO = 'io.creat.chassis.pdo';
	
	/**
	 * Associative array serving as data backend
	 * @var array
	 */
	protected $storage = NULL;
	
	/**
	 * Singleton interface
	 * @var io\creat\chassis\session\repo
	 */
	protected static $instance = NULL;
	
	protected function __construct ( ) { }
	protected function __clone ( ) { }
	
	abstract protected function post ( );


	/**
	 * Singleton interface.
	 * @return singleton instance
	 */
	public static function getInstance ( )
	{
		if ( is_null( static::$instance ) )
		{
			static::$instance = new static( );
			static::$instance->post( );
		}

		return static::$instance;
	}
	
	/**
	 * Stores a new key-value pair into repository.
	 * @param string $key
	 * @param mixed $val 
	 */
	public function set ( $key, &$val ) { $this->storage[$key] = $val; }
	
	/**
	 * Read access for the repository. Provides value stored under the key, NULL
	 * if record does not exist.
	 * @param string $key
	 * @return mixed
	 */
	public function get ( $key )
	{
		if ( array_key_exists( $key, $this->storage ) )
			return $this->storage[$key];
		
		return NULL;
	}
}

?>