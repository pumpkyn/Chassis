<?php

/**
 * @file _i18n_loader.php
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Singleton loading framework localization file depending on language code
 * supplied and providing these resources to other objects, primarily within
 * the framework.
 */
class _i18n_loader
{
	/**
	 * Singleton instance.
	 * 
	 * @var _i18n_loader
	 */
	protected static $instance = NULL;
	
	/**
	 * Two-character code of language currently being used.
	 * 
	 * @var string
	 */
	protected static $lang = NULL;
	
	/**
	 * Array of localization strings.
	 * 
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
	 * 
	 * @param string $lang two-char lanugage code
	 * @return _i18n_loader 
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
			self::$instance = new _i18n_loader( );
		
		return self::$instance;
	}
	
	/**
	 * Read interface to localization messages. If key is not provided,
	 * reference to whole messages storage is provided.
	 * 
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