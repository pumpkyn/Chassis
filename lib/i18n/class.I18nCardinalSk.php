<?php

/**
 * @file class.I18nCardinalSk.php
 * @author giorno
 * @package Chassis
 * @subpackage I18N
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'i18n/class.I18nCardinal.php';

/**
 * Specialization of class I18nCardinal for Slovak language and languages sharing
 * similar .
 */
class I18nCardinalSk extends I18nCardinal
{
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

			case 2:
			case 3:
			case 4:
				$index = 2;
			break;

			default:	$index = 3; break;
		}

		return sprintf( $this->Strings[$index], $amount );
	}
}

?>