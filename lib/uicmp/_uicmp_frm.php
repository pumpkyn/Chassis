<?php

/**
 * @file _uicmp_frm.php
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'uicmp/_uicmp_pool.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_fi.php';

/**
 * Component representing universal form body.
 */
class _uicmp_frm extends _uicmp_pool
{
	/**
	 * Constructor.
	 * 
	 * @param _uicmp_body $parent parent component, the tab body
	 * @param string $id identifier of the component
	 */
	public function __construct( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->jsPrefix		= '_uicmp_frm_i:';
		$this->renderer		= CHASSIS_UI . 'uicmp/frm.html';
	}
	
	/**
	 * Adds new component into the form.
	 * 
	 * @param _uicmp_fi $item form component
	 */
	public function add( &$item )
	{
		/**
		 * Allow to add only form item components.
		 */
		if ( $item instanceof _uicmp_fi )
			parent::add( $item );
	}
}

?>