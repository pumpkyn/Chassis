<?php

/**
 * @file _smarty_wrapper.php
 * @author giorno
 * @package Chassis
 * @subpackage UI
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_CFG . 'config.php';
require_once CHASSIS_3RD . 'Smarty/Smarty.class.php';

/**
 * Wrapper class for configuring and maintaining Smarty template engine instance
 * for framework purposes. Singleton pattern is used as this wrapper may be used
 * by several other objects.
 */
class _smarty_wrapper
{
	/**
	 * Instance of class for Singleton pattern.
	 * @var <_smarty_wrapper>
	 */
	private static $instance = null;

	/**
	 * Reference to Smarty engine instance
	 * @var <Smarty>
	 */
	private $smarty = null;

	/**
	 * Path to index file, i.e. first template to be executed.
	 * 
	 * @var <string>
	 */
	private $index = NULL;

	/**
	 * Indicates end of instance lifecycle. It is a safeguard to prevent render
	 * method being called twice.
	 *
	 * @var <bool>
	 */
	private $done = false;

	/**
	 * Default constructor.
	 */
	private function __construct ( )
	{
		$this->smarty = new Smarty( );

		$this->smarty->compile_dir	= CHASSIS_TMP . 'smarty_cmpl/';
		$this->smarty->cache_dir	= CHASSIS_TMP . 'smarty_cache/';
	}

	/**
	 * Singleton interface. Provides instance of a class.
	 * @return _smarty_wrapper Singleton instance
	 */
	public static function getInstance ( )
	{
		if ( self::$instance == null )
			self::$instance = new _smarty_wrapper( );

		return self::$instance;
	}

	/**
	 * Sets Smarty engine instance.
	 *
	 * @param <Smarty> $smarty Smarty engine instance
	 * @return <bool> success, false means Smarty instance was already set
	 */
	public function setEngine ( &$smarty )
	{
		if ( $this->smarty == null )
		{
			$this->smarty = $smarty;
			return true;
		}
		else
			return false;
	}

	/**
	 * Configures Smarty default template dir.
	 * 
	 * @param <string> $dir path to base template dir
	 */
	public function setDir ( $dir )
	{
		$this->smarty->template_dir = $dir;
	}

	/**
	 * Sets main content of the solution.
	 * 
	 * @param <type> $path path to main content HTML file (absolute or relative
	 * to $this->smarty->template_dir
	 */
	public function setContent ( $path )
	{
		$this->index = $path;
	}

	/**
	 * Returns reference to Smarty engine instance held by this Singleton.
	 *
	 * @return <Smarty>
	 */
	public function getEngine ( ) { return $this->smarty; }

	/**
	 * Sets Smarty variables used by framework templates. End of object
	 * lifecycle.
	 */
	public function render ( )
	{
		if ( ( $this->smarty != null ) && ( !$this->done ) )
		{
			/* Framework templates directories */
			$this->smarty->assign( 'CHASSIS_UI_ROOT', CHASSIS_UI );
			$this->smarty->assign( 'CHASSIS_UI_UICMP', CHASSIS_UICMP );

			/* User templates directory */
			$this->smarty->assign( 'CHASSIS_UI_USRDIR', $this->smarty->template_dir );

			/* Display main Smarty template = content file */
			$this->smarty->assign( 'CHASSIS_UI_INDEX', $this->index );
			$this->smarty->display( $this->index );
			
			$this->done = true;
		}
	}
}

?>