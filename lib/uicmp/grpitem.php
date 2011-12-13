<?php

/**
 * @file grpitem.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . 'uicmp/item.php';
require_once CHASSIS_LIB . 'uicmp/buttons.php';
require_once CHASSIS_LIB . 'uicmp/vsearch.php';

/**
 * Implementation of additional item, which can be placed into resizer row. This
 * is usually to provide additional actions specific for given tab component.
 * GI stands for 'group item'.
 */
class grpitem extends item
{
	/** @deprecated */
	const IT_TXT	= self::GIT_TEXT;
	/** @deprecated */
	const IT_IND	= self::GIT_INDICATOR;
	/** @deprecated */
	const IT_BT		= self::GIT_BUTTON;
	/** @deprecated */
	const IT_CHK	= self::GIT_CHECKBOX;
	/** @deprecated */
	const IT_A		= self::GIT_ANCHOR;
	/** @deprecated */
	const IT_ENUM	= self::GIT_SELECT;

	/**
	 * Additional CSS style for the HTML element. This is extra parameter.
	 * @var string
	 */
	protected $class = NULL;

	/**
	 * Constructor. Parent can be _uicmp_buttons or _uicmp_resizer (or something
	 * else :). Automatically registers component into known parents.
	 *
	 * @param pool $parent parent component instance
	 * @param string $id identifier of the component
	 * @param string $itype type of the item (see member constants)
	 * @param string $title text to display
	 * @param string $action Javascript code to execute on onClick event
	 * @param string $class additional CSS style for the item
	 */
    public function  __construct( &$parent, $id, $itype, $title, $action = NULL, $class = NULL )
	{
		// Pack action into callbacks array before it is passed to parent's
		// constructor.
		if ( !is_null( $action ) )
			switch ( $itype )
			{
				case self::GIT_TEXT:
					$cbs = NULL;
				break;
				case self::GIT_SELECT:
					$cbs = array( 'onChange' => $action );
				break;
				
				case self::GIT_BUTTON:
				case self::GIT_ANCHOR:
				case self::GIT_CHECKBOX:
				default:
					$cbs = array( 'onClick' => $action );
				break;
			}
		else
			$cbs = NULL;
		
		parent::__construct( $parent, $id, $title, $itype, $cbs );
		
		/**
		 * Automatic hook-up to known types of parent.
		 */
		if ( ( !$this->hooked ) && ( ( ( $parent instanceof buttons ) || ( $parent instanceof srchres ) ) || ( $parent instanceof buttons ) || ( $parent instanceof dummyres ) ) )
		{
			$parent->add( $this );
			$this->hooked = TRUE;
		}
		
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UICMP . 'gi.html';
		$this->class	= $class;
	}

	/**
	 * Read interface for item additional CSS style.
	 * @return string
	 */
	public function getClass ( ) { return $this->class; }

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function  generateReqs ( ) { }
}

?>