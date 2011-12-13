<?php

/**
 * @file simplefrm.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . 'uicmp/common.php';
require_once CHASSIS_LIB . 'uicmp/pool.php';
require_once CHASSIS_LIB . 'uicmp/item.php';

/**
 * Component representing form item.
 */
class frmitem extends item
{
	/**
	 * Description of the input field. Displayed in petite under the element.
	 * @var string 
	 */
	protected $desc = '';
	/**
	 * Constructor.
	 * 
	 * @param simplefrm $parent form component
	 * @param string $id id of the component, suffix to parent ID
	 * @param string $prompt prompt string
	 * @param mixed $value value of the item
	 * @param string $desc (optional) description of the item
	 * @param int $type (optional) type of the item
	 * @param array $cbs (optional) callbacks for HTML element events
	 */
	public function __construct( &$parent, $id, $prompt, $value, $desc = '', $type = self::FIT_TEXT, $cbs = NULL )
	{
		parent::__construct( $parent, $parent->getId( ) . '.' . $id, $prompt, $type, $cbs );
		
		/**
		 * Automatic hook-up to known types of parent.
		 */
		if ( ( !$this->hooked ) && ( $parent instanceof simplefrm ) )
		{
			$parent->add( $this );
			$this->hooked = TRUE;
		}
		$this->value	= $value;
		$this->desc		= $desc;
		$this->renderer	= CHASSIS_UI . 'uicmp/fi.html';
	}

	/**
	 * Getter for value.
	 * @return mixed 
	 */
	public function getValue ( ) { return $this->value; }
	
	/**
	 * Getter for description string.
	 * @return string
	 */
	public function getDesc ( ) { return $this->desc; }

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateReqs ( ) { }
}

/**
 * Component representing universal form body.
 */
class simplefrm extends pool
{
	/**
	 * Constructor.
	 * 
	 * @param _uicmp_body $parent parent component, the tab body
	 * @param string $id identifier of the component
	 */
	public function __construct( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		
		/**
		 * Automatic hook-up to known types of parent.
		 */
		if ( ( !$this->hooked ) && ( $parent instanceof body ) )
		{
			$parent->add( $this );
			$this->hooked = TRUE;
		}
		
		$this->jsPrefix		= '_uicmp_frm_i';
		$this->renderer		= CHASSIS_UI . 'uicmp/frm.html';
	}
}

?>