<?php

/**
 * @file _uicmp_comp.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 *
 * Common ancestor to all UICMP components.
 */

require_once CHASSIS_LIB . 'uicmp/_vcmp_comp.php';

abstract class _uicmp_comp extends _vcmp_comp
{
	/**
	 * Type of the item. Each instance/class is responsible for setting this
	 * value.
	 *
	 * @var string
	 */
	protected $type = null;

	/**
	 * Ide of the element. Its HTML id is composed from this.
	 *
	 * @var string
	 */
	public $id = NULL;

	/**
	 * Path to Smarty template rendering the item. This is set in the UICMP
	 * components classes.
	 *
	 * @var string
	 */
	protected $renderer = NULL;

	/**
	 * Contructor.
	 *
	 * @param _uicmp_comp $parent parent component instance
	 * @param string $id identifier of the component
	 */
	public function  __construct ( &$parent, $id = NULL )
	{
		parent::__construct( $parent );
		$this->id = $id;
	}

	/**
	 * RO interface to $type member. Return value identifies default template
	 *
	 * @return string
	 */
	public function type ( ) { return $this->type; }

	/**
	 * Returns HTML id for the element. Lowercase 'm' letter at the beginning of
	 * the string is to conform specification: http://www.w3.org/TR/html4/types.html#type-id
	 * 
	 * @todo produce ID's as hashes to obsfucate content
	 *
	 * @return string
	 */
	public function getHtmlId ( )
	{
		$first = strtoupper( $this->type[0] );
		return ( ( ( $first < 'A' ) || ( $first > 'Z' ) ) ? 'm' : '' ) . $this->type . ':' . $this->id;
	}

	/**
	 * Clibs up parents' hierarchy and returns requirer instance from top parent
	 * (_uicmp_layout).
	 *
	 * @return _requirer
	 */
	public function getRequirer ( ) { return $this->parent->getRequirer( ); }

	/**
	 * Path to Smarty template responsible for item rendering.
	 *
	 * @return string
	 */
	public function getRenderer ( ) { return $this->renderer; }

	/**
	 * Returns Id of the item.
	 *
	 * @return string
	 */
	public function getId ( ) { return $this->id; }

}

?>