<?php

/**
 * @file _uicmp_fi.php
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'uicmp/_uicmp_comp.php';

/**
 * Component representing form item.
 * 
 * @todo onClick, onChange, onKey* event callbacks
 */
class _uicmp_fi extends _uicmp_comp
{
	/**
	 * Simple text input.
	 */
	const TEXT		= 0;
	
	/**
	 * Password field. Value for it is ignored.
	 */
	const PASSWORD	= 1;
	
	/**
	 * Checkbox field. Value is boolean. Description for it is ignored.
	 */
	const CHECKBOX	= 2;
	
	/**
	 * Multioption chooser. 
	 */
	const SELECT	= 3;
	
	/**
	 * Textarea.
	 */
	const TEXTAREA	= 4;
	
	/**
	 * Type of the form item. See class constants for values.
	 * 
	 * @var int 
	 */
	protected $type = self::TEXT;
	
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
	 * Constructor.
	 * 
	 * @param _uicmp_frm $parent form component
	 * @param string $id id of the component, suffix to parent ID
	 * @param string $prompt prompt string
	 * @param mixed $value value of the item
	 * @param string $desc description of the item
	 * @param int $type type of the item
	 */
	public function __construct( &$parent, $id, $prompt, $value, $desc = '', $type = self::TEXT )
	{
		parent::__construct( $parent, $parent->getId( ) . '.' . $id );
		$this->type		= $type;
		$this->prompt	= $prompt;
		$this->value	= $value;
		$this->desc		= $desc;
		
		$this->renderer	= CHASSIS_UI . 'uicmp/fi.html';
	}
	
	/**
	 * Getter for type of form item.
	 * 
	 * @return int 
	 */
	public function getType ( ) { return $this->type; }
	
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
}

?>