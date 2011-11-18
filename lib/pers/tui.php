<?php

/**
 * @file tui.php
 * @author giorno
 * @package Chassis
 * @subpackage Persistence
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\pers;

require_once CHASSIS_LIB . 'uicmp/vcmp.php';
require_once CHASSIS_LIB . 'uicmp/vsearch.php';
require_once CHASSIS_LIB . 'uicmp/tab.php';
require_once CHASSIS_LIB . 'uicmp/headline.php';
require_once CHASSIS_LIB . 'uicmp/fold.php';

require_once CHASSIS_LIB . 'pers/tuifrm.php';

/**
 * Virtual UICMP component building persistence UI for table, in this case
 * advanced search frontend for the table.
 */
class tui extends \io\creat\chassis\uicmp\vcmp
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
		
		/**
		 * Build client UI and logic configuration.
		 */
		$this->jscfg['f'] = false;
		$saved = unserialize( $this->pi->settproxy( )->lcfg( ) );
		if ( $saved === false )
		{
			$this->uicfg['k'] = '';
			$this->jscfg['p'] = 1;
			$this->jscfg['o'] = '';
			$this->jscfg['d'] = 'ASC';
		}
		else
		{
			$this->uicfg['k'] = $saved['k'];
			$this->jscfg['p'] = (int)$saved['p'];
			$this->jscfg['o'] = $saved['o'];
			$this->jscfg['d'] = $saved['d'];
		}
		if ( $this->pi->has( \pers::FL_PI_AS ) )
		{
			$this->jscfg['as'] = false;
			$this->uicfg['as']['show'] = false;
			
			if ( is_array( $fields ) )
			{
				$this->jscfg['as'] = true;
				$this->uicfg['as']['show'] = ( $saved === false ) ? 'false' : $saved['as'];
				
				foreach ( $fields as $field )
				{
					/**
					 * Are we restricting by this field?
					 */
					if ( $field->flags & \pers::FL_FD_RESTRICT )
					{
						$restr = NULL;
						
						$restr['prompt'] = $field->title;
						$restr['dyn'] = ( $field->flags & \pers::FL_FO_DYNAMIC ) > 0;
						$restr['type'] = 'multi';			/** @todo implement also binary */
						if ( ( $saved === false ) || !array_key_exists( $field->name, $saved['r'] ) )
							$restr['selected'] =  '[norestr]';
						else
							$restr['selected'] =  $saved['r'][$field->name];
						
						$ops = $this->pi->restrictions( $field->name );
						if ( is_array( $ops ) )
							foreach ( $ops as $val => $title )
								$restr['option'][$val] = $title;
						else
							$restr['option']['[norestr]'] = $fw_msg['pers']['tui']['as']['norestr'];
						
						$this->uicfg['r'][$field->name] = $restr;
						$this->jscfg['r'][$field->name] = array(	'dyn' => ( ( $field->flags & \pers::FL_FO_DYNAMIC ) > 0 ),
																	'type' =>	( ( ( $field->flags & \pers::FL_FO_BINARY ) > 0 )
																				? 'bin'
																				: 'multi' ) );	// (default) rewrite for each new type
					}
					/**
					 * Are we searching in this field? It is mutually exclusive
					 * witch restrictions.
					 */
					elseif ( $field->flags & \pers::FL_FD_SEARCH )
					{
						/**
						 * Localize searching in field on first searchable
						 * field.
						 */
						if ( !array_key_exists( 'f', $this->uicfg ) )
						{
							$this->jscfg['f'] = true;
							$this->uicfg['f']['prompt'] = $fw_msg['pers']['tui']['as']['field'];
							$this->uicfg['f']['selected'] = ( $saved === false ) ? '[allfields]' : $saved['f'];
							$this->uicfg['f']['option']['[allfields]'] = $fw_msg['pers']['tui']['as']['allfields'];
						}
						$this->uicfg['f']['option'][$field->name] = $field->title;
					}
					
					
				}
			}
		}
		
		/**
		 * @todo Check if, by any accident, we did not end up with single field
		 * to search in, that would render UI elements irrelevant.
		 */
		
		$this->tab = new \io\creat\chassis\uicmp\tab( $this->parent, $this->pi->id( ) . '.Tui' );
			$this->tab->createFold( $cust_msg['tui']['fold'] );
			new \io\creat\chassis\uicmp\headline( $this->tab->getHead( ), $this->pi->id( ) . '.Hl', $cust_msg['tui']['headline'] );
			$form = new tuifrm( $this->tab->getHead( ), $this->pi->id( ) . '.Frm', $this->pi, $this->uicfg );
			$form->ind( new \io\creat\chassis\uicmp\indicator( $form, $this->pi->id( ) . '.Ind', '', $fw_msg['pers']['tui']['ind'] ) );
			$this->tab->getBody( )->add ( $container = new \io\creat\chassis\uicmp\srchcnt( $this->tab->getBody( ), $this->pi->id( ) . '.Results' ) );

		if ( $this->pi->has( \pers::FL_PI_RESIZE ) )
			$resizer = new \io\creat\chassis\uicmp\srchres( $this->tab->getBody( ), $this->pi->id( ) . '.Res', $this->pi->jsVar( ) . '.tui', $pi->settproxy( )->llen( ) );
		elseif ( $this->pi->has( \pers::FL_PI_ANCHORS ) )
			$resizer = new \io\creat\chassis\uicmp\dummyres( $this->tab->getBody( ), $this->pi->id( ) . '.Res' );
		
		// anchor in the resizer row
		if ( ( $this->pi->has( \pers::FL_PI_RUI ) ) && ( $this->pi->has( \pers::FL_PI_CREATE ) ) )
			new \io\creat\chassis\uicmp\grpitem( $resizer, $this->pi->id( ) . '.RuiAnchor', \io\creat\chassis\uicmp\grpitem::IT_A, $cust_msg['tui']['anchor'], $this->pi->jsVar( ) . '.rui.create( );', '_uicmp_gi_add' );
		
		$this->jscfg['frm_id'] = $form->getHtmlId( );
		$this->jscfg['ind'] = new \io\creat\chassis\uicmp\jsobj( $form->ind( )->getJsVar( ) );
		$this->jscfg['res_id'] = $resizer->getHtmlId( );
		$this->jscfg['cnt_id'] = $container->getHtmlId( );
	}
	
	/**
	 * Explicitly make tab component visible after HTML page load.
	 */
	public function show ( ) { $this->tab->show( ); }
	
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