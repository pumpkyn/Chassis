<?php

/**
 * @file buttons.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . 'uicmp/pool.php';

/** 
 * Component displaying group of buttons in header section of the _uicmp_tab
 * component.
 */
class buttons extends pool
{
	/**
	 * Constructor.
	 * 
	 * @param head $parent reference to tab header component instance
	 * @param string $id identifier of the component
	 */
	public function __construct ( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UICMP . 'buttons.html';
	}
}

?>