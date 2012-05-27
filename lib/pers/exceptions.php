<?php

// vim: ts=4

/**
 * @file exception.php
 * @author giorno
 * @package Chassis
 * @subpackage Persistence
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\pers;

/**
 * General exception used for persistence instance.
 */
class exception extends \Exception { }

class invvalexception extends exception
{
	/**
	 * Key of UICMP indicator message to be shown in the UI.
	 * @var string
	 */
	protected $indcode = 'e_unknown';
	
	/**
	 * Constructor.
	 * @param string $indcode key of (UI) indicator message
	 * @param string $message custom message (not used)
	 */
	public function __construct ( $indcode, $message = null )
	{
		parent::__construct( $message );
		$this->indcode = $indcode;
	}
	
	/**
	 * Getter of indicator code.
	 * @return string
	 */
	public function getIndCode ( ) { return $this->indcode; }
}

?>
