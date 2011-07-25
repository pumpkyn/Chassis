<?php

/**
 * @file _uicmp_layout.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_comp.php";
require_once CHASSIS_LIB . "uicmp/_uicmp_tab.php";
require_once CHASSIS_LIB . "uicmp/_uicmp_tab_sep.php";
require_once CHASSIS_LIB . "uicmp/_vcmp_layout.php";

/**
 * Overall uicmps layout container class. Using this class assumes that there is
 * going to be always only one instance of this class.
 */
class _uicmp_layout extends _vcmp_layout
{	
	/**
	 * Constructor. Creates empty array of uicmps. Each layout should contain at
	 * least one tab.
	 *
	 * @param <_requirer> $requirer reference to requirer instance
	 * @param <string> $lang two-character language code for localization messages
	 */
	public function  __construct ( $requirer = NULL, $lang = 'en' )
	{
		parent::__construct( $requirer, $lang );
	}

	/**
	 * Creates new tab element, pushes it onto internal array of uicmps and
	 * returns reference to it.
	 *
	 * @param <string> $id unique identifier for the tab
	 * @param <bool> $hidden rendered visibility of the component
	 * @return <uiTab>
	 */
	public function createTab ( $id, $hidden = TRUE )
	{
		$tab = new _uicmp_tab( $this, $id );
		
		if ( $hidden !== TRUE )
			$tab->show( );

		$this->uicmps[] = $tab;
		
		return $tab;
	}

	/**
	 * Creates dummy _uicmp_tab_sep component to create gap between Folds.
	 *
	 * @return <_uicmp_tab_sep>
	 */
	public function createSep ( )
	{
		$sep = new _uicmp_tab_sep( $this );
		$this->uicmps[] = $sep;
		return $sep;
	}

	/**
	 * Detects if there are folds associated with uicmps.
	 *
	 * @return <bool>
	 */
	public function hasFolds ( )
	{
		if ( !is_array( $this->uicmps ) )
			return false;

		foreach( $this->uicmps as $tab )
			if ( !is_null( $tab->getFold( ) ) )
				return true;

		return false;
	}

	/**
	 * Creates separator widget, i.e. fake tab to occupy space between uicmps
	 * folds.
	 */
	public function createSeparator ( )
	{
		$this->uicmps[] = new uiSeparator( );
	}
}

?>