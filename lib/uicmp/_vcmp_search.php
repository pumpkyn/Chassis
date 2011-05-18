<?php

/**
 * @file _vcmp_search.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * Virtual component to create and connect components used for searching. This
 * does not implement any common UICMP interface.
 *
 * Visual search elements are search form, container for result list and
 * list resizer.
 */

require_once CHASSIS_LIB . 'uicmp/_vcmp_comp.php';

require_once CHASSIS_LIB . 'uicmp/_uicmp_srch_frm.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_srch_res.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_srch_cnt.php';

class _vcmp_search extends _vcmp_comp
{
	/**
	 * Resizer shall not be rendered and used in the solution.
	 */
	const FLAG_NORESIZER = 1;

	/**
	 * Identification string. Used e.g. for pairing in Ajax server implementation.
	 * 
	 * @var <string> 
	 */
	protected $id = NULL;

	/**
	 * Reference to layout component.
	 * 
	 * @var <_uicmp_layout> 
	 */
	protected $layout = NULL;

	/**
	 * Requirer instance for resources loading.
	 * 
	 * @var <Requirer>
	 */
	protected $requirer = NULL;

	/**
	 * Reference to instance of _uicmp_tab providing UI.
	 *
	 * @var <_uicmp_tab>
	 */
	protected $tab = NULL;

	/**
	 * Reference to form component.
	 *
	 * @var <_uicmp_srch_frm>
	 */
	protected $form = NULL;

	/**
	 * Reference to target area in the document for search results.
	 *
	 * @var <_uicmp_container>
	 */
	protected $container = NULL;

	/**
	 * Resizer component.
	 *
	 * @var <_uicmp_resizer>
	 */
	protected $resizer = NULL;

	/**
	 * Indicator component for the search form.
	 *
	 * @var <_uicmp_gi_ind>
	 */
	protected $ind = NULL;

	/**
	 * Search URL. Search parameters are appended to it when search is invoked
	 * from Javascript.
	 * 
	 * @var <string>
	 */
	protected $url = NULL;

	/**
	 * Specific parameters for Ajax requests. Associative array, keys are
	 * parameter names.
	 *
	 * @var <array>
	 */
	protected $params = NULL;

	/**
	 * Array containing search form configuration (keywords, page, order, etc.).
	 * 
	 * @var <array>
	 */
	protected $config = NULL;

	/**
	 * Size for resizers.
	 * 
	 * @var <int>
	 */
	protected $size = NULL;

	/**
	 * Flag describing if Javascript variable for resizer size was set.
	 *
	 * @var <bool>
	 */
	protected $jsSizeSet = false;

	/**
	 * Constructor.
	 *
	 * @param <string> $id identifier for the solution
	 * @param <_uicmp_tab> $tab reference to _uicmp_tab parent instance
	 * @param <int> $url flags, for most of applications it is set to 0
	 * @param <string> $url base URL string for Ajax requests
	 * @param <array> $params additional parameters for Ajax requests
	 * @param <_list_cfg> $list_cfg list configuration for search instance
	 * @param <int> $resizer_size rendered size for resizer, negative value means resizer widget should not be rendered
	 */
	public function __construct( $id, &$tab, $flags, $url, $params, $list_cfg, $resizer_size = 20 )
	{
		parent::__construct( $tab );
		$this->id		= $id;
		$this->tab		= $tab;
		$this->flags	= $flags;
		$this->url		= $url;
		$this->params	= $params;
		$this->config	= $list_cfg->get( );
		$this->layout	= $this->tab->getParent( );
		$this->requirer	= $this->layout->getRequirer( );
		$this->size		= $resizer_size;
		$this->jsPrefix	= '_uicmp_search_i_';

		/**
		 * Setting my own client instance variable so Ajax server can call my
		 * other half.
		 */
		$this->params['js_var']	= $this->getJsVar();

		$head = $this->tab->getHead( );
		$body = $this->tab->getBody( );

		/**
		 * For indicator states messages.
		 */
		$messages = $this->layout->getMessages( );

		$this->form = new _uicmp_srch_frm( $head, $this->id . '.Form', $this->getJsVar( ), ( ( is_array( $this->config ) ) ? $this->config['k'] : '' ) );
			$this->ind = new _uicmp_gi_ind( $this->form, $this->form->getId( ) . '.Ind', _uicmp_gi::IT_IND, $messages['srch'] );
			$this->form->add( $this->ind );
		$this->container = new _uicmp_srch_cnt( $body, $this->id . '.Results' );

		$head->add( $this->form );
		$body->add( $this->container );

		if ( !$this->isFlagged( self::FLAG_NORESIZER ) )
		{
			$this->resizer = new _uicmp_srch_res( $body, $this->id . '.Resizer', $this->getJsVar( ), $this->size );
			$body->add( $this->resizer );
		}
		
		
	}

	/**
	 * Returns reference to resizer component.
	 *
	 * @return <_uicmp_srch_res>
	 */
	public function getResizer ( ) { return $this->resizer; }

	/**
	 * Generates Javascript content for <head> element to initialize solution.
	 */
	public function generateJs( )
	{
		/**
		 * Initialize client side.
		 */
		$this->requirer->call( _uicmp_layout::RES_CSS,	array( $this->requirer->getRelative( ) . 'css/_list.css', __CLASS__ ) );
		$this->requirer->call( _uicmp_layout::RES_JSPLAIN, 'var ' . $this->getJsVar( ) . ' = new _uicmp_search( \'' . $this->id . '\', \'' . $this->tab->getHtmlId( ) . '\', '. $this->ind->getJsVar( ) . ', \'' . $this->url . '\', ' . $this->generateJsArray( $this->params ) . ', ' . $this->generateJsArray( $this->config ) . ', \'' . $this->form->getHtmlId( ) . '\', \'' . $this->container->getHtmlId( ) . '\', ' . ( ( !$this->isFlagged( self::FLAG_NORESIZER ) ) ? '\'' . $this->resizer->getHtmlId( ) . '\'' : 'null' ) . ' );' );
		$this->requirer->call( _uicmp_layout::RES_JSPLAIN, $this->layout->getJsVar( ) . '.registerTabCb( \'' . $this->tab->getHtmlId( ) . '\', \'onShow\', ' . $this->getJsVar( ) . '.tabShown );' );
		$this->requirer->call( _uicmp_layout::RES_JSPLAIN, $this->layout->getJsVar( ) . '.registerTabCb( \'' . $this->tab->getHtmlId( ) . '\', \'onLoad\', ' . $this->getJsVar( ) . '.startup );' );

		$this->setJsSize( );
	}

	/**
	 * Sets size for resizer value cache variable.
	 */
	protected function setJsSize ( )
	{
		/**
		 * To make sure Javascript variable for resizing has correct value.
		 */
		if ( $this->jsSizeSet === false )
		{
			$this->requirer->call( _uicmp_layout::RES_JSPLAIN, '_uicmp_resizer_size = ' . $this->size . ';' );
			$this->jsSizeSet = true;
		}
	}

}

?>