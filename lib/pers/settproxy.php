<?php

/**
 * @file settproxy.php
 * @author giorno
 * @package Chassis
 * @subpackage Persistence
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\pers;

/**
 * Object holding references to settings providers and respective data for the
 * TUI configuration.
 */
class settproxy
{
	/**
	 * Reference to settings object used for access to list length (=page size)
	 * value. This is used to set up resizer UI and also to update the value as
	 * a result of resizer UI operation (clicking to change the page size).
	 * @var _settings
	 */
	protected $llensett = NULL;
	
	/**
	 * Key value for the list length (=page size) in the $llensett member.
	 * @var string
	 */
	protected $llenkey = NULL;
	
	/**
	 * Reference to settings object used for access to list configuration (which
	 * is serialized PHP array) of the TUI of the particular Persistence
	 * instance. Unlike list length setting, key for this one is derived from
	 * the namespace constant and the table name by concatenation of
	 * 'io.creat.chassis.pers.' and the table name.
	 * @var _settings
	 */
	protected $lcfgsett = NULL;
	
	/**
	 * Reference to Persistence instance.
	 * @var \io\creat\chassis\pers\instance
	 */
	protected $pi = NULL;
	
	/**
	 * Constructor for the instance.
	 * @param \io\creat\chassis\pers\instance $pi reference to persistence instance
	 * @param _settings $llensett reference to settings object handling page size
	 * @param string $llenkey key value for the page size setting 
	 * @param \_settings $lcfgsett reference to settings object handling list configuration
	 */
	public function __construct ( &$pi, &$llensett, $llenkey, &$lcfgsett )
	{
		$this->pi		= $pi;
		$this->llensett	= $llensett;
		$this->llenkey	= $llenkey;
		$this->lcfgsett	= $lcfgsett;
	}
	
	/**
	 * Getter/setter for the list configuration setting value. Key name is not
	 * cached, because it is used only once in an object lifetime
	 * @param string $new serialized array of list configuration array, if NULL, method acts as getter
	 * @return string serialized setting value
	 */
	public function lcfg ( $new = NULL )
	{
		if ( !is_null( $new ) )
			$this->lcfgsett->saveOne( 'io.creat.chassis.pers.' . $this->pi->name( ), $new );
		else
			return $this->lcfgsett->get( 'io.creat.chassis.pers.' . $this->pi->name( ) );
	}
	
	/**
	 * getter/setter of list length setting value.
	 * @param int $new new value to be saved into settings, if NULL, method acts as getter
	 * @return type 
	 */
	public function llen ( $new = NULL )
	{
		if ( !is_null( $new ) )
			$this->llensett->saveOne( $this->llenkey, $new );
		else
			return $this->llensett->get( $this->llenkey );
	}
}

?>