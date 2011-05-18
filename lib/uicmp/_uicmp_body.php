<?php

/**
 * @file _uicmp_body.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * UICMP abstraction of tab body section.
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_pool.php";

class _uicmp_body extends _uicmp_pool
{
	/**
	 * Constructor.
	 *
	 * @param <_uicmp_tab> $parent reference to tab component instance
	 * @param <string> $id identifier of the component
	 */
	public function __construct ( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'uicmp/body.html';
	}
}

?>