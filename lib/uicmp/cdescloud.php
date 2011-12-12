<?php

/**
 * @file cdescloud.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . 'uicmp/uicmp.php';

/**
 * Contexts cloud, a subcomponent for application forms. Provides UI and
 * Javascript logic on per instance basis. It can be rendered separately, i.e.
 * outside _uicmp_layout scope, for such use there is not need to specify
 * parent.
 */
class cdescloud extends uicmp
{
	/**
	 * Array of _ctx object instances to be displayed in the cloud.
	 *
	 * @var array
	 */
	protected $ctxs = NULL;

	/**
	 * Prefix for _ctx instances HTML ID's.
	 *
	 * @var string
	 */
	protected $prefix = NULL;
	
	/**
	 * Messages displayed in case no contexts are available.
	 * 
	 * @var string 
	 */
	protected $error_msg = NULL;

	/**
	 * Constructor.
	 * 
	 * @param mixed $parent parent component instance, may be NULL
	 * @param string $id identifier of the component
	 * @param string $js_var name of client side instance variable
	 * @param array $ctxs array of all aplicable contexts
	 * @param string $prefix prefix for contexts' HTML ID's
	 */
	public function  __construct ( $parent, $id, $js_var, $ctxs, $prefix )
	{
		parent::__construct( $parent, $id );
		$this->type		= __CLASS__;
		$this->renderer	= CHASSIS_UICMP . 'cdes_cloud.html';
		$this->jsVar	= $js_var;
		$this->prefix	= $prefix;

		/**
		 * Specify own style for display.
		 */
		if ( is_array( $ctxs ) )
			foreach ( $ctxs as $id => $ctx )
			{
				$this->ctxs[$id] = $ctx;
				$this->ctxs[$id]->rend = '_tdum';
				$this->ctxs[$id]->htmlId = $this->prefix . '.' . $id;
				$this->ctxs[$id]->act = $this->jsVar . '.set( \'' . $id . '\' );';
			}
	}
	
	/**
	 * Sets error message.
	 *
	 * @param string $msg string to set as error message.
	 */
	public function setErrorMsg ( $msg ) { $this->error_msg = $msg; }

	/**
	 * Dummy implementation to conform abstract parent.
	 */
	public function  generateReqs ( ) { }

	/**
	 * Provides list of contexts.
	 *
	 * @return array
	 * @todo rewrite to use iterator pattern (getFirst/getNext)
	 */
	public function getCtxs ( ) { return $this->ctxs; }
	
	/**
	 * Provides error message.
	 *
	 * @return string
	 * @todo make use of some global framework i18n object
	 */
	public function getErrorMsg ( ) { return $this->error_msg; }
	
	
}

?>