<?php

/**
 * @file layout.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . "uicmp/tab.php";
require_once CHASSIS_LIB . "uicmp/uicmp.php";
require_once CHASSIS_LIB . "uicmp/vlayout.php";

/**
 * Dummy component to create gap between folds.
 */
class sepfold extends uicmp
{
	/**
	 * Constructor.
	 *
	 * @param sep $parent parent component instance
	 * @param string $id identifier of the component
	 */
	public function __construct( &$parent, $id )
	{
		parent::__construct( $parent, $id );
		$this->type = __CLASS__;
		$this->renderer = CHASSIS_UI . 'uicmp/sepfold.html';
	}

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateReqs ( ) { }
}

/**
 * Dummy tab widget to carry instance of _uicmp_fold_sep.
 */
class sep extends uicmp
{
	/**
	 * Defines whether tab has visible fold or not.
	 *
	 * @var sepfold
	 */
	public $fold = null;

	/**
	 * Contructor.
	 *
	 * @param <_uicmp_layout> $parent reference to parent widget
	 * @param <string> $id identifier of the component
	 */
	public function  __construct ( &$parent )
	{
		$this->id		= static::$lastId++;
		parent::__construct( $parent, $this->id );
		$this->type		= __CLASS__;
		$this->renderer	= NULL;
		$this->fold		= new sepfold( $this, $this->id . '.Fold' );
	}

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateReqs ( ) { }

	/**
	 * Returns reference to tab fold component.
	 *
	 * @return sepfold
	 */
	public function getFold ( ) { return $this->fold; }
}

/**
 * Overall visual layout container class. Using this class assumes that there is
 * going to be always only one instance of this class.
 */
class layout extends vlayout
{	
	/**
	 * Constructor. Creates empty array of uicmps. Each layout should contain at
	 * least one tab.
	 *
	 * @param _requirer $requirer reference to requirer instance
	 * @param _i18n_loader $i18n_loader instance of localization provider
	 */
	public function  __construct ( $requirer, $i18n_loader )
	{
		parent::__construct( $requirer, $i18n_loader );
	}

	/**
	 * Creates new tab element, pushes it onto internal array of uicmps and
	 * returns reference to it.
	 *
	 * @param string $id unique identifier for the tab
	 * @param bool $hidden rendered visibility of the component
	 * @return tab
	 */
	public function createTab ( $id, $hidden = TRUE )
	{
		$tab = new tab( $this, $id );
		
		if ( $hidden !== TRUE )
			$tab->show( );

		$this->uicmps[] = $tab;
		
		return $tab;
	}

	/**
	 * Creates dummy sep tab component to create gap between folds.
	 *
	 * @return sep
	 */
	public function createSep ( )
	{
		$sep = new sep( $this );
		$this->uicmps[] = $sep;
		return $sep;
	}

	/**
	 * Detects if there are any folds associated with uicmps, which would
	 * require rendering.
	 *
	 * @return bool
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
}

?>