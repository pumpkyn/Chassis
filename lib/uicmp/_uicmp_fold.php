<?php

/**
 * @file _uicmp_fold.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * Class representing tab fold element.
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_comp.php";

class _uicmp_fold extends _uicmp_comp
{
	/**
	 * Text string to display.
	 * 
	 * @var <string>
	 */
	public $title = null;

	/**
	 * Constructor.
	 *
	 * @param <_uicmp_tab> $parent parent component instance
	 * @param <string> $id identifier of the component
	 * @param <string> $title text to display in the fold
	 */
	public function __construct( &$parent, $id, $title )
	{
		parent::__construct( $parent, $id );
		$this->type = __CLASS__;
		$this->title = $title;
		$this->renderer = CHASSIS_UI . 'uicmp/fold.html';
	}

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateJs ( ) { }

	/**
	 * Returns text to be displayed.
	 * 
	 * @return <string>
	 */
	public function getTitle( ) { return $this->title; }
	
}

?>