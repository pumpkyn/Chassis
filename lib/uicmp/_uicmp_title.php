<?php

require_once CHASSIS_LIB . "uicmp/_uicmp_comp.php";

/**
 * @file _uicmp_title.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * Class representing caption of the tab.
 */
class _uicmp_title extends _uicmp_comp
{
	/**
	 * Text string to display.
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Contructor.
	 *
	 * @param _uicmp_head $parent reference to parent widget
	 * @param string $id identifier of the component
	 * @param string $title text of the title
	 */
	public function __construct( &$parent, $id, $title )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->title	= $title;
		$this->renderer	= CHASSIS_UI . 'uicmp/title.html';
	}

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateJs ( ) { }

	/**
	 * Returns text to be displayed.
	 *
	 * @return string
	 */
	public function getTitle( ) { return $this->title; }
}

?>