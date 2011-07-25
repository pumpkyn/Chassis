<?php

/**
 * @file class.I18nCardinal.php
 * @author giorno
 * @package Chassis
 * @subpackage I18N
 * @license Apache License, Version 2.0, see LICENSE file
 */
 
/**
 * Parent to all localization classes handling amount information. It implements
 * logic for English language (use only three strings).
 */
class I18nCardinal
{
	/*
	 * Array containing localization strings.
	 */
	protected $Strings  = null;
	
	/*
	 * This is suposed to pass variable length of strings. Their usage is put
	 * into Out() method.
	 */
	function __construct( )
	{
		$this->Strings = func_get_args( );
	}

	/**
	 * Return proper string. Decision is made upon number passed as first
	 * argument.
	 *
	 * @param $amount (int)
	 */
	public function ToString ( $amount )
	{
		switch ( $amount )
		{
			case 0:		$index = 0; break;
			case 1:		$index = 1; break;
			default:	$index = 2; break;
		}

		return sprintf( $this->Strings[$index], $amount );
	}

}

?>