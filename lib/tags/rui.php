<?php

/**
 * @file rui.php
 * @author giorno
 * @package Chassis
 * @subpackage Tags
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\tags;

require_once CHASSIS_LIB . 'pers/rui.php';
require_once CHASSIS_LIB . 'tags/preview.php';

/**
 * Specialization of standard Persistence RUI to allow use of specialized
 * components.
 */
class rui extends \io\creat\chassis\pers\rui
{
	/**
	 * Constructor. Adding custom localization for client 
	 * @param \io\creat\chassis\pers\instance $pi parent Persistence instance
	 * @param \io\creat\chassis\uicmp\layout $parent parent UICMP component (layout)
	 */
	public function __construct( $pi, $parent ) { parent::__construct( $pi, $parent ); }
	
	/**
	 * Overriding superclass to plant preview field into the edit form.
	 * @param \io\creat\chassis\uicmp\simplefrm $form reference to UICMP form
	 * @param \io\creat\chassis\pers\field $field field instance to plant
	 * @param array $index array of indexes
	 * @return mixed 
	 */
	protected function item ( &$form, &$field, &$index )
	{
		// we need to create preview badge instead of simple form item
		if ( $field->name == \tags::FN_ID )
		{
			$cust_msg = $this->pi->msg( );
			$preview = new preview( $form, $field->name, $cust_msg['preview'] );
			$this->jscfg['preview_id'] = $preview->getCtx( )->getHtmlId( );
		}
		else
			return parent::item ( $form, $field, $index );
	}
}

?>