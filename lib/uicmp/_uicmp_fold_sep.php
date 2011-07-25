<?php

/**
 * @file _uicmp_fold_sep.php
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_comp.php";

/**
 * Dummy component to create gap between folds.
 */
class _uicmp_fold_sep extends _uicmp_comp
{
	/**
	 * Constructor.
	 *
	 * @param <_uicmp_tab> $parent parent component instance
	 * @param <string> $id identifier of the component
	 */
	public function __construct( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type = __CLASS__;
		$this->renderer = CHASSIS_UI . 'uicmp/sepfold.html';
	}

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateJs ( ) { }
}

?>