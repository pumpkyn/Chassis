<?php

/**
 * @file __wwg.Spacer.php
 * @author jstanik
 * @package Chassis
 * @subpackage Apps
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'apps/_wwg.Wwg.php';

/**
 *  Object representing dynamic dummy web widget, the spacer.
 */
class Spacer extends Wwg
{
	/**
	 * Static variable used for generating unique ID of the widget.
	 * 
	 * @var <int>
	 */
	private static $lastId = 0;

	/**
	 * Constructor. This object is passive, so its whole specialized logic is
	 * placed in the constructor.
	 */
    public function __construct ( )
	{
		$this->id = static::$lastId++;
		$this->template = CHASSIS_UI . '_wwg.Spacer.html';
	}
}

?>