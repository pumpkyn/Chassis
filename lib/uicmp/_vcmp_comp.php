<?php

/**
 * @file _vcmp_comp.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * Virtual component interface, common ancestor to all components in UICMP
 * framework, virtual and/or UI.
 */

abstract class _vcmp_comp
{
	/**
	 * Reference to UICMP parent widget.
	 *
	 * @var <_uicmp_layoutItem>
	 */
	protected $parent = NULL;

	/**
	 * Implementation specific flags. May control behaviour of particular class.
	 *
	 * @var <int>
	 */
	protected $flags = 0;

	/**
	 * Name of Javascript variable holding component client side instance.
	 *
	 * @var <string>
	 */
	protected $jsVar = NULL;

	/**
	 * Internal counter to server as cache for generated Javascript variable
	 * names .
	 *
	 * @var <int>
	 */
	protected static $lastId = 0;

	/**
	 * Prefix part of Javascript variable name. Static member $lastId is
	 * appended to this prefix to form Javascript variable name.
	 *
	 * @var <string>
	 */
	protected $jsPrefix = NULL;

	/**
	 * Constructor.
	 *
	 * @param <_vcmp_comp> $parent reference to parent element
	 */
	public function __construct( &$parent )
	{
		$this->parent = $parent;
	}

	/**
	 * Detects if particular flag is set for the component.
	 *
	 * @param <int> $mask
	 * @return <bool>
	 */
	public function isFlagged( $mask ) { return ( $this->flags & $mask ) != 0; }

	/**
	 * Returns reference to parent element.
	 *
	 * @return <mixed>
	 */
	public function getParent ( ) { return $this->parent; }

	/**
	 * Read interface and on-demand instantiation of client side Javascript
	 * variable.
	 *
	 * @return <string>
	 */
	public function getJsVar ( )
	{
		if ( is_null( $this->jsVar ) )
			$this->jsVar = ( ( is_null( $this->jsPrefix ) ) ? '_uicmp_v' : $this->jsPrefix ) . '_' . static::$lastId++;

		return $this->jsVar;
	}

	/**
	 * Compose Javascript Object/associative array with parameters from PHP
	 * array structure.
	 *
	 * @param <array> $struct input structure, PHP array
	 * @return <string> Javascript literal
	 */
	public static function toJsArray ( $struct )
	{
		if ( is_array( $struct ) )
		{
			$pairs = NULL;
			foreach ( $struct as $key => $val )
				$pairs[] = "{$key}: \"{$val}\"";

			return "{ " . implode( ', ', $pairs ) . " }";
		}
		else
			return 'null';
	}

	/**
	 * Backward compatibility interface.
	 *
	 * @obsolete
	 */
	protected function generateJsArray ( $struct ) { return self::toJsArray( $struct ); }

	/**
	 * All UICMP layout objects should support this method to provide Javascript
	 * code for <head> element.
	 */
	abstract public function generateJs ( );
}

?>