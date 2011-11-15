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
	 * value. This is used to set up resizer UI, to update the value as a result
	 * of resizer UI operation (clicking to change the page size) and to extract
	 * pager half size value. Usually global settings, hence G.
	 * @var _settings
	 */
	protected $gsett = NULL;
	
	/**
	 * Key value for the list length (=page size) in the $gsett member.
	 * @var string
	 */
	protected $llenkey = NULL;
	
	/**
	 * Key value for the pager half size setting in the $gsett member.
	 * @var string
	 */
	protected $phkey = NULL;
	
	/**
	 * Reference to settings object used for access to list configuration (which
	 * is serialized PHP array) of the TUI of the particular Persistence
	 * instance. Unlike list length setting, key for this one is derived from
	 * the namespace constant and the table name by concatenation of
	 * 'io.creat.chassis.pers.' and the table name. Usually app and user
	 * specific, hence L.
	 * @var _settings
	 */
	protected $lsett = NULL;
	
	/**
	 * Reference to Persistence instance.
	 * @var \io\creat\chassis\pers\instance
	 */
	protected $pi = NULL;
	
	/**
	 * Constructor for the instance.
	 * @param _settings $gsett reference to settings object handling page size
	 * @param \_settings $lsett reference to settings object handling list configuration
	 * @param string $llenkey key value for the page size setting 
	 * @param string $phkey key value for the pager half size setting
	 */
	public function __construct ( &$gsett, &$lsett, $llenkey, $phkey )
	{
		$this->gsett	= $gsett;
		$this->llenkey	= $llenkey;
		$this->lsett	= $lsett;
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
			$this->lsett->saveOne( 'io.creat.chassis.pers.' . $this->pi->name( ), $new );
		else
			return $this->lsett->get( 'io.creat.chassis.pers.' . $this->pi->name( ) );
	}
	
	/**
	 * Getter/setter of list length setting value.
	 * @param int $new new value to be saved into settings, if NULL, method acts as getter
	 * @return string
	 */
	public function llen ( $new = NULL )
	{
		if ( !is_null( $new ) )
			$this->gsett->saveOne( $this->llenkey, $new );
		else
			return $this->gsett->get( $this->llenkey );
	}
	
	/**
	 * Getter of pager half size setting value.
	 * @return string 
	 */
	public function ph ( ) { return $this->gsett->get( $this->phkey ); }
	
	/**
	 * Setter for persistence instance. Should be called from instance itself
	 * to populate with proper reference.
	 * @param \io\creat\chassis\pers\instance $pi reference to instance itself (i.e. $this)
	 */
	public function pi( &$pi ) { $this->pi = $pi; }
}

?>