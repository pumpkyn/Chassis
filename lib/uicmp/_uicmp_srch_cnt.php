<?php

/**
 * @file _uicmp_srch_cnt.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_comp.php";

/**
 * Container for search results. Part of search solution.
 */
class _uicmp_srch_cnt extends _uicmp_comp
{
	/**
	 * Constructor.
	 *
	 * @param <_uicmp_body> $parent reference to parent component instance
	 * @param <string> $id identifier of the component
	 */
	public function __construct ( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type = __CLASS__;
		$this->renderer = CHASSIS_UI . 'uicmp/search_container.html';
	}

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateJs ( ) { }
}

?>