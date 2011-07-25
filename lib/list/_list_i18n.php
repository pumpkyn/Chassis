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
	 * @param string $lang two-character code of language
	 */
	public function __construct ( $lang = 'en' )
	{
		$i18n = CHASSIS_I18N . 'uicmp/' . $lang . '.php';
		if (file_exists( $i18n ) )
		{
			include $i18n;
			$this->messages = $_uicmp_i18n;
		}
	}
	
	/**
	 * Read interface for framework localization messages.
	 * 
	 * @return array 
	 */
	public function getI18n ( ) { return $this->messages; }
}

?>