<?php

/**
 * @file _session_wrapper.php
 * @author giorno
 * @package Chassis
 * @subpackage Session
 * @license Apache License, Version 2.0, see LICENSE file
 */
 
require_once CHASSIS_LIB . 'session/_session.php';

 /** 
  * Factory method to provide all scopes available _session instance.
 */
class _session_wrapper
{
	/**
	 * Singleton instance.
	 * 
	 * @var <_session_wrapper>
	 */
	protected static $instance = NULL;

	/**
	 * Singleton interface.
	 *
	 * @return <_session_wrapper>
	 */
	public static function getInstance ( )
	{
		if ( is_null ( static::$instance ) )
			static::$instance = new _session( );

		return static::$instance;
	}

	/**
	 * If there is no existing instance, set it.
	 *
	 * @param <_session> $instance
	 */
	public static function setInstance ( &$instance )
	{
		if ( is_null ( static::$instance ) )
			static::$instance = $instance;
	}
}

?>