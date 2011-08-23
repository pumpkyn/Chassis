<?php

/**
 * @file vlayout.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

/**
 * Virtual layout. Common functionality for top level UICMP containers: layout
 * (ordinary in-page placed UI) and dlgs (for SkyDome dialogs).
 */
abstract class vlayout
{
	/**
	 * Resource types used by UICMP widgets.
	 */
	const RES_JS		= 1;
	const RES_CSS		= 2;
	const RES_ONLOAD	= 4;
	const RES_JSPLAIN	= 8;
	const RES_BODYCHILD	= 16;

	/**
	 * Virtual components driven by this layout.
	 *
	 * @var array
	 */
	protected $vcmps = NULL;

	/**
	 * Visual components driven by and rendered in this layout.
	 * 
	 * @var array
	 */
	public $uicmps = null;

	/**
	 * Requirer instance used for resource requirements.
	 *
	 * @var _requirer
	 */
	protected $requirer = NULL;

	/**
	 * Last integer part of layout Id to be used to generate unique global
	 * Javascript variable representing layout instance on the other side.
	 *
	 * @var int
	 */
	protected static $lastId = 0;

	/**
	 * Name for Javascript variable representing layout on the other side.
	 *
	 * @var string
	 */
	protected $jsVar = NULL;

	/**
	 * Localization messages.
	 *
	 * @var array
	 */
	protected $messages = NULL;

	/**
	 * Constructor.
	 * 
	 * @param _requirer $requirer requirer instance
	 * @param _i18n_loader $i18n_loader instance of localization provider
	 */
	public function  __construct ( $requirer, $i18n_loader )
	{
		$this->requirer	= $requirer;
		$this->messages	= $i18n_loader->msg( );
	}

	/**
	 * Attach virtual component to the tab.
	 *
	 * @param vcmp $vcmp virtual component
	 */
	public function addVcmp ( $vcmp ) { $this->vcmps[] = $vcmp; }

	/**
	 * Append tab created outside of this class. Usable for user components
	 * derived from tab component.
	 *
	 * @param uicmp $uicmp reference to visual component instance
	 */
	public function addUicmp ( &$uicmp ) { $this->uicmps[] = $uicmp; }

	/**
	 * Implements last phase of PHP object LC.
	 */
	public function init ( )
	{
		/**
		 * Basic resources requirements.
		 */
		if ( !is_null( $this->requirer ) )
		{
			$this->requirer->call( static::RES_CSS,	array( $this->requirer->getRelative( ) . 'css/_uicmp.css', __CLASS__ ) );
			$this->requirer->call( static::RES_JS,	array( $this->requirer->getRelative( ) . 'js/_uicmp.js', __CLASS__ ) );
			$this->requirer->call( static::RES_ONLOAD,	$this->getJsVar( ) . '.startup();' );
		}

		/**
		 * Call all children and ask them to render their Javascript code for
		 * <head> element.
		 */
		if ( is_array( $this->uicmps ) )
			foreach ( $this->uicmps as $tab )
				$tab->generateReqs( );

		/**
		 * Generate Javascript for virtual components.
		 */
		if ( is_array( $this->vcmps ) )
			foreach ( $this->vcmps as $vcmp )
				$vcmp->generateReqs( );
	}

	/**
	 * Generates, caches and provides Javascript variable name for layout
	 * instance on the other side. Also registers necessary code to be placed
	 * into <head> element.
	 *
	 * @return <string>
	 */
	public function getJsVar( )
	{
		if ( is_null( $this->jsVar ) )
		{
			$this->jsVar = 'uicmp_layout_i_' . static::$lastId++;

			if ( !is_null( $this->requirer ) )
				$this->requirer->call( static::RES_JSPLAIN,	'var ' . $this->jsVar . ' = new _uicmp_layout( );' );
		}

		return $this->jsVar;
	}

	/**
	 * This method must have same signature as one defined in _uicmp_layoutItem class.
	 *
	 * @return _requirer
	 */
	public function getRequirer ( ) { return $this->requirer; }

	/**
	 * Returns array of localization messages.
	 *
	 * @return array
	 */
	public function getMsgs ( ) { return $this->messages; }

	/**
	 * Iterator interface. Returns first tab in the table.
	 *
	 * @return uicmp
	 */
	public function getFirst ( )
	{
		if ( is_array( $this->uicmps ) )
			return reset( $this->uicmps );

		return NULL;
	}

	/**
	 * Iterator interface. Returns next tab in the table.
	 *
	 * @return uicmp
	 */
	public function getNext ( )
	{
		if ( is_array( $this->uicmps ) )
			return next( $this->uicmps );

		return NULL;
	}
}

?>