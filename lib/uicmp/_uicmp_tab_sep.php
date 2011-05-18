<?php

/**
 * @file _uicmp_tab_sep.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * Dummy tab widget to carry instance of _uicmp_fold_sep.
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_comp.php";
require_once CHASSIS_LIB . "uicmp/_uicmp_fold_sep.php";

class _uicmp_tab_sep extends _uicmp_comp
{
	/**
	 * Defines whether tab has visible fold or not.
	 *
	 * @var <uiTabFold>
	 */
	public $fold = null;

	/**
	 * Used to generate unuque Id.
	 * @var <int>
	 */
	//private static $lastId = 0;

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
		$this->fold		= new _uicmp_fold_sep( $this, $this->id . '.Fold' );
	}

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function generateJs ( ) { }

	/**
	 * Returns reference to tab fold component.
	 *
	 * @return <_uicmp_fold>
	 */
	public function getFold ( ) { return $this->fold; }


}

?>