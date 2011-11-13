<?php

/**
 * @file vsearch.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . 'uicmp/indicator.php';
require_once CHASSIS_LIB . 'uicmp/pool.php';
require_once CHASSIS_LIB . 'uicmp/vcmp.php';
require_once CHASSIS_LIB . 'uicmp/uicmp.php';
/*require_once CHASSIS_LIB . 'uicmp/_uicmp_srch_frm.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_srch_res.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_srch_cnt.php';*/

/**
 * Simple form for searching by single keyword. It is derived from _uicmp_pool
 * component due to its need to use _uicmp_gi_ind and separator for
 * indicator of outgoing Ajax request. Part of search solution.
 */
class srchfrm extends pool
{
	/**
	 * Prefill keywords.
	 * 
	 * @var string
	 */
	protected $keywords = NULL;

	/**
	 * Constructor.
	 *
	 * @param head $parent reference to parent component instance
	 * @param string $id identifier of the component
	 * @param string $js_var name of Javascript variable, created by vsearch to control search operation
	 * @param string $keywords text to populate search field
	 */
	public function __construct( &$parent, $id, $js_var, $keywords )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'uicmp/search_form.html';
		$this->jsVar	= $js_var;
		$this->keywords	= $keywords;
	}

	/**
	 * Returns keywords to prefill form.
	 *
	 * @return string
	 */
	public function getKeywords( ) { return $this->keywords; }
}

/**
 * Container for search results. Part of search solution.
 */
class srchcnt extends uicmp
{
	/**
	 * Constructor.
	 *
	 * @param body $parent reference to parent component instance
	 * @param string $id identifier of the component
	 */
	public function __construct ( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'uicmp/search_container.html';
	}

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateReqs ( ) { }
}

/**
 * UICMP component for search list resizer. This component is mainly used for
 * resizing lists with search results, but it can contain additional items. Part
 * of search solution.
 */
class srchres extends pool
{
	/**
	 * Actual size of page.
	 *
	 * @var int
	 */
	protected $sizer = 10;

	/**
	 * Constructor.
	 *
	 * @param body $parent reference to parent component instance
	 * @param string $id identifier of the component
	 * @param string $js_var name of client side Javascript instance
	 * @param int $size current size
	 */
	public function __construct ( &$parent, $id, $js_var, $size )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'uicmp/search_resizer.html';
		$this->jsVar	= $js_var;
		$this->size		= $size;
		
		if ( $parent instanceof body )
			$parent->add( $this );
	}

	/**
	 * Returns actual size for the resizer.
	 *
	 * @return int
	 */
	public function getSize ( ) { return $this->size; }
}

/**
 * Dummy resizer to replace ordinary resizer in the case that we need to create
 * some anchors on the bottom of the search results container.
 */
class dummyres extends pool
{
	/**
	 * Constructor.
	 *
	 * @param body $parent reference to parent component instance
	 * @param string $id identifier of the component
	 * @param string $js_var name of client side Javascript instance
	 * @param int $size current size
	 */
	public function __construct ( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'uicmp/dummy_resizer.html';
	}
}

/**
 * Virtual component to create and connect components used for searching. This
 * does not implement any common UICMP interface.
 *
 * Visual search elements are search form, container for result list and
 * list resizer.
 */
class vsearch extends vcmp
{
	/**
	 * Resizer shall not be rendered and used in the solution.
	 */
	const FLAG_NORESIZER = 1;
	
	/**
	 * Use dummy resizer instance.
	 */
	const FLAG_DUMMYRESIZER = 2;

	/**
	 * Identification string. Used e.g. for pairing in Ajax server implementation.
	 * 
	 * @var string
	 */
	protected $id = NULL;

	/**
	 * Reference to layout component.
	 * 
	 * @var layout
	 */
	protected $layout = NULL;

	/**
	 * Requirer instance for resources loading.
	 * 
	 * @var _requirer
	 */
	protected $requirer = NULL;

	/**
	 * Reference to instance of _uicmp_tab providing UI.
	 *
	 * @var tab
	 */
	protected $tab = NULL;

	/**
	 * Reference to form component.
	 *
	 * @var srchfrm
	 */
	protected $form = NULL;

	/**
	 * Reference to target area in the document for search results.
	 *
	 * @var srchcnt
	 */
	protected $container = NULL;

	/**
	 * Resizer component.
	 *
	 * @var srchres
	 */
	protected $resizer = NULL;

	/**
	 * Indicator component for the search form.
	 *
	 * @var indicator
	 */
	protected $ind = NULL;

	/**
	 * Search URL. Search parameters are appended to it when search is invoked
	 * from Javascript.
	 * 
	 * @var string
	 */
	protected $url = NULL;

	/**
	 * Specific parameters for Ajax requests. Associative array, keys are
	 * parameter names.
	 *
	 * @var array
	 */
	protected $params = NULL;

	/**
	 * Array containing search form configuration (keywords, page, order, etc.).
	 * 
	 * @var array
	 */
	protected $config = NULL;

	/**
	 * Size for resizers.
	 * 
	 * @var int
	 */
	protected $size = NULL;

	/**
	 * Flag describing if Javascript variable for resizer size was set.
	 *
	 * @var bool
	 */
	protected $jsSizeSet = false;

	/**
	 * Constructor.
	 *
	 * @param string $id identifier for the solution
	 * @param tab $tab reference to _uicmp_tab parent instance
	 * @param int $url flags, for most of applications it is set to 0
	 * @param string $url base URL string for Ajax requests
	 * @param array $params additional parameters for Ajax requests
	 * @param _list_cfg $list_cfg list configuration for search instance
	 * @param int $resizer_size rendered size for resizer, negative value means resizer widget should not be rendered
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
		$messages = $this->layout->getMsgs( );

		$this->form = new srchfrm( $head, $this->id . '.Form', $this->getJsVar( ), ( ( is_array( $this->config ) ) ? $this->config['k'] : '' ) );
			$this->ind = new indicator( $this->form, $this->form->getId( ) . '.Ind', grpitem::IT_IND, $messages['srch'] );
			$this->form->add( $this->ind );
		$this->container = new srchcnt( $body, $this->id . '.Results' );

		$head->add( $this->form );
		$body->add( $this->container );

		if ( !$this->isFlagSet( self::FLAG_NORESIZER ) && !$this->isFlagSet( self::FLAG_DUMMYRESIZER ) )
		{
			$this->resizer = new srchres( $body, $this->id . '.Resizer', $this->getJsVar( ), $this->size );
			$body->add( $this->resizer );
		}

		if ( $this->isFlagSet( self::FLAG_DUMMYRESIZER ) )
		{
			$this->resizer = new dummyres( $body, $this->id . '.Resizer' );
			$body->add( $this->resizer );
		}
	}

	/**
	 * Returns reference to resizer component.
	 *
	 * @return srchres
	 */
	public function getResizer ( ) { return $this->resizer; }

	/**
	 * Generates Javascript content for <head> element to initialize solution.
	 */
	public function generateReqs ( )
	{
		/**
		 * Initialize client side.
		 */
		$this->requirer->call( vlayout::RES_CSS,	array( $this->requirer->getRelative( ) . 'css/_list.css', __CLASS__ ) );
		$this->requirer->call( vlayout::RES_JSPLAIN, 'var ' . $this->getJsVar( ) . ' = new _uicmp_search( \'' . $this->id . '\', \'' . $this->tab->getHtmlId( ) . '\', '. $this->ind->getJsVar( ) . ', \'' . $this->url . '\', ' . uicmp::toJsArray( $this->params ) . ', ' . uicmp::toJsArray( $this->config ) . ', \'' . $this->form->getHtmlId( ) . '\', \'' . $this->container->getHtmlId( ) . '\', ' . ( ( ( !$this->isFlagSet( self::FLAG_DUMMYRESIZER ) ) && ( !$this->isFlagSet( self::FLAG_NORESIZER ) ) ) ? '\'' . $this->resizer->getHtmlId( ) . '\'' : 'null' ) . ' );' );
		$this->requirer->call( vlayout::RES_JSPLAIN, $this->layout->getJsVar( ) . '.registerTabCb( \'' . $this->tab->getHtmlId( ) . '\', \'onShow\', ' . $this->getJsVar( ) . '.tabShown );' );
		$this->requirer->call( vlayout::RES_JSPLAIN, $this->layout->getJsVar( ) . '.registerTabCb( \'' . $this->tab->getHtmlId( ) . '\', \'onLoad\', ' . $this->getJsVar( ) . '.startup );' );

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
			$this->requirer->call( vlayout::RES_JSPLAIN, '_uicmp_resizer_size = ' . $this->size . ';' );
			$this->jsSizeSet = true;
		}
	}

}

?>