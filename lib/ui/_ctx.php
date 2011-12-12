<?php

/**
 * @file _ctx.php
 * @author giorno
 * @package Chassis
 * @subpackage UI
 * @license Apache License, Version 2.0, see LICENSE file
 */

/** 
 * Structure used for visual representation of single context. Its members are
 * public so they can be accessed directly from Smarty templates.
 */
class _ctx
{
	/**
	 * Identifier of the context. Used for Javascript identification and to
	 * generate implicit HTML Id.
	 *
	 * @var <string>
	 */
	public $id = NULL;

	/**
	 * Explicit HTML id for the ctx badge.
	 *
	 * @var <string>
	 */
	public $htmlId = NULL;

	/**
	 * Scheme, the style of the context badge.
	 *
	 * @var <string>
	 */
	public $sch = NULL;

	/**
	 * Visual scheme, the style of the context badge. In most cases it has same
	 * value as $sch member. Can be overriden, e.g. for cloud component.
	 *
	 * @var <string>
	 */
	public $rend = NULL;

	/**
	 * Text to be displayed in the badge.
	 *
	 * @var <string>
	 */
	public $disp = NULL;

	/**
	 * Description of context.
	 * @var <string>
	 */
	public $desc = NULL;

	/**
	 * Javascript code to be executed in onClick event.
	 *
	 * @var <string>
	 */
	public $act = NULL;

	/**
	 * Constructor of the structure.
	 *
	 * @param <string> $id identifier of the context instance
	 * @param <string> $sch style of the badge
	 * @param <string> $disp text to display
	 * @param <string> $act action to perform
	 */
	public function __construct ( $id, $sch, $disp, $desc = NULL, $act = NULL )
	{
		$this->id	= $id;
		$this->sch	= $sch;
		$this->rend	= '_tsch_' . $sch;
		$this->disp	= $disp;
		$this->desc = $desc;
		$this->act	= $act;
	}

	/**
	 * Provides HTML Id for the badge and its components.
	 *
	 * @return <string>
	 */
	public function getHtmlId ( )
	{
		if ( is_null( $this->htmlId ) )
			return 'm_ctx:' . $this->id;
		else
			return $this->htmlId;
	}
    
}

?>