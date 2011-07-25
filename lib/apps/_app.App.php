<?php

/**
 * @file _app.App.php
 * @author giorno
 * @package Chassis
 * @subpackage Apps
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'apps/_app_registry.php';

/**
 * Abstract class providing interface to application instance.
 */
abstract class App
{
	/**
	 * (unique) Identifier of application. Execution is triggerred by this id.
	 * 
	 * @var <string>
	 */
	protected $id = null;

	/**
	 * Localization messages.
	 * @var <array>
	 */
	protected $messages = NULL;

	/**
	 * Path to Smarty template to be included in HTML document <head> tag.
	 * 
	 * @var <string>
	 */
	protected $headTemplatePath = NULL;

	/**
	 * Path to Smarty teplace to be rendered as main content, the index.
	 *
	 * @var <string>
	 */
	protected $indexTemplatePath = NULL;

	/**
	 * Array containing applpication menu items.
	 *
	 * @var <array>
	 */
	protected $menu = NULL;

	/**
	 * Prevent access to instantiation and cloning.
	 */
	protected function __construct ( ) { }
	protected function __clone ( ) { }

	/**
	 * Returns identifier of application (its URL id).
	 *
	 * @return <string>
	 */
	public function getId ( ) { return $this->id; }

	/**
	 * Returns <head> tag content for application.
	 *
	 * @return <string>
	 */
	public function getHeadTemplatePath ( ) { return $this->headTemplatePath; }

	/**
	 * Returns main HTML content for application.
	 *
	 * @return <string>
	 */
	public function getIndexTemplatePath ( ) { return $this->indexTemplatePath; }

	/**
	 * Returns (reference) to localization messages of the application.
	 *
	 * @return <array>
	 */
	public function getMessages ( ) { return $this->messages; }

	/**
	 * Implementation of this method is supposed to provide an instance of
	 * AppIcon class.
	 *
	 * @todo for now it uses array, but that should be changed to conform desc
	 */
	abstract public function icon ( );

	/**
	 * Implementation of this method is supposed to perform main execution of
	 * application, i.e. an equivalent of index script or Ajax dispatcher.
	 */
	abstract public function exec ( );

	/**
	 * Implementation of this method is supposed to be handler of events. See
	 * static event codes in _app_registry Singleton.
	 */
	abstract public function event ( $event );
}

?>