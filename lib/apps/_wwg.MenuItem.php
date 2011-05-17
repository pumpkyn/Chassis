<?php

/**
 * @file __wwg.MenuItem.php
 * @author giorno
 * @package Chassis
 * @subpackage Apps
 *
 * Object representing anchor in menu row.
 */

require_once CHASSIS_LIB . 'apps/_wwg.Wwg.php';

class MenuItem extends Wwg
{
	/**
	 * Definition of menu item types.
	 */
	const TYPE_JS	= 'js';		// Javascript action item
	const TYPE_A	= 'a';		// regular HTML anchor

	/**
	 * Static variable used for generating unique ID of the widget.
	 *
	 * @var <int>
	 */
	private static $lastId = 0;

	/**
	 * Type of menu item. See constants.
	 *
	 * @var <string>
	 */
	private $type = NULL;

	/**
	 * Text to be displayed.
	 *
	 * @var <string>
	 */
	private $title = NULL;

	/**
	 * Javascript code, URL or another value of the item. Depends on type.
	 *
	 * @var <string>
	 */
	private $value = NULL;

	/**
	 * Optional stylesheet for the item.
	 * @var <string>
	 */
	private $class = NULL;

	/**
	 * Constructor. This object is passive, so its whole specialized logic is
	 * placed in the constructor.
	 *
	 * @param <string> $type type of menu item, see constants
	 * @param <string> $title text to display
	 * @param <string> $value value of item
	 * @param <string> $class optional style
	 */
	public function __construct ( $type, $title, $value, $class = NULL )
	{
		$this->id = static::$lastId++;
		$this->template = CHASSIS_UI . '_wwg.MenuItem.html';

		$this->type = $type;
		$this->title = $title;
		$this->value = $value;
		$this->class = $class;
	}

	/**
	 * Read interface for type.
	 *
	 * @return <string> type of the item
	 */
	public function getType() { return $this->type; }

	/**
	 * Read interface for title.
	 *
	 * @return <string>
	 */
	public function getTitle() { return $this->title; }

	/**
	 * Read interface for value.
	 *
	 * @return <string>
	 */
	public function getValue() { return $this->value; }

	/**
	 * Read interface for class.
	 *
	 * @return <mixed> can be NULL
	 */
	public function getClass() { return $this->class; }
}

?>