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
require_once CHASSIS_LIB . 'uicmp/uicmp.php';

/**
 * Component representing form item.
 */
class frmitem extends uicmp implements \_uicmp
{	
	/**
	 * Type of the form item. See class constants for values.
	 * 
	 * @var int 
	 */
	protected $itype = self::FIT_TEXT;
	
	/**
	 * Text displayed before the form element to indicate its purpose.
	 * 
	 * @var string 
	 */
	protected $prompt = NULL;
	
	/**
	 * Value of element. May be string, bool or other.
	 * 
	 * @var mixed 
	 */
	protected $value = NULL;
	
	/**
	 * Description of the input field. Displayed in petite under the element.
	 * 
	 * @var string 
	 */
	protected $desc = '';
	
	/**
	 * Associative array of Javascript code snippets to be executed for certain
	 * events. Event name is a key, e.g. 'onClick'.
	 * 
	 * @var array
	 */
	protected $cbs = NULL;
	
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
		parent::__construct( $parent, $parent->getId( ) . '.' . $id );
		
		/**
		 * Automatic hook-up to known types of parent.
		 */
		if ( ( !$this->hooked ) && ( $parent instanceof simplefrm ) )
		{
			$parent->add( $this );
			$this->hooked = TRUE;
		}
		
		$this->itype	= $type;
		$this->prompt	= $prompt;
		$this->value	= $value;
		$this->desc		= $desc;
		$this->cbs		= $cbs;
		
		$this->renderer	= CHASSIS_UI . 'uicmp/fi.html';
	}
	
	/**
	 * Getter for type of form item.
	 * 
	 * @return int 
	 */
	public function getIType ( ) { return $this->itype; }
	
	/**
	 * Getter for prompt string.
	 * 
	 * @return string 
	 */
	public function getPrompt ( ) { return $this->prompt; }
	
	/**
	 * Getter for value.
	 * 
	 * @return mixed 
	 */
	public function getValue ( ) { return $this->value; }
	
	/**
	 * Getter for description string.
	 * 
	 * @return string
	 */
	public function getDesc ( ) { return $this->desc; }
	
	/**
	 * Read interface for callbacks array.
	 * 
	 * @todo use iterator
	 * 
	 * @return array 
	 */
	public function getCbs ( ) { return $this->cbs; }
	
	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateReqs () { }
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
		
		$this->jsPrefix		= '_uicmp_frm_i:';
		$this->renderer		= CHASSIS_UI . 'uicmp/frm.html';
	}
}

?>