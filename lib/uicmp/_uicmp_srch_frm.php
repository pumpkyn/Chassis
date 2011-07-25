<?php

/**
 * @file _uicmp_srch_frm.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_pool.php";

/**
 * Simple form for searching by single keyword. It is derived from _uicmp_pool
 * component due to its need to use _uicmp_gi_ind and separator for
 * indicator of outgoing Ajax request. Part of search solution.
 */
class _uicmp_srch_frm extends _uicmp_pool
{
	/**
	 * Prefill keywords.
	 * 
	 * @var <string>
	 */
	protected $keywords = NULL;

	/**
	 * Constructor.
	 *
	 * @param <_uicmp_head> $parent reference to parent component instance
	 * @param <string> $id identifier of the component
	 * @param <string> $js_var name of Javascript variable, created by _vcmp_search to control search operation
	 * @param <string> $keywords text to populate search field
	 */
	public function __construct( &$parent, $id, $js_var, $keywords )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'uicmp/search_form.html';
		$this->jsVar	= $js_var;
		$this->keywords	= $keywords;
	}

	/**
	 * Returns keywords to prefill form.
	 *
	 * @return <string>
	 */
	public function getKeywords( ) { return $this->keywords; }
}

?>