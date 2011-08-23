<?php

/**
 * @file indicator.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . 'uicmp/grpitem.php';

/**
 * Specialized group item to provide both, server and client side of component
 * providing status information. Used for forms in head section of the
 * _uicmp_tab to show Ajax request status, information about form events, etc.
 */
class indicator extends grpitem
{
	/**
	 * Reference to separator component associated to this indicator.
	 * 
	 * @var grpitem
	 */
	protected $separator = NULL;

	/**
	 * Associative array with messages to be displayed in the indicator.
	 *
	 * @var array
	 */
	protected $messages = NULL;

	/**
	 * Constructor.
	 *
	 * @param pool $parent parent component instance
	 * @param string $id identifier of the component
	 * @param string $title text to display
	 * @param array $messages associative array with messages
	 */
    public function  __construct( &$parent, $id, $title, $messages )
	{
		/**
		 * Create separator item before me.
		 */
		$this->separator = new grpitem( $parent, $id . '.sep', grpitem::IT_TXT, '|', NULL, '_uicmp_hidden' );
		$parent->add( $this->separator );

		parent::__construct( $parent, $id, self::IT_IND, $title, NULL, '_uicmp_hidden' );

		$this->messages	= $messages;
		$this->type		= __CLASS__;
		$this->jsPrefix	= '_uicmp_ind_i_';
	}

	/**
	 * Generating client side requirements for the indicator.
	 */
	public function generateReqs ( )
	{
		$requirer = $this->getRequirer( );
		if ( !is_null( $requirer ) )
		{
			$requirer->call( vlayout::RES_JSPLAIN, 'var ' . $this->getJsVar( ) . ' = new _uicmp_ind( \'' . $this->getHtmlId( ) . '\', \''. $this->separator->getHtmlId( ) .'\', ' . self::toJsArray( $this->messages ) . ' );' );
		}
	}
}

?>