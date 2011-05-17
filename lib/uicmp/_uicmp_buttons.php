<?php

/**
 * @file _uicmp_buttons.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * Component displaying group of buttons in header section of the _uicmp_tab
 * component.
 */

require_once CHASSIS_LIB . 'uicmp/_uicmp_pool.php';

class _uicmp_buttons extends _uicmp_pool
{
	/**
	 * Constructor.
	 * 
	 * @param <_uicmp_head> $parent reference to tab header component instance
	 * @param <string> $id identifier of the component
	 */
	public function __construct ( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UICMP . 'buttons.html';
	}
}

?>