<?php

/**
 * @file __wwg.Menu.php
 * @author giorno
 * @package Chassis
 * @subpackage Apps
 *
 * Object representing group of MenuItem widgets.
 */

require_once CHASSIS_LIB . 'apps/_wwg.Wwg.php';

class Menu extends Wwg
{
	/*
	 * Default id used for the widget when no ID is specified in constructor.
	 */
	const DEFAULT_ID = '_wwg.Menu';

	/**
	 * Internal storage for menu items.
	 * 
	 * @var <array>
	 */
	private $items = NULL;

	/**
	 * Constructor.
	 *
	 * @param <string> $id widget id
	 */
	public function __construct ( $id = NULL )
	{
		if ( !is_null( $id ) )
			$this->id = $id;
		else
			$this->id = static::DEFAULT_ID;
		
		$this->template = CHASSIS_UI . '_wwg.Menu.html';

		$this->type = __CLASS__;
	}

	/**
	 * Registers new menu item.
	 *
	 * @param <MenuItem> $item menu item widget
	 */
	public function register( &$item )
	{
		$this->items[$item->getId( )] = $item;
	}

	/**
	 * Returns first item and resets pointer to beginning of array.
	 *
	 * @return <MenuItem>
	 */
	public function getFirst ( )
	{
		if ( is_array( $this->items ) )
			return reset( $this->items );
		
		return NULL;
	}

	/**
	 * Move pointer forwards and returns next item.
	 *
	 * @return <MenuItem>
	 */
	public function getNext ( )
	{
		if ( is_array( $this->items ) )
			return next( $this->items );

		return NULL;
	}
}

?>