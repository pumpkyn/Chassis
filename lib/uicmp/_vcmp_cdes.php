<?php

/**
 * @file _vcmp_cdes.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * Virtual component for creating Context Displaying and Editing Solutions
 * (CDES). This comprises creation of UICMP (1) tab with search solution for
 * contexts and (2) context editor. Another part of framework should provide
 * Ajax server side processing implementation and it is responsibility of
 * application to arrange proper configuration of the component and its Ajax
 * channel.
 */

require_once CHASSIS_LIB . 'uicmp/_vcmp_comp.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_cdes_editor.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_buttons.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_gi.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_gi_ind.php';

class _vcmp_cdes extends _vcmp_comp
{
	/**
	 * Identifier of the component. Used to generate HTML ID's for the client
	 * side.
	 * 
	 * @var <string> 
	 */
	protected $id = NULL;

	/**
	 * Associative array holding specialized localization strings for CDES. So
	 * far these keys from this array are used (rest of messages is taken from
	 * framework localization files):
	 *
	 * 'cdesFold'	...	for table fold title
	 * 'cdesTitle'	...	for table main caption
	 *
	 * @var <array>
	 */
	protected $cust = NULL;

	/**
	 * URL of Ajax server implementation. Used for initialization of client side
	 * structured.
	 *
	 * @var <string>
	 */
	protected $url = NULL;

	/**
	 * Additional parameters for Ajax requests. Associative array, which should
	 * have these keys:
	 *
	 * 'app'	... application identifier (to trigger proper Ajax channel)
	 * 'action'	... action identifier (to trigger proper action within the channel)
	 *
	 * @var <array>
	 */
	protected $params = NULL;

	/**
	 * Initial configuration for search solution. Used for populating HTML
	 * elements values and initialization of client side Javascript. Key should
	 * be the same as for _vcmp_search component.
	 *
	 * @var <array>
	 */
	protected $config = NULL;

	/**
	 * Initial size for _uicmp_resizer objects.
	 *
	 * @var <int>
	 */
	protected $size = NULL;

	/**
	 * Editor tab component.
	 *
	 * @var <_uicmp_tab>
	 */
	protected $tab = NULL;

	/**
	 * Indicator component for the editor form.
	 *
	 * @var <_uicmp_gi_ind>
	 */
	protected $ind = NULL;

	/**
	 * Instance of CDES editor component.
	 *
	 * @var <_uicmp_cdes_editor>
	 */
	protected $editor = NULL;

	/**
	 * Contructor.
	 *
	 * @param <_uicmp_layout> $parent reference to parent widget
	 * @param <string> $id identifier of the component
	 * @param <array> $cust customized localizationf or tab fold and caption
	 * @param <string> $url URL of Ajax server
	 * @param <array> $params parameters for Ajax request
	 * @param <_list_cfg> $list_cfg list configuration (for search solution)
	 * @param <int> $resizer_size size for resizer instance (in search solution)
	 */
	public function __construct ( &$parent, $id, $cust, $url, $params, $list_cfg, $resizer_size )
	{
		parent::__construct( $parent );
		$this->id		= $id;
		$this->cust		= $cust;
		$this->url		= $url;
		$this->params	= $params;
		$this->config	= $list_cfg->get( );
		$this->size		= $resizer_size;

		/**
		 * Create editor tab.
		 */
		$this->tab = $this->parent->createTab( $this->id . '.Edit' );
		$this->tab->unstack( );

		/**
		 * This is important for client side of the search solution to provide
		 * editor instance variable name. This is part of Ajax request
		 * parameters so it get transported to the server and proper Javascript
		 * code can be attached to list entries (contexts) in context searching
		 * backend.
		 *
		 * There is not need for similar parameter for search instance id as for
		 * returning from the editor the _uicmp_tab_stack implementation is
		 * used.
		 */
		$this->editor = new _uicmp_cdes_editor( $this->tab, $this->tab->getId( ) . '.Form', $this->url, $this->params );
		$this->params['cdes_ed']	= $this->editor->getJsVar( );

		/**
		 * Needed to build UI.
		 */
		$messages = $this->parent->getMessages( );

			$this->tab->getBody( )->add( $this->editor );

			/**
			 * This must be first item in head as its HTML Id is used for CDES
			 * form client instance.
			 */
			$this->tab->getHead( )->add( new _uicmp_title( $this->tab, $this->tab->getId( ) . '.Title', $messages['cdesCaption'] ) );

			/**
			 * Buttons in head section.
			 */
			$buttons = new _uicmp_buttons( $this->tab->getHead( ), $this->tab->getHead( )->getId( ) . '.Buttons' );
				$buttons->add( new _uicmp_gi( $buttons, $buttons->getId( ) . '.Back', _uicmp_gi::IT_A, $messages['formBtBack'], $this->parent->getJsVar( ) . '.back( );', '_uicmp_gi_back' ) );
				$buttons->add( new _uicmp_gi( $buttons, $buttons->getId( ) . '.S1', _uicmp_gi::IT_TXT, '|' ) );
				$buttons->add( new _uicmp_gi( $buttons, $buttons->getId( ) . '.Save', _uicmp_gi::IT_BT, $messages['formBtSave'], $this->editor->getJsVar() . '.save();' ) );
				//$buttons->add( new _uicmp_gi( $buttons, $buttons->getId( ) . '.S2', _uicmp_gi::IT_TXT, '|' ) );
				$this->ind = new _uicmp_gi_ind( $buttons, $buttons->getId( ) . '.Ind', _uicmp_gi::IT_IND, $messages['cdes'] );
					$buttons->add( $this->ind );
				$this->tab->getHead( )->add( $buttons );

			

		/**
		 * Create search tab.
		 */
		$sTab = $this->parent->createTab( $this->id . '.Display' );
			$sTab->createFold( $this->cust['cdesFold'] );
			$sTab->getHead( )->add( new _uicmp_title( $sTab, $sTab->getId( ) . '.Title', $this->cust['cdesTitle'] ) );
			$search = new _vcmp_search( $sTab->getId( ) . '.Search', $sTab, 0, $this->url, $this->params, $list_cfg, $this->size );

				$resizer = $search->getResizer( );
				if ( $resizer )
					$resizer->add( new _uicmp_gi( $resizer, $resizer->getId( ), _uicmp_gi::IT_A, $messages['cdesCreateContext'], $this->editor->getJsVar( ) . '.create( );', '_uicmp_gi_add' ) );

			$sTab->addVcmp( $search );
			


		/**
		 * Register VCMP to layout instance.
		 */
		$this->parent->addVcmp( $this );
	}

	/**
	 * Registers Javascript code for client side.
	 */
	public function  generateJs ( )
	{
		$requirer = $this->parent->getRequirer( );

		if ( !is_null( $requirer ) )
		{
			$requirer->call( _uicmp_layout::RES_JSPLAIN, 'var ' . $this->editor->getJsVar( ) . ' = new _uicmp_cdes_editor( \'' . $this->editor->getHtmlId( ) . '\', '. $this->parent->getJsVar( ) .', \''. $this->tab->getHtmlId( ) .'\', \''. $this->tab->getHead( )->getFirst( )->getHtmlId( ) .'\', \''. $this->editor->getCtx()->getHtmlId( ) .'\', '. $this->ind->getJsVar( ) . ', \''. $this->url . '\', ' . $this->generateJsArray( $this->params ) . ' );' );
			$requirer->call( _uicmp_layout::RES_JSPLAIN, $this->parent->getJsVar( ) . '.registerTabCb( \'' . $this->tab->getHtmlId( ) . '\', \'onShow\', ' . $this->editor->getJsVar( ) . '.reset );' );
		}
	}

}

?>