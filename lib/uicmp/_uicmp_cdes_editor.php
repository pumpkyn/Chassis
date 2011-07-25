<?php

/**
 * @file _uicmp_cdes_editor.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'ui/_ctx.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_comp.php';

/**
 * Context editor component for Context Displaying and Editing Solution (CDES).
 *
 * @todo automatic update of the form caption
 */
class _uicmp_cdes_editor extends _uicmp_comp
{
	/**
	 * Ajax server URL. Used from client side logic to send requests.
	 *
	 * @var <string>
	 */
	protected $url = NULL;

	/**
	 * Additional parameters for Ajax requests. These should identify request
	 * for Ajax server to create proper channel for communication of data
	 * between client and server implementations.
	 *
	 * @var <array>
	 */
	protected $params = NULL;

	/**
	 * Context instance for preview badge in the form.
	 *
	 * @var <_ctx>
	 */
	protected $ctx = NULL;

	/**
	 * Constructor.
	 *
	 * @param <_uicmp_body> $parent reference to parent component instance
	 * @param <string> $id identifier of the component
	 * @param <string> $url URL of Ajax server implementation
	 * @param <array> $params parameters for Ajax request
	 */
	public function  __construct ( &$parent, $id, $url, $params )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->url		= $url;
		$this->params	= $params;
		$this->renderer = CHASSIS_UI . 'uicmp/cdes_editor.html';
		$this->ctx		= new _ctx( $this->id . '.Preview' , 'dar', 'dar' );
		$this->jsPrefix	= '_uicmp_cdes_i_';
	}

	/**
	 * Registers requirements of the component. Called from parent component.
	 */
	public function  generateJs ( )
	{
		$requirer = $this->getRequirer( );
		if ( !is_null( $requirer ) )
			$requirer->call( _uicmp_layout::RES_CSS, array( $requirer->getRelative( ) . 'css/_ctx.css', __CLASS__ ) );
	}

	/**
	 * Provides data for initial context badge in the editor form.
	 */
	public function getCtx() { return $this->ctx; }
}

?>