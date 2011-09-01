<?php


/**
 * @file info.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . "uicmp/headline.php";

/**
 * Specialization of UICMP title component to provide informational message in
 * tab head or body.
 */
class info extends headline
{
	/**
	 * Additional stylesheet class.
	 *
	 * @var string
	 */
	public $class = null;

	/**
	 * Contructor. Automatically registers component into known parents.
	 *
	 * @param head $parent reference to parent widget
	 * @param string $id identifier of the component
	 * @param string $title text of the title
	 * @param string $class additional CSS class name(s)
	 */
	public function __construct( &$parent, $id, $text, $class = NULL )
	{
		parent::__construct( $parent, $id, $text );
		
		/**
		 * Automatic hook-up to known types of parent.
		 */
		if ( ( !$this->hooked ) && ( $parent instanceof head ) )
		{
			$parent->add( $this );
			$this->hooked = TRUE;
		}
		
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