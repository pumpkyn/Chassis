<?php

/**
 * @file _vcmp_comp.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * @todo rewrite whole UICMP to automatically register to parent in case that
 * proper action is known (by instanceof operator on parent value)
 * 
 * @todo rename generateJs to more proper name, as it does not generate only
 * Javascript code
 */

/** 
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
	 * Ajax server URL.
	 * 
	 * @var string
	 */
	protected $url = NULL;
	
	/**
	 * Ajax request base set of parameters. Associative array.
	 * 
	 * @var array
	 */
	protected $params = NULL;

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
	 * Sets Ajax related properties.
	 * 
	 * @param string $url string
	 * @param array $params array of common request parameters
	 */
	public function setAjaxProperties ( $url, $params )
	{
		$this->url		= $url;
		$this->params	= $params;
	}
	
	/**
	 * Returns Javascript initialization code for associative array or Ajax
	 * request properties.
	 * 
	 * @return string 
	 */
	public function getJsAjaxParams ( ) { return self::toJsArray( $this->params ); }

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
	 * @param array $struct input structure, PHP array
	 * @return string Javascript literal
	 */
	public static function toJsArray ( $struct )
	{
		if ( is_array( $struct ) )
		{
			$pairs = NULL;
			foreach ( $struct as $key => $val )
			{
				/**
				 * Recursive application onto subarrays.
				 */
				if ( is_array( $val ) )
					$pairs[] = "{$key}: " . self::toJsArray( $val );
				else
					$pairs[] = "{$key}: \"{$val}\"";
			}

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