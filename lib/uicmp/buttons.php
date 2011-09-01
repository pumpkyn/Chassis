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
require_once CHASSIS_LIB . 'uicmp/tab.php';

/** 
 * Component displaying group of buttons in header section of the _uicmp_tab
 * component.
 */
class buttons extends pool
{
	/**
	 * Constructor. Automatically registers component into known parents.
	 * 
	 * @param head $parent reference to tab header component instance
	 * @param string $id identifier of the component
	 */
	public function __construct ( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		
		/**
		 * Automatic hook-up to known types of parent.
		 */
		if ( ( !$this->hooked ) && ( ( $parent instanceof head ) || ( $parent instanceof body ) ) )
		{
			$parent->add( $this );
			$this->hooked = TRUE;
		}
		
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UICMP . 'buttons.html';
	}
}

?>