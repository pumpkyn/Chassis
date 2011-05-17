<?php
/**
 * @file _wwg_registry.php
 * @author giorno
 * @package Chassis
 * @subpackage Apps
 *
 * Singleton holding definitions of web widgets layouts.
 */

require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';

class _wwg_registry
{
	/**
	 * Constant describing separator widget.
	 */
	const WWG_SEP		= 1;

	const POOL_MENU			= 'MFW_POOL_MENU';
	const POOL_FOOTER		= 'MFW_POOL_FOOTER';
	const POOL_BOTTOM		= 'MFW_POOL_BOTTOM';
	const POOL_DASHBOARD	= 'MFW_POOL_DASHBOARD';

	/**
	 * Singleton instance.
	 * 
	 * @var <_wwg_registry>
	 */
	private static $instance = NULL;

	/**
	 * Definitions of widget pools.
	 *
	 * @var <array>
	 */
	private $pools = NULL;

	/**
	 * Array of widget ids to serve as visual layout for widgets in the pool.
	 * 
	 * @var <array>
	 */
	private $layouts = NULL;

	/**
	 * Id of pool which is currently being iterated. See getFirst() and
	 * getNext().
	 * 
	 * @var <string>
	 */
	private $iter = NULL;

	protected function __construct ( )
	{
		$this->createPool( static::POOL_MENU );
		$this->createPool( static::POOL_FOOTER );
		$this->createPool( static::POOL_BOTTOM );
		$this->createPool( static::POOL_DASHBOARD );
	}

	protected function __clone ( ) { }

	/**
	 * Singleton interface.
	 *
	 * @return <_wwg_registry>
	 */
	public static function getInstance ( )
	{
		if ( is_null( static::$instance ) )
			static::$instance = new static( );

		return static::$instance;
	}

	/**
	 * If pool with given id does not exist, creat it.
	 *
	 * @param <string> $id pool Id
	 */
	public function createPool ( $id )
	{
		if ( !is_array( $this->pools ) || !array_key_exists( $id, $this->pools ) )
		{
			$this->pools[$id] = TRUE;
		}
	}

	/**
	 * Registers new widget into the pool.
	 *
	 * @param <string> $pool pool Id
	 * @param <string> $id id of widget
	 * @param <Wwg> $wwg reference to instance of the widget
	 */
	public function register ( $pool, $id, &$wwg )
	{
		if ( is_array( $this->pools ) && array_key_exists( $pool, $this->pools ) )
		{
			if ( !is_array( $this->pools[$pool] ) || !array_key_exists( $id, $this->pools[$pool] ) )
			{
				if ( !is_array( $this->pools[$pool] ) )
					$this->pools[$pool] = Array( );
				$this->pools[$pool][$id] = $wwg;
			}
		}
	}

	/**
	 * Loads 1D array with order of web widgets to be displayed in the pool.
	 * De facto teplate.
	 *
	 * @param <string> $pool pool Id
	 * @param <array> $layout array containing ids of widgets.
	 */
	public function setLayout ( $pool, $layout )
	{
		$this->layouts[$pool] = $layout;
	}

	/**
	 * Propagate instance to Smarty engine.
	 */
	public function render ( )
	{
		_smarty_wrapper::getInstance( )->getEngine( )->registerObject( 'MFW_OBJ_WWG_REGISTRY', $this, NULL, FALSE );
	}

	/**
	 * Returns reference to first widget in the pool and sets pointer to it.
	 *
	 * @param <string> $pool pool Id
	 * @return <Wwg>
	 */
	public function getFirst ( $pool )
	{
		/**
		 * No pools, no swimming.
		 */
		if ( !is_array( $this->pools ) || !array_key_exists( $pool, $this->pools ) )
		{
			$this->iter = NULL;
			return NULL;
		}

		/*
		 * If there is a layout, use it for traversing the pool.
		 */
		if ( is_array( $this->layouts ) && array_key_exists( $pool, $this->layouts ) )
		{
			$id = reset( $this->layouts[$pool] );

			if ( is_array( $this->pools[$pool] ) && array_key_exists( $id, $this->pools[$pool] ) )
			{
				$this->iter = $pool;
				return $this->pools[$pool][$id];
			}
			elseif ( is_array( $this->pools[$pool] ) )	// recover from broken layout
			{
				$this->iter = $pool;

				// skip gaps in layout
				while ( $id = next( $this->layouts[$pool] ) )
					if ( array_key_exists( $id, $this->pools[$pool] ) )
						return $this->pools[$pool][$id];
			}
			
			$this->iter = NULL;
			return NULL;
		}

		/*
		 * There is no custom layout, widgets are served as they were registered.
		 */
		$this->iter = $pool;
		return reset( $this->pools[$pool] );
	}

	/**
	 * Returns next widget in the pool which is currently iterated.
	 *
	 * @return <Wwg>
	 */
	public function getNext ( )
	{
		if ( is_null( $this->iter ) )
			return false;

		/*
		 * If there is a layout, use it for traversing the pool.
		 */
		if ( is_array( $this->layouts ) && array_key_exists( $this->iter, $this->layouts ) )
		{
			$id = next( $this->layouts[$this->iter] );
			
			if ( ( $id !== FALSE ) && array_key_exists( $id, $this->pools[$this->iter] ) )
				return $this->pools[$this->iter][$id];

			$this->iter = NULL;
			return NULL;
		}

		return next( $this->pools[$this->iter] );
	}
}

?>