<?php

/**
 * @file tuifrm.php
 * @author giorno
 * @package Chassis
 * @subpackage Persistence
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\pers;

require_once CHASSIS_LIB . 'uicmp/pool.php';

/**
 * Form for advanced searching in the table. It is derived from _uicmp_pool
 * component due to its need to use _uicmp_gi_ind and separator for
 * indicator of outgoing Ajax request. Part of search solution.
 */
class tuifrm extends \io\creat\chassis\uicmp\pool
{
	/**
	 * Parent Persistence instance.
	 * @var \io\creat\chassis\pers\instance 
	 */
	protected $pi = NULL;
	
	/**
	 * Instance of UICMP indicator.
	 * @var \io\creat\chassis\uicmp\indicator
	 */
	protected $indicator = NULL;
	
	/**
	 * Form UI configuration.
	 * @var array
	 */
	protected $cfg = NULL;

	/**
	 * Constructor.
	 *
	 * @param head $parent reference to parent component instance
	 * @param string $id identifier of the component
	 * @param string $js_var name of Javascript variable, created by vsearch to control search operation
	 * @param string $keywords text to populate search field
	 */
	public function __construct( &$parent, $id, $pi, $cfg )
	{
		parent::__construct( $parent, $id );
		$this->pi		= $pi;
		$this->cfg		= $cfg;
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UI . 'pers/tuifrm.html';
		
		$this->parent->add( $this );
	}
	
	/**
	 * Getter/setter for UICMP indicator instance.
	 * @param \io\creat\chassis\uicmp\indicator $ind if not NULL, act as setter, otherwise get current value
	 * @return \io\creat\chassis\uicmp\indicator
	 */
	public function ind ( $indicator = NULL )
	{
		if ( !is_null( $indicator ) )
		{
			$this->indicator = $indicator;
			$this->add( $this->indicator->sep( ) );
			$this->add( $this->indicator );
		}
		
		return $this->indicator;
	}
	
	/**
	 * Getter for form configuration. Configuration prescribes what and how will
	 * be displayed in the form UI.
	 * @param array
	 */
	public function cfg ( ) { return $this->cfg; }
	
	/**
	 * Getter for name of Javascript instance. Intended for use from the
	 * renderer.
	 * @return string
	 */
	public function jsVar ( ) { return $this->pi->jsVar( ); }
}

?>