<?php

/**
 * @file _list_cfg.php
 * @author giorno
 * @package Chassis
 * @subpackage List
 * @license Apache License, Version 2.0, see LICENSE file
 */
 
/**
 * Class responsible for management of list of search results configuration
 * (page, order, ...). De facto wrapper of settings instance and methods
 * responsible for list configuration.
 */
class _list_cfg
{
	/**
	 * Reference to instance of settings handling object.
	 * 
	 * @var <_settings> 
	 */
	protected $settings = NULL;

	protected $data = NULL;
	
	protected $widths = NULL;

	protected $key = NULL;

	public function __construct ( $settings, $key )
	{
		$this->settings		= $settings;
		$this->key			= $key;
		//$this->set( $keywords, $order, $dir, $page );
		$this->load( );
	}

	protected function set ( $keywords, $order, $dir, $page )
	{
		$this->data['k']	= $keywords;
		$this->data['o']	= $order;
		$this->data['d']	= $dir;
		$this->data['p']	= (int)$page;
	}

	public function get ( ) { return $this->data; }

	//private function serialize ( ) { return serialize( $this->data ); }

	//private function unserialize ( $raw ) { return unserialize( $raw ); }

	public function save ( $keywords = '', $order = 'default', $dir = 'ASC', $page = 1 )
	{
		$this->set( $keywords, $order, $dir, $page );
		$this->settings->saveOne( $this->key, serialize( $this->data ) );
	}

	public function load ( ) { $this->data = unserialize( $this->settings->get( $this->key ) ); }

	public function getWidth( $index ) { return $this->widths[$index]; }
}

?>