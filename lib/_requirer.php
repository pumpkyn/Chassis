<?php

/**
 * @file _requirer.php
 * @author giorno
 * @package Chassis
 *
 * Object encapsulating resource requirements. This aims to be user independent.
 */

class _requirer
{
	/**
	 * Identifier of requirer.
	 * 
	 * @var <string>
	 */
	protected $id = NULL;
	
	/**
	 * Array of callbacks, indexed by resource.
	 * 
	 * @var <array> 
	 */
	protected $cbs = NULL;
	
	/**
	 * Relative path (from browser perspective) to be prepended to resources.
	 *
	 * @var <string>
	 */
	protected $relative = NULL;

	/**
	 * Constructor. Sets up requirer.
	 *
	 * @param <string> $id identification of requirer
	 * @param <string> $relative relative path to resources
	 */
	public function __construct ( $id, $relative = NULL )
	{
		$this->id = $id;
		$this->relative = $relative;
	}

	/**
	 * Requires resource.
	 *
	 * @param <mixed> $res resource type identifier
	 * @param <mixed> $arg argument, can be array of arguments for registered callback
	 * @return <mixed> false of failure, otherwise return value of callback
	 */
	public function call ( $res, $arg )
	{
		/**
		 * If it exists, it is callable. See setCb().
		 */
		if ( is_array( $this->cbs ) && array_key_exists( $res, $this->cbs ) )
		{
			if ( is_array( $arg ) )
				return call_user_func_array ( $this->cbs[$res], $arg );
			else
				return call_user_func ( $this->cbs[$res], $arg );
		}

		return false;
	}

	/**
	 * Set callback for particular resource. Only derived objects can use this
	 * method.
	 *
	 * @param <mixed> $res resource type identifier
	 * @param <callback> $cb function to call, should conform PHP callback format
	 */
	protected function setCb ( $res, $cb )
	{
		if ( is_callable( $cb ) )
			$this->cbs[$res] = $cb;
	}

	/**
	 * Returns relative path to be prepended to resources.
	 * 
	 * @return <string>
	 */
	public function getRelative ( )
	{
		return $this->relative;
	}
}

?>