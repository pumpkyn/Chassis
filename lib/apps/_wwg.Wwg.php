<?php
/**
 * @file _wwg.Wwg.php
 * @author giorno
 * @package Chassis
 * @subpackage Apps
 *
 * Interface and common functionality for web widget derivations.
 */

abstract class Wwg
{
	/**
	 * Path to template generating RR content.
	 * 
	 * @var <string>
	 */
    protected $template = NULL;

	/**
	 * Id of application handling this widget.
	 *
	 * @var <string>
	 */
	protected $appId = NULL;

	/**
	 * Identifier of widget.
	 *
	 * @var <string>
	 */
	protected $id = NULL;

	public function getId ( )
	{
		return $this->id;
	}

	public function getTemplate ( )
	{
		return $this->template;
	}

}

?>