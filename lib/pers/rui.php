<?php

/**
 * @file rui.php
 * @author giorno
 * @package Chassis
 * @subpackage Persistence
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\pers;

require_once CHASSIS_LIB . 'uicmp/vcmp.php';
require_once CHASSIS_LIB . 'uicmp/tab.php';
require_once CHASSIS_LIB . 'uicmp/headline.php';

/**
 * Virtual UICMP component building persistence UI for record, in this case
 * editor instance.
 */
class rui extends \io\creat\chassis\uicmp\vcmp
{
	/**
	 * Parent Persistence instance.
	 * @var \io\creat\chassis\pers\instance 
	 */
	protected $pi = NULL;
	
	/**
	 * Created UICMP tab object.
	 * @var \io\creat\chassis\uicmp\tab
	 */
	protected $tab = NULL;
	
	/**
	 * Configuration array used for UI building.
	 * @var array
	 */
	protected $uicfg = NULL;
	
	/**
	 * Configuration array used for client side logic parametrization.
	 * @var array
	 */
	protected $jscfg = NULL;
	
	/**
	 * Constructor.
	 * 
	 * @param \io\creat\chassis\pers\instance $pi parent Persistence instance
	 * @param \io\creat\chassis\uicmp\layout $parent parent UICMP component (layout)
	 */
	public function __construct( $pi, $parent )
	{
		
		parent::__construct( $parent );
		$this->pi	= $pi;
		$fw_msg		= $this->parent->getMsgs( );
		$cust_msg	= $this->pi->msg( );
		$fields		= $this->pi->def( );

		$this->tab = new \io\creat\chassis\uicmp\tab( $this->parent, $this->pi->id( ) . '.Rui' );
			new \io\creat\chassis\uicmp\headline( $this->tab->getHead( ), $this->pi->id( ) . '.Hl', $cust_msg['rui']['edit'] );
			$buttons = new \io\creat\chassis\uicmp\buttons( $this->tab->getHead( ), $this->pi->id( ) . '.Buttons' );
			$buttons->add( $back = new \io\creat\chassis\uicmp\grpitem( $buttons, $this->pi->id( ) . '.Back', \io\creat\chassis\uicmp\grpitem::IT_A, $fw_msg['pers']['rui']['back'], $this->parent->getJsVar() . ".back();", '_uicmp_gi_back' ) );
			$buttons->add( new \io\creat\chassis\uicmp\grpitem( $buttons, $this->pi->id( ) . '.S1', \io\creat\chassis\uicmp\grpitem::IT_TXT, '|' ) );
			$buttons->add( new \io\creat\chassis\uicmp\grpitem( $buttons, $this->pi->id( ) . '.Save', \io\creat\chassis\uicmp\grpitem::IT_BT, $fw_msg['pers']['rui']['save'], $this->pi->jsVar( ) . ".rui.save( );" ) );
			$buttons->add( $indicator = new \io\creat\chassis\uicmp\indicator( $buttons, $this->pi->id( ) . '.Rui.Ind', \io\creat\chassis\uicmp\grpitem::IT_IND, $fw_msg['pers']['rui']['ind'] ) );

		$fields = $this->pi->def( );
		if ( is_array( $fields ) )
		{
			$form = new \io\creat\chassis\uicmp\simplefrm( $this->tab->getBody( ), $this->pi->id( ) . '.Frm' );
			
			// indexes are first, non modifiable fields
			$index = $this->pi->idx( );
			if ( is_array( $index ) )
				foreach( $index as $name )
				{
					$this->jscfg['idx'][$name] = 1;
					new \io\creat\chassis\uicmp\frmitem(	$form,
															'rui::' . $fields[$name]->name,
															$fields[$name]->title,
															'',
															'',
															\io\creat\chassis\uicmp\frmitem::FIT_ROTEXT );
				}
			
			// modifiable fields
			foreach( $fields as $field )
			{
				// indexes were already processed
				if ( in_array( $field->name, $index ) )
					continue;
				
				if ( $field->flags & field::FL_FD_MODIFY )
				{
					$this->jscfg['f'][$field->name]['d'] = ( ( $field->flags & field::FL_FO_DYNAMIC ) ? true : false );
					
					switch ( $field->type )
					{
						case field::FT_TAG:
							$this->jscfg['f'][$field->name]['t'] = 'tag';
							
							new \io\creat\chassis\uicmp\frmitem(	$form,
																	'rui::' . $field->name,
																	$field->title,
																	'',
																	'',
																	\io\creat\chassis\uicmp\frmitem::FIT_SELECT,
																	'cbs' );
						break;
					
						default:
							$this->jscfg['f'][$field->name]['t'] = 'string';
							new \io\creat\chassis\uicmp\frmitem(	$form,
																	'rui::' . $field->name,
																	$field->title,
																	'',
																	'',
																	\io\creat\chassis\uicmp\frmitem::FIT_TEXT,
																	'cbs' );
						break;
					}
				}
			}
		}
			
		$this->jscfg['tab_id'] = $this->tab->getHtmlId( );
		$this->jscfg['back_id'] = $back->getHtmlId( );
		$this->jscfg['ind'] = new \io\creat\chassis\uicmp\jsobj( $indicator->getJsVar( ) );
		$this->jscfg['frm_id'] = $form->getHtmlId( );
	}
	
	/**
	 * Provides HTML id of the tab component.
	 * @return string
	 */
	public function id ( ) { return $this->tab->getHtmlId( ); }
	
	/**
	 * Provides Javascript initialization literal for search configuration.
	 * @return string
	 */
	public function jsCfg ( ) { return \io\creat\chassis\uicmp\vcmp::toJsArray( $this->jscfg ); }
	
	/**
	 * Generate client side requirements for whole subtree of UICMP components.
	 */
	public function generateReqs ( ) { $this->tab->generateReqs( ); }
}

?>