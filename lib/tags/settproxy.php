<?php

/**
 * @file settproxy.php
 * @author giorno
 * @package Chassis
 * @subpackage Tags
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\tags;

require_once CHASSIS_LIB . 'pers/settproxy.php';

/**
 * Extension of Persitence Settings Proxy to allow save and load of application
 * specific settings. This creates methods to access settings storage area in
 * the settings table. Primary users for this class are Tags Persistence
 * objects, but can be use also by other (app/user specific) derivations of
 * Persistence.
 */
class settproxy extends \io\creat\chassis\pers\settproxy
{
	/**
	 * Proxy method for saving value into local namespace at 'U' (user) scope.
	 * @param string $key setting identifier
	 * @param string $val setting value 
	 */
	public function setl ( $key, $val ) { $this->lsett->saveOne( $key, $val ); }
	
	/**
	 * Proxy method for loading value from local namespace.
	 * @param string $key setting identifier
	 * @return string
	 */
	public function getl ( $key ) { return $this->lsett->get( $key ); }
}

?>