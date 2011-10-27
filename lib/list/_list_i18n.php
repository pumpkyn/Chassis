<?php

/**
 * @file _list_i18n.php
 * @author giorno
 * @package Chassis
 * @subpackage List
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Common ancestor to all List objects, which have to carry their own
 * localization (from framework i18n files).
 */
class _list_i18n
{
	/**
	 * Associative array of localization messages.
	 * 
	 * @var array
	 */
	protected $messages = NULL;
	
	/**
	 * Constructor.
	 * 
	 * @param _i18n_loader $i18n_loader instance of localization provider
	 */
	public function __construct ( $i18n_loader )
	{
		if ( !is_null( $i18n_loader ) )
			$this->messages = $i18n_loader->msg( );
	}
	
	/**
	 * Read interface for framework localization messages.
	 * 
	 * @return array 
	 */
	public function getI18n ( ) { return $this->messages; }
}

?>