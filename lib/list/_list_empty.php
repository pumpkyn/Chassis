<?php

require_once CHASSIS_LIB . 'list/_list_i18n.php';

/**
 * @file _list_empty.php
 * @author giorno
 * @package Chassis
 * @subpackage List
 * 
 * Data abstraction for UI element showing empty result set message and offers
 * options.
 */
class _list_empty extends _list_i18n
{
	/**
	 * Array of options.
	 * 
	 * @var array 
	 */
	protected $options = NULL;
	
	/**
	 * Message displayed on the top of element.
	 * 
	 * @var string 
	 */
	protected $message = NULL;
	
	/**
	 * Constructor.
	 * 
	 * @param string $message informational text to be displayed above list of options
	 * @param
	 */
	public function __construct( $message, $lang = 'en' )
	{
		parent::__construct( $lang );
		$this->message = $message;
	}
	
	/**
	 * Adds new option to the list
	 * 
	 * @param string $display text to display
	 * @param string $action Javascript code to be executed in onClick event
	 * @param string $class additional stylesheet
	 */
	public function add ( $display, $action, $class = '' ) { $this->options[] = Array( 'display' => $display, 'action' => $action, 'class' => $class ); }
	
	/**
	 * Read interface for options array.
	 * 
	 * @return array 
	 */
	public function getOptions ( ) { return $this->options; }
	
	/**
	 * Read interface for message text.
	 * 
	 * @return string 
	 */
	public function getMsg ( ) { return $this->message; }
	
	/**
	 * Calls Smarty wrapper interface to render element instance.
	 */
	public function render ( )
	{
		_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'USR_LIST_EMPTY', $this );
		_smarty_wrapper::getInstance( )->setDir( CHASSIS_UI . 'list' );
		_smarty_wrapper::getInstance( )->setContent( 'empty.html' );
		_smarty_wrapper::getInstance( )->render( );
	}
}

?>