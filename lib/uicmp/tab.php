<?php

/**
 * @file tab.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . "uicmp/uicmp.php";
require_once CHASSIS_LIB . "uicmp/pool.php";
require_once CHASSIS_LIB . "uicmp/fold.php";
require_once CHASSIS_LIB . "uicmp/vsearch.php";

/**
 * UICMP component for tab head section.
 */
class head extends pool
{
	/**
	 * Constructor.
	 *
	 * @param tab $parent reference to tab component instance
	 * @param string $id identifier of the component
	 */
	public function __construct ( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'uicmp/head.html';
	}
}

/** 
 * UICMP component for tab body section.
 */
class body extends pool
{
	/**
	 * Constructor.
	 *
	 * @param tab $parent reference to tab component instance
	 * @param string $id identifier of the component
	 */
	public function __construct ( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'uicmp/body.html';
	}
}

/**
 * Class representing single tab widget.
 */
class tab extends uicmp
{
	/**
	 * Defines whether tab has visible fold or not.
	 * 
	 * @var fold
	 */
	protected $fold = NULL;

	/**
	 * Rendered initial visibility of the tab. Only one tab within layout
	 * container should be visible.
	 * 
	 * @var bool
	 */
	protected $hidden = TRUE;

	/**
	 * Indicates if tab can be put onto stack and used by Back buttons.
	 *
	 * @var bool
	 */
	protected $stackable = TRUE;

	/**
	 * Header section of the tab.
	 * 
	 * @var head
	 */
	protected $head = NULL;

	/**
	 * Main section of the tab.
	 *
	 * @var body
	 */
	protected $body = NULL;

	/**
	 * Array of virtual components assigned to tab.
	 *
	 * @var array
	 */
	protected $vcmps = NULL;

	/**
	 * Constructor.
	 *
	 * @param vlayout $parent reference to layout instance
	 * @param string $id identifier of the component
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
	public function generateReqs ( )
	{
		$requirer = $this->getRequirer( );
		
		if ( !is_null( $requirer ) )
		{
			if ( !is_null( $this->fold ) )
			{
				$foldId= '\'' . $this->fold->getHtmlId( ) . '\'';
				$this->fold->generateReqs( );
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
				$requirer->call( vlayout::RES_JSPLAIN, $this->parent->getJsVar( ) . '.addTab( \'' . $this->getHtmlId( ) . '\', ' . ( ( $this->isHidden( ) ) ? 'true' : 'false' ) . ', ' . ( ( $this->isStackable( ) ) ? 'true' : 'false' ) . ', ' . $foldId . ' );' );

			/**
			 * Generate Javascript for head and body sections of the tab.
			 */
			if ( !is_null( $this->head ) )
				$this->head->generateReqs( );

			if ( !is_null( $this->body ) )
				$this->body->generateReqs( );

			/**
			 * Generate Javascript for virtual components.
			 */
			if ( is_array( $this->vcmps ) )
				foreach ( $this->vcmps as $vcmp )
					$vcmp->generateReqs( );
		}
	}

	/**
	 * Created Fold component for the tab and returns its reference.
	 *
	 * @param string $title text to display
	 * @return fold
	 */
	public function createFold ( $title )
	{
		$this->fold = new fold( $this, $this->id . '.Fold', $title );
		return $this->fold;
	}

	/**
	 * Creates search solution for the tab.
	 *
	 * @param string $id identifier of search instance
	 * @param flags $flags flags for the search solution
	 * @param string $url base URL for sending Ajax requests
	 * @param array $params array of additional parameters for Ajax request
	 * @param array $config search client instance configuration
	 * @param int $resizerSize size for resizer
	 * @return vsearch refence to stored virtual component
	 */
	public function createSearch ( $id, $flags, $url, $params, $config, $resizerSize ) { return $this->addVcmp( new vsearch( $id, $this, $flags, $url, $params, $config, $resizerSize ) ); }

	/**
	 * Attach virtual component to the tab.
	 *
	 * @param vcmp $vcmp virtual component
	 * @return vcmp refence to stored virtual component
	 */
	public function addVcmp ( $vcmp ) { $this->vcmps[] = $vcmp; return $vcmp; }

	/**
	 * Sets fold component for the tab.
	 *
	 * @param fold $fold
	 */
	public function setFold ( &$fold ) { $this->fold = $fold; }

	/**
	 * Returns reference to tab fold component.
	 *
	 * @return fold
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
	 * @return bool
	 */
	public function isHidden ( ) { return $this->hidden; }

	/**
	 * Returns stackability of the component.
	 *
	 * @return bool
	 */
	public function isStackable ( ) { return $this->stackable; }

	/**
	 * Read interface and lazy initialization of tab header section.
	 * 
	 * @return head
	 */
	public function getHead ( )
	{
		if ( is_null( $this->head ) )
			$this->head = new head( $this, $this->id . '.Head' );

		return $this->head;
	}

	/**
	 * Read interface and lazy initialization of tab body section.
	 * 
	 * @return body
	 */
	public function getBody ( )
	{
		if ( is_null( $this->body ) )
			$this->body = new body( $this, $this->id . '.Body' );

		return $this->body;
	}

	/**
	 * Read interface for _uicmp_layout Javascript variable name.
	 *
	 * @return string
	 */
	public function getLayoutJsVar ( ) { return $this->parent->getJsVar(); }
}

?>