<?php


/**
 * @file _uicmp_info.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_title.php";

/**
 * Specialization of UICMP title component to provide informational message in
 * tab head or body.
 */
class _uicmp_info extends _uicmp_title
{
	/**
	 * Additional stylesheet class.
	 *
	 * @var string
	 */
	public $class = null;

	/**
	 * Contructor.
	 *
	 * @param _uicmp_head $parent reference to parent widget
	 * @param string $id identifier of the component
	 * @param string $title text of the title
	 * @param string $class additional CSS class name(s)
	 */
	public function __construct( &$parent, $id, $text, $class = NULL )
	{
		parent::__construct( $parent, $id, $text );
		$this->type		= __CLASS__;
		$this->class	= $class;
		$this->renderer	= CHASSIS_UI . 'uicmp/info.html';
	}

	/**
	 * Returns additional CSS class.
	 *
	 * @return string
	 */
	public function getClass( ) { return $this->class; }
}

?>