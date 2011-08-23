<?php

/**
 * @file pool.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . "uicmp/uicmp.php";

/**
 * Abstract container component providing supercomponent feature.
 */
abstract class pool extends uicmp
{
	/**
	 * Array of subcomponents.
	 *
	 * @var array
	 */
	protected $items = NULL;

	/**
	 * Constructor.
	 *
	 * @param uicmp $parent reference to parent component instance
	 * @param string $id identifier of the component
	 */
	public function  __construct ( &$parent, $id ) { parent::__construct( $parent, $id ); }

	/**
	 * Registers new component into array of subcomponents.
	 *
	 * @param uicmp $item reference to subcomponent instance
	 */
	public function add( &$item ) { $this->items[$item->getId( )] = $item; }

	/**
	 * Detects if internal storage of subcomponents if empty.
	 *
	 * @return bool
	 */
	public function isEmpty ( ) { return ( !is_array( $this->items ) || !count( $this->items ) ); }

	/**
	 * Iterator interface. Returns first subcomponent and sets reading pointer
	 * on it.
	 *
	 * @return uicmp
	 */
	public function getFirst ( )
	{
		if ( is_array( $this->items ) )
			return reset( $this->items );

		return NULL;
	}

	/**
	 * Iterator interface. Moves pointer forward and returns component.
	 *
	 * @return uicmp
	 */
	public function getNext ( )
	{
		if ( is_array( $this->items ) )
			return next( $this->items );

		return NULL;
	}

	/**
	 * Calls generateReqs() method on all subcomponents.
	 */
	public function generateReqs ( )
	{
		if ( is_array( $this->items ) )
			foreach ( $this->items as $item )
				$item->generateReqs( );
	}
}

?>