<?php

/**
 * @file preview.php
 * @author giorno
 * @package Chassis
 * @subpackage Tags
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\tags;

require_once CHASSIS_LIB . 'uicmp/simplefrm.php';

/**
 * Component representing form item.
 */
class preview extends \io\creat\chassis\uicmp\frmitem
{	
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
	public function __construct( &$parent, $id, $prompt  )
	{
		parent::__construct( $parent, $id, $prompt, '' );
		$this->renderer	= CHASSIS_UI . 'tags/preview.html';
	}
	
	/**
	 * Provides dummy badge for preview.
	 * @return \_ctx 
	 */
	public function getCtx ( ) { return new \_ctx( $this->id . '.Preview' , 'dar', 'dar' ); }
}

?>