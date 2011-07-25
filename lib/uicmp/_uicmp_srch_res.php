<?php

/**
 * @file _uicmp_srch_res.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_pool.php";

/**
 * UICMP component for search list resizer. This component is mainly used for
 * resizing lists with search results, but it can contain additional items. Part
 * of search solution.
 */
class _uicmp_srch_res extends _uicmp_pool
{
	/**
	 * Actual size of page.
	 *
	 * @var <int>
	 */
	protected $sizer = 10;

	/**
	 * Constructor.
	 *
	 * @param <_uicmp_body> $parent reference to parent component instance
	 * @param <string> $id identifier of the component
	 * @param <string> $js_var name of client side Javascript instance
	 * @param <int> $size current size
	 */
	public function __construct ( &$parent, $id, $js_var, $size )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'uicmp/search_resizer.html';
		$this->jsVar	= $js_var;
		$this->size		= $size;
	}

	/**
	 * Returns actual size for the resizer.
	 *
	 * @return <int>
	 */
	public function getSize ( ) { return $this->size; }
}

?>