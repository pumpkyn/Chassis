<?php

/**
 * @file strings.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */
namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . 'uicmp/uicmp.php';

/**
 * Provides serialized array of strings for client side UI to be parsed by
 * client side logic. These data could be easily distributed in <HEAD> section
 * as ordinary plain Javascript static initialization code or by linking it, but
 * this solution does not require extra request (optimization) and bundles data
 * into appropriate UICMP component instance UI code (maintainability).
 */
class strings extends uicmp
{
	/**
	 * Multidimensional associative array of strings.
	 * 
	 * @var array
	 */
	protected $messages = NULL;
	
	/**
	 * Constructor.
	 * 
	 * @param uicmp $parent parent component, used to conform UICMP concept
	 * @param string $id identifier of component
	 * @param array $messages strings to be provided
	 */
	public function __construct ( &$parent, $id, &$messages )
	{
		parent::__construct( $parent , $id );
		$this->renderer	= CHASSIS_UI . 'uicmp/strings.html';
		$this->messages	= $messages;
	}
	
	/**
	 * Provides internal data as plain structure interpretable by Javascript
	 * eval() function.
	 * 
	 * @return string 
	 */
	public function get ( ) { return self::toJsArray( $this->messages ); }
	
	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateReqs ( ) { }
}

?>