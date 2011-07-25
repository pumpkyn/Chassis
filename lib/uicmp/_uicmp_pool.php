<?php

/**
 * @file _uicmp_pool.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . "uicmp/_uicmp_comp.php";

/**
 * Abstract container component providing supercomponent feature.
 */
abstract class _uicmp_pool extends _uicmp_comp
{
	/**
	 * Array of subcomponents.
	 *
	 * @var <array>
	 */
	protected $items = NULL;

	/**
	 * Constructor.
	 *
	 * @param <_uicmp_comp> $parent reference to parent component instance
	 * @param <string> $id identifier of the component
	 */
	public function  __construct ( &$parent, $id ) { parent::__construct( $parent, $id ); }

	/**
	 * Registers new component into array of subcomponents.
	 *
	 * @param <uicmp_component> $item
	 */
	public function add( &$item ) { $this->items[$item->getId( )] = $item; }

	/**
	 * Detects if internal storage of subcomponents if empty.
	 *
	 * @return <bool>
	 */
	public function isEmpty ( ) { return ( !is_array( $this->items ) || !count( $this->items ) ); }

	/**
	 * Returns first subcomponent and sets reading pointer on it.
	 *
	 * @return <_uicmp_comp>
	 */
	public function getFirst ( )
	{
		if ( is_array( $this->items ) )
			return reset( $this->items );

		return NULL;
	}

	/**
	 * Moves pointer and returns component.
	 *
	 * @return <_uicmp_comp>
	 */
	public function getNext ( )
	{
		if ( is_array( $this->items ) )
			return next( $this->items );

		return NULL;
	}

	/**
	 * Calls generateJs() method on all subcomponents.
	 */
	public function generateJs ( )
	{
		if ( is_array( $this->items ) )
			foreach ( $this->items as $item )
				$item->generateJs( );
	}
}

?>