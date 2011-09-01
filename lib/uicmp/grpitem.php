<?php

/**
 * @file grpitem.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . 'uicmp/uicmp.php';
require_once CHASSIS_LIB . 'uicmp/buttons.php';
require_once CHASSIS_LIB . 'uicmp/vsearch.php';

/**
 * Implementation of additional item, which can be placed into resizer row. This
 * is usually to provide additional actions specific for given tab component.
 * GI stands for 'group item'.
 */
class grpitem extends uicmp
{
	/**
	 * Constants for item type.
	 */
	const IT_TXT	= 'txt';	// plain text ( like '|' for separator)
	const IT_IND	= 'ind';	// status indicator (e.g. for Ajax operations)
	const IT_BT		= 'bt';		// button
	const IT_CHK	= 'chk';	// checkbox
	const IT_A		= 'a';		// anchor-like control

	/**
	 * Defines type of the item.
	 * 
	 * @var string
	 */
	protected $what = self::IT_A;

	/**
	 * Text to display.
	 *
	 * @var string
	 */
	protected $title = NULL;

	/**
	 * Javascript code executed in onClick event.
	 *
	 * @var string
	 */
	protected $action = NULL;

	/**
	 * Additional CSS style for the HTML element.
	 *
	 * @var class
	 */
	protected $class = NULL;

	/**
	 * Constructor. Parent can be _uicmp_buttons or _uicmp_resizer (or something
	 * else :). Automatically registers component into known parents.
	 *
	 * @param pool $parent parent component instance
	 * @param string $id identifier of the component
	 * @param string $what type of the item (see member constants)
	 * @param string $title text to display
	 * @param string $action Javascript code to execute on onClick event
	 * @param string $class additional CSS style for the item
	 */
    public function  __construct( &$parent, $id, $what, $title, $action = NULL, $class = NULL )
	{
		parent::__construct( $parent, $id );
		
		/**
		 * Automatic hook-up to known types of parent.
		 */
		if ( ( !$this->hooked ) && ( ( $parent instanceof buttons ) || ( $parent instanceof srchres ) ) )
		{
			$parent->add( $this );
			$this->hooked = TRUE;
		}
		
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UICMP . 'gi.html';
		$this->title	= $title;
		$this->action	= $action;
		$this->class	= $class;
		$this->what		= $what;
	}

	/**
	 * Read interface for item title.
	 *
	 * @return string
	 */
	public function getTitle ( ) { return $this->title; }

	/**
	 * Read interface for item Javascript action.
	 *
	 * @return string
	 */
	public function getAction ( ) { return $this->action; }

	/**
	 * Read interface for item additional CSS style.
	 *
	 * @return <string>
	 */
	public function getClass ( ) { return $this->class; }

	/**
	 * Read interface for item true essence.
	 *
	 * @return string
	 */
	public function getWhat ( ) { return $this->what; }

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function  generateReqs ( ) { }
}

?>