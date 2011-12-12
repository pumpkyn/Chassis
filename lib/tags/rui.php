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
require_once CHASSIS_LIB . 'uicmp/simplefrm.php';

/**
 * Special form item rendering select box and scheme cloud to set tag color
 * scheme. An UICMP component.
 */
class scheme extends \io\creat\chassis\uicmp\frmitem
{
	/**
	 * Array of color schemes (_ctx instances).
	 * @var array
	 */
	protected $schemes = NULL;
	
	/**
	 * Reference to Persistance instance.
	 * @var \io\creat\chassis\tags\instance
	 */
	protected $pi = NULL;
	
	/**
	 * Constructor.
	 * 
	 * @param \io\creat\chassis\uicmp\simplefrm $parent parent form
	 * @param string $id component ID (not derived from form's ID)
	 * @param \io\creat\chassis\tags\instance $pi Persistence instance used for extraction of additional data
	 * @param string $title display title of the field
	 * @param array $schemes schemes to display and pick
	 * @param string $cbs Javascript callbacks relayed to the parent constructor
	 */
	public function __construct ( &$parent, $id, $pi, $title, $schemes, $cbs )
	{
		parent::__construct( $parent, $id, $title, "", "", \_uicmp::FIT_SELECT, $cbs );
		$this->renderer	= CHASSIS_UI . 'tags/scheme.html';
		$this->schemes	= $schemes;
		$this->jsVar	= $pi->jsVar( );
		$this->pi		= $pi;
	}
	
	/**
	 * Getter for schemes.
	 * @return array
	 */
	public function getSchemes ( ) { return $this->schemes; }
	
	/**
	 * Provides setting of palette visibility status.
	 * @return boolean
	 */
	public function isPaletteShown ( ) { return ( $this->pi->settproxy()->getl( 'usr.palette.shown.' . $this->pi->name( ) ) == 'true' ); }
}

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
		switch ( $field->name )
		{
			case \tags::FN_ID:
				$cust_msg = $this->pi->msg( );
				$preview = new preview( $form, $field->name, $cust_msg['preview'] );
				$this->jscfg['preview_id'] = $preview->getCtx( )->getHtmlId( );
			break;
		
			case \tags::FN_SCHEME:
				$this->jscfg['f'][$field->name]['t'] = 'enum';
				
				$schemes = NULL;
				//var_dump($field->opts->values);
				foreach( $field->opts->values as $key => $val )
					$schemes[$key] = new \_ctx ( 'rui::' . $field->name . "::" . $key, $key, $val, '', $this->pi->jsVar( ) . '.rui.sch_set(\'' . $key . '\')' );

				$fi = new scheme(	$form,
									'rui::' . $field->name,
									$this->pi,
									$field->title,
									$schemes,
									( ( $field->flags & \pers::FL_FD_PREVIEW ) ? array( 'onChange' => $this->pi->jsVar( ) . '.rui.preview( \'' . $field->name . '\' );' ) : NULL ) );
				$fi->setOptions( $field->opts->values );
			break;
		
			default:
				return parent::item( $form, $field, $index );
			break;
		}
	}
}

?>