<?php

/**
 * @file headline.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . "uicmp/uicmp.php";

/**
 * Class representing caption of the tab.
 */
class headline extends uicmp
{
	/**
	 * Text string to display.
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Contructor. Automatically registers component into known parents.
	 *
	 * @param head $parent reference to parent widget
	 * @param string $id identifier of the component
	 * @param string $title text of the title
	 */
	public function __construct( &$parent, $id, $title )
	{
		parent::__construct( $parent, $id );
		
		/**
		 * Automatic hook-up to known types of parent.
		 */
		if ( ( !$this->hooked ) && ( $parent instanceof head ) )
		{
			$parent->add( $this );
			$this->hooked = TRUE;
		}
		
		$this->type		= __CLASS__;
		$this->title	= $title;
		$this->renderer	= CHASSIS_UI . 'uicmp/title.html';
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