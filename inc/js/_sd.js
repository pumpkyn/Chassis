/**
 * @file _sd.js
 * @package Chassis
 * @author giorno
 *
 * @requires _srv.js
 * @requires browserdetect.js
 *
 * Library providing UI objects.
 *
 * @todo SkyDome's z-index has to be highest for the one used for default dialogs.
 */

/**
 * Minimal distance between rim of SkyDome (document) and widget edge.
 */
var _sd_padding = 64;

/**
 * Parking position coordinate value for "invisible widgets".
 */
var _sd_parking = -2048;

/**
 * SkyDome dialogs width is computed from width of workspace. This constant is
 * lowest allowed value of their width in pixels.
 */
var _sd_dlg_minw = 360;

/**
 * Common widget - ancestor/interface for all widgets. Contains base features.
 */
function _wdg ( )
{
	/*
	 * HTML Id of container element.
	 */
	this.html_id = null;

	/*
	 * Structure holding all subwidgets, which can be put and displayed on the
	 * widget.
	 */
	this.items = Array( );

	this.show = function ( ) { };
	this.hide = function ( ) { };

	/*
	 * Method responsible for updating geometry.
	 */
	this.resize = function ( )
	{

	};
}

/*
 * SkyDome inherits from Common widget.
 */
_sd_dome.prototype = new _wdg;

/**
 * Virtual board used for explicit widgets emulating modal dialogs or to provide
 * dashboard feature.
 */
function _sd_dome ( id )
{
	/**
	 * Identifier of Sky Dome instance.
	 */
	this.id = id;

	/**
	 * HTML Id for container.
	 */
	this.html_id = '_sd_domeInst_' + this.id;

	/**
	 * Building HTML container.
	 */
	this.el = document.createElement( 'div' );
	this.el.setAttribute( 'id', this.html_id );
	this.el.className = '_sd_dome';
	//this.el.innerHTML = 'www';

	/**
	 * Apparently it is not that easy to append directly into body tag.
	 */
	//this.frag = document.createDocumentFragment( );
	//this.frag.appendChild( this.el );
	document.body.appendChild( this.el );

	/**
	 * Registering intercepted events.
	 */
	this.listener = new _srv_ev_listener( );
	this.listener.event = 'window.onresize';
	this.listener.object = this;
	this.listener.active = false;
	_srv_listeners.register( this.listener );
	
	/**
	 * Events processing.
	 */
	this.handle = function ( event )
	{
		if ( event == 'window.onresize' )
			this.resize( true );
	};

	this.park = function ( )
	{
		this.el.style.left			= _sd_parking;
	};

	/**
	 * Hide Sky Dome canvas.
	 */
	this.hide = function ( )
	{
		this.listener.active		= false;
		this.el.style.visibility	= 'hidden';
		this.el.style.display		= 'none';
		this.park( );
	};

	/**
	 * Show Sky Dome.
	 */
	this.show = function ( )
	{
		//alert('YnDlg.show');
		this.el.style.left			= '0px';
		this.el.style.top			= '0px';
		this.el.style.visibility	= 'visible';
		this.el.style.display		= 'block';
		this.resize( );

		this.listener.active		= true;
	};

	/**
	 * Method to be responsible for updating dimensions of Sky Dome.
	 */
	this.resize = function ( recursive )
	{
		/**
		 * Shrinking SkyDome so it will not interfere with body dimensions.
		 */
		this.park( );
		//this.el.style.width			= '0px';
		//this.el.style.height		= '0px';

		if ( recursive )
			for ( i = 0; i < this.items.length; ++i )
			{
				this.items[i].park( );
				this.items[i].render( );
		//		this.items[i].resize( );
			}

		//alert(document.body.offsetWidth + ':' + getDocHeight() );
		this.el.style.width			= document.body.offsetWidth + 'px';
		this.el.style.height		= getDocHeight() + 'px';

		//this.el.style.left			= '0px';

		// reposition and recomputation positions and sizes of items
		if ( recursive )
			for ( i = 0; i < this.items.length; ++i )
			{
	//			this.items[i].park( );
				this.items[i].resize( );
			}

		/**
		 * Try and error proven inferiority of MSIE 7.0.
		 */
		if ( BrowserDetect.browser == 'Explorer' )
		{
			this.el.style.left			= '0px';
			this.el.style.top			= '0px';
		}
	};
}

/*
 * SkyDome Widget inherits from Common widget.
 */
_sd_wdg.prototype = new _wdg;

/**
 * One SkyDome widget object.
 */
function _sd_wdg ( )
{
	/**
	 * HTML id of widget element.
	 */
	this.html_id = null;

	/**
	 * SkyDome instance. Parent.
	 */
	this.sky_dome = null;

	/**
	 * Meaning of widget's geometry parameters.
	 */
	this.geometry = 0;

	this.show = function ( ) { };
	this.hide = function ( ) { };
}

/*
 * SkyDome Dialog inherits from SkyDome Widget.
 */
_sd_dlg.prototype = new _sd_wdg;

function _sd_dlg ( )
{
	this.title = null;
	this.text = null;

	this.show = function ( )
	{
		if ( this.sky_dome != null )
		{
			this.sky_dome.show( );
			
			this.park( );

			var container = document.getElementById( this.html_id );

			container.style.visibility = 'visible';

			this.resize( );
		}
	};

	this.park = function ( )
	{
		//alert(this.htmlId);
		var container = document.getElementById( this.html_id );
		container.style.left = _sd_parking + 'px';
		container.style.top = _sd_parking + 'px';
	};

	this.hide = function ( )
	{
		/**
		 * Dialogs are supposed to hide also SkyDome instance associated with them.
		 */
		if ( this.sky_dome != null )
			this.sky_dome.hide( );

		this.park( );

		document.getElementById( this.html_id ).style.visibility = 'hidden';
	};

	/**
	 * Handle parent (SkyDome) resize event = recompute position.
	 */
	this.resize = function ( )
	{
		var container = document.getElementById( this.html_id );
		
		var width = document.body.offsetWidth / 4;

		if ( width < _sd_dlg_minw )
			width = _sd_dlg_minw;

		container.style.width = width + 'px';

		var left = ( document.body.offsetWidth - container.offsetWidth ) / 2;
		//alert(left + 'px');
		container.style.left = left + 'px';
		container.style.top = _sd_padding + 'px';


		scroll( 0, 0 );
	};

	this.focus = function ( ) { };
}

/**
 * Object to encapsulate informations and actions for dialog buttons.
 */
function _sd_dlg_bt ( cb, text, params )
{
	/**
	 * Text to be displayed on button.
	 */
	this.text = text;

	/**
	 * Callback function for onClick() event.
	 */
	this.cb = cb;

	/**
	 * Parameters to be passed to callback.
	 */
	this.params = params;

	//alert(this.params);
}

/*
 * SkyDome Yes/No Dialog inherits from SkyDome Dialog.
 */
_sd_dlg_yn.prototype = new _sd_dlg( );

function _sd_dlg_yn ( parent, html_id )
{
	this.sky_dome = parent;
	this.html_id = html_id;
	this.yes_bt_txt = null;
	this.no_bt_txt = null;

	if ( this.sky_dome != null )
		this.sky_dome.items[this.sky_dome.items.length] = this;

	this.render = function ( )
	{
		document.getElementById( this.html_id + '.text').innerHTML		= this.text;
		document.getElementById( this.html_id + '.caption' ).innerHTML	= this.title;
		document.getElementById( this.html_id + '.yesBt' ).value		= this.yes_bt_txt;
		document.getElementById( this.html_id + '.noBt' ).value			= this.no_bt_txt;
	};

	this.show = function ( caption, text, yes_bt, no_bt )
	{
		this.text		= text;
		this.title		= caption;
		this.yes_bt_txt	= yes_bt.text;
		this.no_bt_txt	= no_bt.text;

		if ( this.sky_dome != null )
		{
			this.sky_dome.show( );

			var container = document.getElementById( this.html_id );
			
			this.park( );
			this.render( );

			var me = this;

			if ( yes_bt!= null )
			{
				var bt = yes_bt;
				document.getElementById( this.html_id + '.yesBt' ).onclick = function ( ) { me.hide( ); bt.cb( bt.params ); };
			}
			else // default behaviour of Yes button.
				document.getElementById( this.html_id + '.yesBt' ).onclick = function ( ) { me.hide( ); };

			/**
			 * Default behaviour of No button.
			 */
			if ( ( no_bt == null ) || ( no_bt.cb == null ) )
				document.getElementById( this.html_id + '.noBt' ).onclick = function ( ) { me.hide( ); };
	
			container.style.visibility = 'visible';

			this.resize( );

			this.focus( );
		}
	};

	/**
	 * Called from subclass after resize.
	 */
	this.focus = function ( )
	{
		document.getElementById( this.html_id + '.noBt' ).focus( );
	};
}

/*
 * SkyDome Ok Dialog inherits from SkyDome Dialog.
 */
_sd_dlg_ok.prototype = new _sd_dlg;

function _sd_dlg_ok ( parent, html_id )
{
	this.sky_dome = parent;
	this.html_id = html_id;
	this.okBtTxt = null;

	if ( this.sky_dome != null )
		this.sky_dome.items[this.sky_dome.items.length] = this;

	this.render = function ( )
	{
		document.getElementById( this.html_id + '_text').innerHTML		= this.text;
		document.getElementById( this.html_id + '_caption' ).innerHTML	= this.title;
		document.getElementById( this.html_id + '_okBt' ).value			= this.okBtTxt;
	};

	this.show = function ( caption, text, okBt )
	{
		this.text		= text;
		this.title		= caption;
		this.okBtTxt	= okBt.text;

		if ( this.sky_dome != null )
		{
			this.sky_dome.show( );

			var container = document.getElementById( this.html_id );

			this.park( );
			this.render( );

			var me = this;

			if ( okBt!= null )
			{
				var bt = okBt;

				document.getElementById( this.html_id + '_okBt' ).onclick = function ( )
				{
					me.hide( );
					
					if ( okBt.cb != null )
						bt.cb.apply( this, bt.params );
				};
			}
			else // default behaviour of Yes button.
				document.getElementById( this.html_id + '_okBt' ).onclick = function ( ) { me.hide( ); };

			container.style.visibility = 'visible';
			
			this.resize( );

			this.focus( );
		}
	};

	/**
	 * Called from subclass after resize.
	 */
	this.focus = function ( )
	{
		document.getElementById( this.html_id + '_okBt' ).focus( );
	};
}

/*
 * SkyDome FullScreen control inherits from SkyDome dialog.
 */
_sd_fullscr_ctrl.prototype = new _sd_dlg( );

/*
 * SkyDome control for Fullscreen widget (custom dialogs)
 */
function _sd_fullscr_ctrl ( parent, html_id )
{
	this.sky_dome = parent;
	this.html_id = html_id;

	if ( this.sky_dome != null )
		this.sky_dome.items[this.sky_dome.items.length] = this;

	/**
	 * Handle parent (SkyDome) resize event = recompute position.
	 */
	this.resize = function ( )
	{
		var container = document.getElementById( this.html_id );

		var width = document.body.offsetWidth - ( 2 * _sd_padding );

		container.style.width	= width + 'px';
		container.style.top		= ( 2 * _sd_padding ) + 'px';	// necessary for creating gap between bottom edges of dialog and document
		container.style.left	= _sd_padding + 'px';

		scroll( 0, 0 );
		this.sky_dome.resize( false );
		container.style.top		= _sd_padding + 'px';			// creating gap
	};

	/**
	 * Conventional method.
	 */
	this.render = function ( ) { };

}

/*
 * SkyDome simple (non-fullscreen) control inherits from fullscreen SkyDome
 * control.
 */
_sd_simple_ctrl.prototype = new _sd_fullscr_ctrl( );

/*
 * SkyDome control for simple (non-fullscreen) widget (custom dialogs).
 */
function _sd_simple_ctrl ( parent, html_id )
{
	this.sky_dome = parent;
	this.html_id = html_id;

	if ( this.sky_dome != null )
		this.sky_dome.items[this.sky_dome.items.length] = this;

	/**
	 * Handle parent (SkyDome) resize event = recompute position.
	 */
	this.resize = function ( )
	{
		var container = document.getElementById( this.html_id );
		var content = document.getElementById( this.html_id + '.cnt' );

		if ( content )
		{
			var width = content.offsetWidth;
			container.style.width = width + 'px';
		}

		//

		//container.style.width	= width + 'px';
		//container.style.top		= ( 2 * _sd_padding ) + 'px';	// necessary for creating gap between bottom edges of dialog and document
		//container.style.left	= _sd_padding + 'px';

		//scroll( 0, 0 );
		//this.skyDome.resize( false );
		//container.style.top		= _sd_padding + 'px';			// creating gap

		/*var width = document.body.offsetWidth / 4;

		if ( width < _sd_dlg_minw )
			width = _sd_dlg_minw;*/

		//container.style.width = width + 'px';

		var left = ( document.body.offsetWidth - container.offsetWidth ) / 2;
		//alert(left + 'px');
		container.style.left = left + 'px';
		container.style.top = _sd_padding + 'px';


		scroll( 0, 0 );
	};

	/**
	 * Conventional method.
	 */
	this.render = function ( ) { };

}