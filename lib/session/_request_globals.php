<?php

/**
 * @file _request_globals.php
 * @author giorno
 * @package Chassis
 * @subpackage Session
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Singleton keeping variables, which are supposed to be available throughout
 * whole solution.
 */
class _request_globals
{
	/**
	 * Data structure for values.
	 *
	 * @var <array>
	 */
	protected $storage = NULL;

	/**
	 * Singleton instance.
	 *
	 * @var <_request_globals>
	 */
	protected static $instance = NULL;

	/**
	 * Hide constructors.
	 */
	protected function __construct ( ) { }
	protected function __clone ( ) { }

	/**
	 * Singleton interface
	 *
	 * @return <_request_globals>
	 */
	public static function getInstance ( )
	{
		if ( is_null( static::$instance ) )
			static::$instance = new static( );

		return static::$instance;
	}

	/**
	 * Write interface for variable.
	 *
	 * @param <string> $key
	 * @param <mixed> $value
	 */
	public function set ( $key, &$value )
	{
		$this->storage[$key] = $value;
	}

	/**
	 * Read interface for variable.
	 * 
	 * @param <string> $key
	 * @return <mixed>
	 */
	public function get ( $key )
	{
		return $this->storage[$key];
	}
}

?>