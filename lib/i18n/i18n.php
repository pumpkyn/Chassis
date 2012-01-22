<?php

/**
 * @file i18n.php
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * [Smarty]
 * Singleton loading framework localization file depending on language code
 * supplied and providing these resources to other objects, primarily within
 * the framework. Once initialized, it is used from both, PHP logic and Smarty
 * templates to obtain framework localization. Put intentionally out of any
 * namespaces to be accessible from the Smarty template engine.
 */
class i18n
{
	/**
	 * Singleton instance.
	 * @var i18n
	 */
	protected static $instance = NULL;
	
	/**
	 * Two-character code of language currently being used.
	 * @var string
	 */
	protected static $lang = NULL;
	
	/**
	 * Array of localization strings.
	 * @var array 
	 */
	protected $messages = NULL;
	
	/**
	 * Loads and parses resources by given language code. Hidden implementation.
	 */
	protected function __construct ( )
	{
		$file = CHASSIS_I18N . self::$lang . '.php';
		if ( file_exists( $file ) )
			include $file;
		else
		{
			include CHASSIS_I18N . 'en.php';
			self::$lang = 'en';
		}
		
		$this->messages = $__chassis_msg;
	}
	
	/**
	 * Hide clone constructor.
	 */
	protected function __clone ( ) { }
	
	/**
	 * Singleton interface.
	 * @param string $lang two-char lanugage code
	 * @return i18n 
	 */
	public static function getInstance ( $lang = NULL )
	{
		if ( !is_null( $lang ) )
			self::$lang = $lang;
		
		if ( is_null( self::$lang ) )
			self::$lang = 'en';
		
		/**
		 * Reinitialize if different language is to be used or instance has not
		 * been created yet.
		 */
		if ( ( ( !is_null( $lang ) ) && ( self::$lang != $lang ) ) || ( is_null( self::$instance ) ) )
			self::$instance = new i18n( );
		
		return self::$instance;
	}
	
	/**
	 * Read interface to localization messages. If key is not provided,
	 * reference to whole messages storage is provided.
	 * @param string $key optional, used onyl for lookup
	 * @return mixed
	 */
	public function msg ( $key = NULL )
	{
		if ( !is_null( $key ) && array_key_exists( $key, $this->messages ) )
			return $this->messages[$key];
		
		if ( is_null( $key ) )
			return $this->messages;
		
		return NULL;
	}
}

?>