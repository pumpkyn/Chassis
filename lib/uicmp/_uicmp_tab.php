<?php

/**
 * @file _uicmp_tab.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * Class representing single tab widget.
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_comp.php";
require_once CHASSIS_LIB . "uicmp/_uicmp_fold.php";
require_once CHASSIS_LIB . "uicmp/_vcmp_search.php";

class _uicmp_tab extends _uicmp_comp
{
	/**
	 * Defines whether tab has visible fold or not.
	 * 
	 * @var <uiTabFold>
	 */
	protected $fold = NULL;

	/**
	 * Rendered visibility of the tab.
	 * 
	 * @var <bool>
	 */
	protected $hidden = TRUE;

	/**
	 * Indicates if tab can be put onto stack and used by Back buttons.
	 *
	 * @var <bool>
	 */
	protected $stackable = TRUE;

	/**
	 * Header section of the tab.
	 * 
	 * @var <_uicmp_head>
	 */
	protected $head = NULL;

	/**
	 * Main section of the tab.
	 *
	 * @var <_uicmp_body>
	 */
	protected $body = NULL;

	/**
	 * Array of virtual components assigned to tab.
	 *
	 * @var <array>
	 */
	protected $vcmps = NULL;

	/**
	 * Constructor.
	 *
	 * @param <_uicmp_layout> $parent reference to layout instance
	 * @param <string> $id identifier of the component
	 */
	public function  __construct ( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'uicmp/tab.html';
	}

	/**
	 * Implementation of virtual method. Registers Javascript code for <head>
	 * element.
	 */
	public function generateJs ( )
	{
		$requirer = $this->getRequirer( );
		
		if ( !is_null( $requirer ) )
		{
			if ( !is_null( $this->fold ) )
			{
				$foldId= '\'' . $this->fold->getHtmlId( ) . '\'';
				$this->fold->generateJs( );
			}
			else
				$foldId= 'null';

			/**
			 * Tab interface may be used also by SkyDome dialogs, for which
			 * there is usually no Javascript instance created.
			 *
			 * @todo is this still true?
			 */
			//if ( !is_null( $this->parent->getJsVar( ) ) )
				$requirer->call( _uicmp_layout::RES_JSPLAIN, $this->parent->getJsVar( ) . '.addTab( \'' . $this->getHtmlId( ) . '\', ' . ( ( $this->isHidden( ) ) ? 'true' : 'false' ) . ', ' . ( ( $this->isStackable( ) ) ? 'true' : 'false' ) . ', ' . $foldId . ' );' );

			/**
			 * Generate Javascript for head and body sections of the tab.
			 */
			if ( !is_null( $this->head ) )
				$this->head->generateJs( );

			if ( !is_null( $this->body ) )
				$this->body->generateJs( );

			/**
			 * Generate Javascript for virtual components.
			 */
			if ( is_array( $this->vcmps ) )
				foreach ( $this->vcmps as $vcmp )
					$vcmp->generateJs( );
		}
	}

	/**
	 * Created Fold component for the tab and returns its reference.
	 *
	 * @param <string> $title text to display
	 * @return <_uicmp_fold>
	 */
	public function createFold ( $title )
	{
		$this->fold = new _uicmp_fold( $this, $this->id . '.Fold', $title );
		return $this->fold;
	}

	/**
	 * Creates search solution for the tab.
	 *
	 * @param <string> $id identifier of search instance
	 * @param <flags> $flags flags for the search solution
	 * @param <string> $url base URL for sending Ajax requests
	 * @param <array> $params array of additional parameters for Ajax request
	 * @param <array> $config search client instance configuration
	 * @param <int> $resizerSize size for resizer
	 * @return <_vcmp_comp> refence to stored virtual component
	 */
	public function createSearch ( $id, $flags, $url, $params, $config, $resizerSize ) { return $this->addVcmp( new _vcmp_search( $id, $this, $flags, $url, $params, $config, $resizerSize ) ); }

	/**
	 * Attach virtual component to the tab.
	 *
	 * @param <_vcmp_comp> $vcmp virtual component
	 * @return <_vcmp_comp> refence to stored virtual component
	 */
	public function addVcmp ( $vcmp ) { $this->vcmps[] = $vcmp; return $vcmp; }

	/**
	 * Sets fold component for the tab.
	 *
	 * @param <_uicmp_fold> $fold
	 */
	public function setFold ( &$fold ) { $this->fold = $fold; }

	/**
	 * Returns reference to tab fold component.
	 *
	 * @return <_uicmp_fold>
	 */
	public function getFold ( ) { return $this->fold; }

	/**
	 * Render component as hidden.
	 */
	public function hide ( ) { $this->hidden = TRUE; }

	/**
	 * Render component as visible.
	 */
	public function show ( ) { $this->hidden = FALSE; }

	/**
	 * Render component as unstackable.
	 */
	public function unstack ( ) { $this->stackable = FALSE; }

	/**
	 * Returns visibility of the component.
	 *
	 * @return <bool>
	 */
	public function isHidden ( ) { return $this->hidden; }

	/**
	 * Returns stackability of the component.
	 *
	 * @return <bool>
	 */
	public function isStackable ( ) { return $this->stackable; }

	/**
	 * Read interface and lazy initialization of tab header section.
	 * 
	 * @return <_uicmp_head>
	 */
	public function getHead ( )
	{
		if ( is_null( $this->head ) )
		{
			require_once CHASSIS_LIB . 'uicmp/_uicmp_head.php';
			$this->head = new _uicmp_head( $this, $this->id . '.Head' );
		}

		return $this->head;
	}

	/**
	 * Read interface and lazy initialization of tab body section.
	 * 
	 * @return <_uicmp_body>
	 */
	public function getBody ( )
	{
		if ( is_null( $this->body ) )
		{
			require_once CHASSIS_LIB . 'uicmp/_uicmp_body.php';
			$this->body = new _uicmp_body( $this, $this->id . '.Body' );
		}

		return $this->body;
	}

	/**
	 * Read interface for _uicmp_layout Javascript variable name.
	 *
	 * @return <string>
	 */
	public function getLayoutJsVar ( )
	{
		return $this->parent->getJsVar();
	}
}

?>