<?php

/**
 * @file _vcmp_layout.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * Common functionality for top level UICMP containers: _uicmp_layout (ordinary
 * in-page placed UI) and _uicmp_dlgs (for SkyDome dialogs).
 */

abstract class _vcmp_layout
{
	/**
	 * Resource types used by UICMP widgets.
	 */
	const RES_JS		= 'resJavascript';
	const RES_CSS		= 'resCss';
	const RES_ONLOAD	= 'resOnLoad';
	const RES_JSPLAIN	= 'resJsPlain';
	const RES_BODYCHILD	= 'resBodyChild';

	/**
	 * Array of virtual components assigned directly to layout.
	 *
	 * @var <array>
	 */
	protected $vcmps = NULL;

	/**
	 * uicmps driven by this layout.
	 * 
	 * @var <array>
	 */
	public $uicmps = null;

	/**
	 * Requirer instance used for resource requirements.
	 *
	 * @var <Requirer>
	 */
	protected $requirer = NULL;

	/**
	 * Last integer part of layout Id to be used to generate unique global
	 * Javascript variable representing layout instance on the other side.
	 *
	 * @var <int>
	 */
	protected static $lastId = 0;

	/**
	 * Name for Javascript variable representing layout on the other side.
	 *
	 * @var <string>
	 */
	protected $jsVar = NULL;

	/**
	 * Localization messages.
	 *
	 * @var <array>
	 */
	protected $messages = NULL;

	public function  __construct ( $requirer = NULL, $lang = 'en' )
	{
		$this->uicmps		= Array( );
		$this->requirer		= $requirer;

		$i18n = CHASSIS_I18N . 'uicmp/' . $lang . '.php';
		if ( file_exists( $i18n ) )
			include $i18n;
		else
			include CHASSIS_I18N . 'uicmp/en.php';

		$this->messages = $_uicmp_i18n;
	}

	/**
	 * Attach virtual component to the tab.
	 *
	 * @param <_vcmp_comp> $vcmp virtual component
	 */
	public function addVcmp ( $vcmp ) { $this->vcmps[] = $vcmp; }

	/**
	 * Append tab created outside of this class. Usable for user components
	 * derived from _uicmp_tab.
	 *
	 * @param <_uicmp_tab> $tab
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
				$tab->generateJs( );

		/**
		 * Generate Javascript for virtual components.
		 */
		if ( is_array( $this->vcmps ) )
			foreach ( $this->vcmps as $vcmp )
				$vcmp->generateJs( );
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
			$this->jsVar = '_uicmp_layout_i_' . static::$lastId++;

			if ( !is_null( $this->requirer ) )
				$this->requirer->call( static::RES_JSPLAIN,	'var ' . $this->jsVar . ' = new _uicmp_layout( );' );
		}

		return $this->jsVar;
	}

	/**
	 * This method must have same signature as one defined in _uicmp_layoutItem class.
	 *
	 * @return <Requirer>
	 */
	public function getRequirer ( ) { return $this->requirer; }

	/**
	 * Returns array of localization messages.
	 *
	 * @return <array>
	 */
	public function getMessages ( ) { return $this->messages; }

	/**
	 * Returns first tab in the table.
	 *
	 * @return <uiTab>
	 */
	public function getFirst ( )
	{
		if ( is_array( $this->uicmps ) )
			return reset( $this->uicmps );

		return NULL;
	}

	/**
	 * Returns next tab in the table.
	 *
	 * @return <uiTab>
	 */
	public function getNext ( )
	{
		if ( is_array( $this->uicmps ) )
			return next( $this->uicmps );

		return NULL;
	}
}

?>