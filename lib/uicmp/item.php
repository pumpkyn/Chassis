<?php

/**
 * @file item.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . 'uicmp/uicmp.php';
require_once CHASSIS_LIB . 'uicmp/common.php';

/**
 * Abstract UICMP component encapsulating basic state and behaviour of any
 * input item/field in any form like superstructure. This is intended to provide
 * universal parent to all specializations later used in components like forms
 * resizer rows or button groups. Subclasses must provide their own renderer,
 * otherwise Smarty processing will fail with error.
 */
abstract class item extends uicmp implements \_uicmp
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
	protected $title = NULL;

	/**
	 * Associative array of Javascript code snippets to be executed for certain
	 * events. Event name is a key, e.g. 'onClick'.
	 * 
	 * @var array
	 */
	protected $cbs = NULL;
	
	/**
	 * Associative array of SELECT item values or special values for other
	 * imputs: textarea renderer uses 'tah' for its height.
	 * 
	 * @var array
	 */
	protected $options = NULL;
	
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
	public function __construct( &$parent, $id, $prompt, /*$value, $desc = '', */$type = self::FIT_TEXT, $cbs = NULL )
	{
		parent::__construct( $parent, $id );
		$this->itype	= $type;
		$this->title	= $prompt;
		$this->cbs		= $cbs;
	}
	
	/**
	 * Adds new option for SELECT type item or custom option for any other type
	 * of form item. This is also used for delivery of special properties to
	 * UI template (e.g. textarea height).
	 * @param string $value value for OPTION element
	 * @param string $display text to display
	 */
	public function setOption ( $value, $display ) { $this->options[$value] = $display; }
	
	/**
	 * Batch load of key-value pairs.
	 * @param array $options associative array to replace member variable
	 */
	public function setOptions( &$options ) { $this->options = $options; }
	
	/**
	 * Getter for type of form item.
	 * @return int 
	 */
	public function getIType ( ) { return $this->itype; }
	
	/**
	 * Getter for prompt string.
	 * @return string 
	 */
	public function getPrompt ( ) { return $this->title; }
	
	/**
	 * Read interface for callbacks array.
	 * @todo use iterator
	 * @return array 
	 */
	public function getCbs ( ) { return $this->cbs; }
	
	/**
	 * Read interface for custom options (beside the SELECT box).
	 * @return mixed
	 */
	public function getOption ( $key ) { return $this->options[$key]; }
	
	/**
	 * Read interface for SELECT box options array.
	 * @return array
	 */
	public function getOptions ( ) { return $this->options; }

}

?>