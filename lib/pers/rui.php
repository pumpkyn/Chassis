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
	 * Post build up Javascript code. Used to register resizable textarea
	 * related code.
	 * @var string
	 */
	protected $postJs = NULL;
	
	/**
	 * Constructor.
	 * @param \io\creat\chassis\pers\instance $pi parent Persistence instance
	 * @param \io\creat\chassis\uicmp\layout $parent parent UICMP component (layout)
	 */
	public function __construct( $pi, $parent )
	{
		parent::__construct( $parent );
		$this->pi	= $pi;
		$this->build( );
	}
	
	/**
	 * Builder method creating the UICMP for the RUI. Can be overridden in the
	 * subclass to provide different behaviour or UI.
	 */
	protected function build ( )
	{
		$fw_msg		= $this->parent->getMsgs( );
		$cust_msg	= $this->pi->msg( );
		$fields		= $this->pi->def( );

		// Incorporate user code localization.
		if ( array_key_exists( 'i', $cust_msg['rui'] ) )
			$imsg = array_merge( $fw_msg['pers']['rui']['ind'], $cust_msg['rui']['i'] );
		elseif ( array_key_exists( 'ind', $cust_msg['rui'] ) )
			$imsg = array_merge( $fw_msg['pers']['rui']['ind'], $cust_msg['rui']['ind'] );
		else
			$imsg = $fw_msg['pers']['rui']['ind'];
				
		$this->jscfg['loc']['edit'] = $cust_msg['rui']['edit'];
		$this->jscfg['loc']['create'] = $cust_msg['rui']['create'];
		
		$this->tab = new \io\creat\chassis\uicmp\tab( $this->parent, $this->pi->id( ) . '.Rui' );
			$headline = new \io\creat\chassis\uicmp\headline( $this->tab->getHead( ), $this->pi->id( ) . '.RuiHl', $cust_msg['rui']['edit'] );
			$buttons = new \io\creat\chassis\uicmp\buttons( $this->tab->getHead( ), $this->pi->id( ) . '.Buttons' );
			$buttons->add( $back = new \io\creat\chassis\uicmp\grpitem( $buttons, $this->pi->id( ) . '.Back', \io\creat\chassis\uicmp\grpitem::IT_A, $fw_msg['pers']['rui']['back'], $this->parent->getJsVar() . ".back();", '_uicmp_gi_back' ) );
			$buttons->add( new \io\creat\chassis\uicmp\grpitem( $buttons, $this->pi->id( ) . '.S1', \io\creat\chassis\uicmp\grpitem::IT_TXT, '|' ) );
			$buttons->add( new \io\creat\chassis\uicmp\grpitem( $buttons, $this->pi->id( ) . '.Save', \io\creat\chassis\uicmp\grpitem::IT_BT, $fw_msg['pers']['rui']['save'], $this->pi->jsVar( ) . ".rui.save( );" ) );
			$buttons->add( $indicator = new \io\creat\chassis\uicmp\indicator( $buttons, $this->pi->id( ) . '.Rui.Ind', \io\creat\chassis\uicmp\grpitem::IT_IND, $imsg ) );

		$this->jscfg['frmhl_id'] = $headline->getHtmlId( );
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
					
					// do not display hidden fields
					if ( $fields[$name]->flags & field::FL_FD_HIDDEN )
						continue;
					
					new \io\creat\chassis\uicmp\frmitem(	$form,
															'rui::' . $fields[$name]->name,
															$fields[$name]->title,
															'',
															'',
															\io\creat\chassis\uicmp\frmitem::FIT_ROTEXT );
				}
			
			// modifiable fields
			$map = $this->pi->map( );
			if ( is_array( $map ) )
				foreach( $map as $name )
					$this->item( $form, $fields[$name], $index );
			else
				foreach( $fields as $field )
					$this->item( $form, $field, $index );
		}
			
		$this->jscfg['tab_id'] = $this->tab->getHtmlId( );
		$this->jscfg['back_id'] = $back->getHtmlId( );
		$this->jscfg['ind'] = new \io\creat\chassis\uicmp\jsobj( $indicator->getJsVar( ) );
		$this->jscfg['frm_id'] = $form->getHtmlId( );
	}
	
	/**
	 * Creates UI component and populates configuration structures for single
	 * item.
	 * @param \io\creat\chassis\uicmp\simplefrm $form reference to the form instance
	 * @param \io\creat\chassis\pers\field $field reference to the table field configuration
	 * @param array $index array of index fields
	 * @return \io\creat\chassis\uicmp\frmitem 
	 */
	protected function item ( &$form, &$field, &$index )
	{
		// indexes were already processed
		if ( in_array( $field->name, $index ) )
			return;
				
		if ( ( $field->flags & field::FL_FD_MODIFY ) || !( $field->flags & field::FL_FD_HIDDEN ) )
		{
			// Disable changes in edit mode.
			$this->jscfg['f'][$field->name]['c'] = ( ( $field->flags & field::FL_FD_CONST ) ? true : false );
			
			$this->jscfg['f'][$field->name]['d'] = ( ( $field->opts->flags & field::FL_FO_DYNAMIC ) ? true : false );
			$this->jscfg['f'][$field->name]['e'] = ( ( $field->opts->flags & field::FL_FO_NE ) ? false : true );
			$this->jscfg['f'][$field->name]['m'] = false;
			
			if ( $field->flags & field::FL_FD_PREVIEW )
				$this->jscfg['preview'] = $field->name;
			
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
															( ( $field->flags & field::FL_FD_PREVIEW ) ? array( 'onChange' => $this->pi->jsVar( ) . '.rui.preview( \'' . $field->name . '\' );' ) : NULL ) );
				break;
			
				case field::FT_ENUM:
					$this->jscfg['f'][$field->name]['t'] = 'enum';
					$fi = new \io\creat\chassis\uicmp\frmitem(	$form,
																'rui::' . $field->name,
																$field->title,
																'',
																'',
																\io\creat\chassis\uicmp\frmitem::FIT_SELECT,
																( ( $field->flags & field::FL_FD_PREVIEW ) ? array( 'onChange' => $this->pi->jsVar( ) . '.rui.preview( \'' . $field->name . '\' );' ) : NULL ) );
					$fi->setOptions( $field->opts->values );
				break;
			
				case field::FT_BOOL:
					$this->jscfg['f'][$field->name]['t'] = 'bool';
					$fi = new \io\creat\chassis\uicmp\frmitem(	$form,
																'rui::' . $field->name,
																$field->title,
																'',
																'',
																\io\creat\chassis\uicmp\frmitem::FIT_CHECKBOX,
																( ( $field->flags & field::FL_FD_PREVIEW ) ? array( 'onChange' => $this->pi->jsVar( ) . '.rui.preview( \'' . $field->name . '\' );' ) : NULL ) );
					$fi->setOptions( $field->opts->values );
				break;
			
				case field::FT_DATESTAMP:
					$this->jscfg['f'][$field->name]['t'] = 'datestamp';
					$fi = new \io\creat\chassis\uicmp\frmitem(	$form,
																'rui::' . $field->name,
																$field->title,
																'',
																'',
																\io\creat\chassis\uicmp\frmitem::FIT_DATE );
					//$fi->setOptions( $field->opts->values );
				break;
					
				case field::FT_PASSWORD:
					$this->jscfg['f'][$field->name]['t'] = 'password';
					new \io\creat\chassis\uicmp\frmitem(	$form,
															'rui::' . $field->name,
															$field->title,
															'',
															'',
															\io\creat\chassis\uicmp\frmitem::FIT_PASSWORD,
															( ( $field->flags & field::FL_FD_PREVIEW ) ? array( 'onKeyUp' => $this->pi->jsVar( ) . '.rui.preview( \'' . $field->name . '\' );' ) : NULL ) );
				break;
			
				case field::FT_STRING:
				case field::FT_COMMENT:
				default:
					$this->jscfg['f'][$field->name]['t'] = 'string';

					$fi = new \io\creat\chassis\uicmp\frmitem(	$form,
															'rui::' . $field->name,
															$field->title,
															'',
															'',
															( $field->type == field::FT_COMMENT ) ? \io\creat\chassis\uicmp\frmitem::FIT_TEXTAREA : \io\creat\chassis\uicmp\frmitem::FIT_TEXT,
															( ( $field->flags & field::FL_FD_PREVIEW )
																? array( 'onKeyUp' => $this->pi->jsVar( ) . '.rui.preview( \'' . $field->name . '\' );' )
																: NULL ) );
					
					
					
					// required to enforce client logic to make textarea resizable
					if ( $field->type == field::FT_COMMENT )
					{
						$this->jscfg['f'][$field->name]['m'] = true;
						$fi->setOption( 'tah', $this->pi->settproxy( )->tah( $this->pi->name( ), $field->name ) );
					}
				break;
			}
		}
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
	 * Returns this instance's Javascript post initialization.
	 * @return array
	 */
	public function getPostJs ( ) { return $this->postJs; }
	
	/**
	 * Generate client side requirements for whole subtree of UICMP components.
	 */
	public function generateReqs ( )
	{
		if ( !is_null( $this->tab ) ) $this->tab->generateReqs( );
		
		/*if ( is_array( $this->postJs ) )
		//if ( ( $this->itype == self::FIT_TEXTAREA ) && is_array( $this->cbs ) )
		{
			$requirer = $this->parent->getRequirer( );

			if ( !is_null( $requirer ) )
			{
				$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( 'inc/chassis/3rd/textarearesizer.js' , __CLASS__ ) );
				$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_CSS, array( 'inc/chassis/3rd/textarearesizer.css' , __CLASS__ ) );
				
				foreach ( $this->postJs as $js )
					$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, $js );
			}
		}*/
	}
}

?>