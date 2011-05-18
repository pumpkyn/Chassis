/**
 * @file _srv.js
 * @package Chassis
 * @author giorno
 *
 * Background services provided to framework Javascript libraries.
 */

/**
 * Instance of this object should be owned by referenced object.
 */
function _srv_ev_listener ( )
{
	/**
	 * Indicates whether lister is listening or not.
	 */
	this.active = false;
	
	/**
	 * Intercepted event identifier, e.g. 'window.onresize'.
	 */
	this.event = '';

	/**
	 * Object referenced here must have 'handle' method and 'listening' property
	 * of type 'bool'.
	 */
	this.object = null;
}

function _srv_ev_listeners ( )
{
	this.listeners = new Array( );

	this.register = function ( listener )
	{
		this.listeners[this.listeners.length] = listener;
	};

	this.deregister = function ( listener )
	{
		// remove by event, object and method
	};

	this.handle = function ( event )
	{
		
		for ( i = 0; i < this.listeners.length; ++i )
		{
			if ( ( this.listeners[i].event == event ) && ( this.listeners[i].active == true ) )
				this.listeners[i].object.handle( event );
		}
	};
}

/**
 * Global instance of event handlers.
 */
var _srv_listeners = new _srv_ev_listeners( );

/**
 * Redirect browser window onResize event to framework handler.
 */
window.onresize = function ( )
{
	_srv_listeners.handle( 'window.onresize' );
}