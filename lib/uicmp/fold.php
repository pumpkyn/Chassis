<?php

/**
 * @file fold.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . "uicmp/uicmp.php";

/**
 * Class representing tab fold element.
 */
class fold extends uicmp
{
	/**
	 * Text string to display.
	 * 
	 * @var string
	 */
	public $title = null;

	/**
	 * Constructor.
	 *
	 * @param tab $parent parent component instance
	 * @param string $id identifier of the component
	 * @param string $title text to display in the fold
	 */
	public function __construct( &$parent, $id, $title )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->title	= $title;
		$this->renderer	= CHASSIS_UI . 'uicmp/fold.html';
	}

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateReqs ( ) { }

	/**
	 * Returns text to be displayed.
	 * 
	 * @return string
	 */
	public function getTitle( ) { return $this->title; }
}

?>