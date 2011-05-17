<?php

/**
 * @file _app_registry.php
 * @author giorno
 * @package Chassis
 * @subpackage Apps
 * 
 * Static class keeping information about applications using the framework and
 * providing interface to manage the registry. This is a central repository for
 * all applications instances, so it can be used to call applications from
 * other applications.
 */

require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';

class _app_registry
{
	/**
	 * Event indicating that settings have changed.
	 */
	const EV_UPDATEDSETT		= 1;

	/**
	 * Event indicating instance was registered.
	 */
	const EV_REGISTERED			= 2;

	/**
	 * Singleton pattern instance.
	 * @var <_app_registry> 
	 */
	private static $instance = NULL;

	/**
	 * Associative array of applications instances.
	 *
	 * @var <array>
	 */
	private $apps = NULL;

	/**
	 * Id of application used as default, fallback, application.
	 * 
	 * @var <string>
	 */
	private $default = NULL;

	/**
	 * Id of executed application. There should be only one exec in object
	 * lifetime.
	 *
	 * @var <string>
	 */
	private $executed = NULL;

	/**
	 * Array containing paths to Javascript libraries required by applications.
	 *
	 * @var <array>
	 */
	protected $jsLibs = NULL;

	/**
	 * Array containing paths to CSS stylesheets required by applications.
	 *
	 * @var <array>
	 */
	protected $cssLinks = NULL;

	/**
	 * Javascript codes to be executed in <head> element. This is facility to
	 * pass initialization and configuration of certain objects.
	 *
	 * @var <array>
	 */
	protected $jsPlain = NULL;

	/**
	 * Array of Javascript codes to be executed in the <body> onLoad event. This
	 * member use different structure than $jsLibs and $cssLinks. Keys are
	 * irrelevant here. Only values matter.
	 *
	 * @var <array>
	 */
	protected $onLoad = NULL;

	/**
	 * Array of paths to templates which have to be rendered directly under the
	 * <body> elements (as its childs). Indented to be used for SkyDome feature,
	 * etc.
	 *
	 * @var <array> 
	 */
	protected $bodyChildren = NULL;

	/**
	 * Prevent access to instantiation and cloning.
	 */
	private function __construct ( ) { }
	private function __clone ( ) { }

	/**
	 * Singleton interface.
	 *
	 * @return <_app_registry>
	 */
	public static function getInstance( )
	{
		if ( static::$instance == NULL )
		{
			static::$instance = new static( );
		}

		return static::$instance;
	}

	/**
	 * Registers new application into the registry and informs it about this
	 * status change.
	 *
	 * @param <App> $app application instance
	 */
	public function register( &$app )
	{
		if ( ( $app != null ) && ( ( !is_array( $this->apps) ) || !array_key_exists( $app->getId( ), $this->apps ) ) )
		{
			$this->apps[$app->getId( )] = $app;
			$app->event( static::EV_REGISTERED );
		}
	}

	/**
	 * Register new CSS file withing the registry. If path to the resource does
	 * not exist in internal array, it will be added.
	 *
	 * @param <string> $path path to the resource
	 * @param <string> $by application identifier, used for comments
	 */
	public function requireCss ( $path, $by )
	{
		if ( !is_array( $this->cssLinks ) || !array_key_exists( $path, $this->cssLinks ) )
			$this->cssLinks[$path] = $by;
	}

	/**
	 * Register new Javascript library withing the registry. If path to the
	 * resource does not exist in internal array, it will be added.
	 *
	 * @param <string> $path path to the resource
	 * @param <string> $by application identifier, used for comments
	 */
	public function requireJs ( $path, $by )
	{
		if ( !is_array( $this->jsLibs ) || !array_key_exists( $path, $this->jsLibs ) )
			$this->jsLibs[$path] = $by;
	}

	/**
	 * Register new Javascript code toe be executed in page <head> element.
	 * Code is required to be passed by reference, so it can be updated till
	 * it is needed for rendering.
	 *
	 * @param <string> $code Javascript code
	 */
	public function requireJsPlain ( $code)
	{
		if ( !is_array( $this->jsPlain ) || !in_array( $code, $this->jsPlain ) )
			$this->jsPlain[] = $code;
	}

	/**
	 * Register new Javascript code toe be executed in <body> element onLoad
	 * event. Logic prevents double insertion of same code.
	 *
	 * @param <string> $code Javascript code
	 */
	public function requireOnLoad ( $code)
	{
		if ( !is_array( $this->onLoad ) || !in_array( $code, $this->onLoad ) )
			$this->onLoad[] = $code;
	}

	/**
	 * Puts Smarty rendering requirements into internal array. Stored path is
	 * used for including templates directly into <body> element.
	 *
	 * @param <string> $path path to Smarty template
	 * @param <string> $by identifier of software block requiring the resource
	 */
	public function requireBodyChild ( $path, $by )
	{
		if ( !is_array( $this->bodyChildren ) || !array_key_exists( $path, $this->bodyChildren ) )
			$this->bodyChildren[$path] = $by;
	}

	/**
	 * Returns whether application with given id exists within the repository
	 * or not, i.e. it is registered or not.
	 * 
	 * @param <string> $id application id
	 * @return <bool> tells if app is registered or not
	 */
	public function exists ( $id ) { return ( ( is_array( $this->apps) ) && ( array_key_exists( $id, $this->apps ) ) ); }

	/**
	 * Executes exec() method on application given by its id.
	 *
	 * @param <string> $id application id
	 * @return <bool> success, false if call fails
	 */
	public function exec( $id )
	{
		if ( !is_array( $this->apps) ) return false;

		if ( ( !is_null( $id ) && array_key_exists( $id, $this->apps ) ) )
		{
			$this->executed = $id;
			return $this->apps[$id]->exec( );
		}
		elseif ( array_key_exists( $this->default, $this->apps ) )
		{
			$this->executed = $this->default;
			return $this->apps[$this->default]->exec( );
		}
		else
			return false;
	}

	/**
	 * Sets application identifier passed as parameter to be default, a fallback,
	 * application. This can be performed only once, first call of this method
	 * prevents future changes.
	 *
	 * @param <string> $id application id
	 * @return <bool> success
	 */
	public function setDefault ( $id )
	{
		if ( $this->default == NULL )
		{
			$this->default = $id;
			return true;
		}
		else
			return false;
	}

	/**
	 * Returns value of executed application. It can be used to mark icon of app
	 * as an active one.
	 *
	 * @return <string> id of executed app
	 */
	public function getExecutedId ( )
	{
		return $this->executed;
	}

	/**
	 * Returns Javascript libraries/scripts.
	 *
	 * @return <array> reference to array of registered libraries
	 */
	public function getJs ( ) { return $this->jsLibs; }

	/**
	 * Returns Javascript codes to be planted into <head> element.
	 *
	 * @return <array> reference to array of registered scripts
	 */
	public function getJsPlain ( )
	{
		$code = NULL;

		if ( is_array( $this->jsPlain ) )
			foreach ( $this->jsPlain as $piece )
				$code .= $piece;

		return $code;
	}

	/**
	 * Returns CSS links.
	 *
	 * @return <array> reference to array of registered stylesheets
	 */
	public function getCss ( ) { return $this->cssLinks; }

	/**
	 * Returns <body> required children templates.
	 *
	 * @return <array> reference to array of registered template paths
	 */
	public function getBodyChildren ( ) { return $this->bodyChildren; }

	/**
	 * Composes and returns <body> onLoad event code to be executed.
	 *
	 * @return <string> Javascript code
	 */
	public function getOnLoad ( )
	{
		$code = NULL;
		
		if ( is_array( $this->onLoad ) )
			foreach ( $this->onLoad as $piece )
				$code .= $piece;

		return $code;
	}

	/**
	 * Populates Smarty template engine with objects required in render phase.
	 *
	 * @param <Smarty> $smarty Smarty class instance
	 * @todo change deployment to static value when deploying
	 */
	public function render (  )
	{
		_smarty_wrapper::getInstance( )->getEngine( )->registerObject( 'MFW_OBJ_APPS_REGISTRY', $this, NULL, FALSE );
		_smarty_wrapper::getInstance( )->getEngine( )->assign( 'MFW_DEPLOYMENT_MAGIC', time( ) );
	}

	/**
	 * Broadcasts event to all registered applications. Typical event is update
	 * of settings so application will reload their resources.
	 *
	 * @param <int> $event
	 */
	public function signal ( $event )
	{
		if ( is_array( $this->apps ) )
			foreach ( $this->apps as $app )
				$app->event( $event );
	}

	/**
	 * Returns reference to first application and resets internal pointer.
	 *
	 * @return <mixed> reference to app instance or NULL;
	 */
	public function getFirst ( )
	{
		if ( is_array( $this->apps ) )
			return reset( $this->apps );

		return NULL;
	}

	/**
	 * Moves internal pointer one step forward and returns reference to
	 * application on actual position.
	 *
	 * @return <mixed> reference to app instance or NULL;
	 */
	public function getNext ( )
	{
		if ( is_array( $this->apps ) )
			return next( $this->apps );

		return NULL;
	}

	/**
	 * Returns reference to app by its id.
	 *
	 * @param <string> $id application identifier
	 */
	public function getById( $id )
	{
		/*debug_print_backtrace();
		echo "\n\n";*/
		return $this->apps[$id];
	}
}

?>