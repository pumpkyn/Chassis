<?php

/**
 * @file vcmp.php
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

namespace io\creat\chassis\uicmp;

/** 
 * Virtual component interface, common ancestor to all components in UICMP
 * framework, virtual and/or visual.
 */
abstract class vcmp
{
	/**
	 * Reference to UICMP parent widget.
	 *
	 * @var mixed
	 */
	protected $parent = NULL;

	/**
	 * Implementation specific flags. May control behaviour of particular class.
	 *
	 * @var int
	 */
	protected $flags = 0;

	/**
	 * Name of Javascript variable holding component client side instance.
	 *
	 * @var string
	 */
	protected $jsVar = NULL;

	/**
	 * Internal counter to server as cache for generated Javascript variable
	 * names.
	 *
	 * @var int
	 */
	protected static $lastId = 0;

	/**
	 * Prefix part of Javascript variable name. Static member $lastId is
	 * appended to this prefix to form Javascript variable name.
	 *
	 * @var string
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
	 * @param mixed $parent reference to parent element
	 */
	public function __construct( &$parent ) { $this->parent = $parent; }

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
	 * Sets particular flag on.
	 * 
	 * @param int $flag flag value
	 */
	public function setFlag( $flag ) { $this->flags = $this->flags | $flag; }
	
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
	 * Detects if particular flag is set for the component.
	 *
	 * @param int $flag
	 * @return bool
	 */
	public function isFlagSet( $flag ) { return ( $this->flags & $flag ) != 0; }
	
	/**
	 * Returns Javascript initialization code for associative array or Ajax
	 * request properties.
	 * 
	 * @return string 
	 */
	public function getJsAjaxParams ( ) { return self::toJsArray( $this->params ); }

	/**
	 * Returns reference to parent element.
	 *
	 * @return mixed
	 */
	public function getParent ( ) { return $this->parent; }

	/**
	 * Read interface and on-demand instantiation of client side Javascript
	 * variable.
	 *
	 * @return string
	 */
	public function getJsVar ( )
	{
		if ( is_null( $this->jsVar ) )
			$this->jsVar = ( ( is_null( $this->jsPrefix ) ) ? 'cmp_' : $this->jsPrefix ) . '_' . static::$lastId++;

		return $this->jsVar;
	}

	/**
	 * All UICMP layout objects should support this method to generate client
	 * side requirements, i.e. stylesheets, Javascript libraries, scripts, plain
	 * code, etc..
	 */
	abstract public function generateReqs ( );
}

?>