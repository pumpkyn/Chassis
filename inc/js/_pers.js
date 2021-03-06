
/**
 * @file _pers.js
 * @author giorno
 * @package Chassis
 * @subpackage Persistence
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * @requires _ajax_req_ad.js
 * @requires XMLWriter-1.0.0-min.js
 * @requires wa.js
 */

/**
 * Table UI client side logic. This (default) implementation is dedicated to
 * search in the table.
 */
function _pers_tui ( pi )
{
	/**
	 * Copy scope.
	 */
	var me = this;
	
	/**
	 * Parent Persistence instance. Used to access instance wide data.
	 */
	this.pi = pi;
	
	/**
	 * Semi-translucent cover over search results area for the duration of
	 * search operation.
	 */
	this.effect = null;
	
	// Keeps track of last Ajax call from getr(). Used for not to calling Ajax
	// server too often.
	this.lastgr = Math.round( +new Date( ) / 1000 );
	
	/**
	 * Called from Persistence instance startup( ) routine.
	 */
	this.startup = function ( )
	{
		/**
		 * @todo bypass this when search solution is removed from sources, client logic should be called by JS instance name
		 */
		_uicmp_lookup.register( me.pi.ajax.base.jsvar + '.tui', me );
		
		if ( me.pi.tcfg !== null )
		{
			//me.cnt	= document.getElementById( this.pi.tcfg.cnt_id );
			disableSelection( document.getElementById( this.pi.tcfg.res_id ) );
			if ( this.pi.tcfg.as )
				disableSelection( document.getElementById( this.pi.tcfg.frm_id + '::as::anchor' ) );
		}
	};
	
	//Interface to update scope with proper value. This should be used from
	// subclass to set proper private member for whole inheritance hierarchy.
	this.cp = function ( alter ) {me = alter;};
	
	this.search = function ( )
	{
		this.pi.tcfg.p = 1;
		this.refresh( );
	};
	
	this.showall = function ( )
	{
		document.getElementById( this.pi.tcfg.frm_id + '::keywords' ).value = '';
		if ( me.pi.tcfg.as )
			document.getElementById( this.pi.tcfg.frm_id + '::as::chkbox' ).checked = false;
		this.pi.tcfg.p = 1;
		this.as_click( false );
		this.search( );
	};
	
	// Calls Ajax server with request for new list according to search query
	// parameters.
	this.refresh = function ( )
	{
		this.focus( );
		this.res_render( );
		
		function onCreate( ) {me.effect_show( );me.pi.tcfg.ind.show( 'loading', '_uicmp_ind_gray' );}
		function onFailure( ) {me.effect_hide( );me.pi.tcfg.ind.show( 'e_unknown', '_uicmp_ind_red' );}
		function onComplete( ) { /*me.effect_hide( );*/ }
		function onSuccess( )
		{
			me.effect_hide( );
			me.pi.tcfg.ind.fade( 'loaded', '_uicmp_ind_green' );
			me.getr( );
		}

		var data = {primitive: 'tui', method: 'refresh'};
		data['k'] = document.getElementById( this.pi.tcfg.frm_id + '::keywords' ).value;
		data['p'] = this.pi.tcfg.p;
		data['o'] = this.pi.tcfg.o;
		data['d'] = this.pi.tcfg.d;
		var as = ( ( me.pi.tcfg.as ) ? document.getElementById( this.pi.tcfg.frm_id + '::as::chkbox' ).checked : false );
		if ( as )
		{
			data['as'] = 'true';
			if ( this.pi.tcfg.f )
			{
				var f = document.getElementById( this.pi.tcfg.frm_id + '::field' );
				if ( f )
					data['f'] = f[f.selectedIndex].value;
			}
			
			// extract values for restrictors part of the search query
			if ( this.pi.tcfg.r )
			{
				for ( var id in this.pi.tcfg.r )
				{
					var el = document.getElementById( this.pi.tcfg.frm_id + '::restrictor::' + id );
					if ( el && ( el.selectedIndex >= 0 ) )
						data['r_' + id] = el[el.selectedIndex].value;
				}
			}
		}

		me.pi.ajax.update(	data,
							{onCreate: onCreate, onFailure: onFailure, onComplete: onComplete, onSuccess: onSuccess},
							me.pi.tcfg.cnt_id,
							null,
							true );
	};
	// Updates dynamic restrictor fields with new values.
	this.getr = function ( )
	{
		// At least 30 seconds must pass between two consecutive calls to Ajax
		// server.
		var now = Math.round( +new Date( ) / 1000 );
		if ( me.lastgr + 30 > now )
			return;
		else
			me.lastgr = now;
		
		function onSuccess( data )
		{
			var parser	= new DOMImplementation( );
			var domDoc	= parser.loadXML( data.responseText );
			var tui		= domDoc.getDocumentElement( );
			var rs		= tui.getElementsByTagName( 'r' );
			
			for( var i = 0; i < rs.length; ++i )
			{
				var r = rs.item( i );
				var restrictor = me.pi.tcfg.r[r.getAttribute( 'n' )];
				var el = document.getElementById( me.pi.tcfg.frm_id + '::restrictor::' + r.getAttribute( 'n' ) );
				
				if ( restrictor )
				{
					var j = 0;
					var val = el[el.selectedIndex].value;
					
					for ( j  = el.length - 1; j >= 0; --j )
						el.remove( j );
							
					var o = r.getElementsByTagName( 'o' );
					for ( j = 0; j < o.length; ++j )
					{
						var opt = document.createElement( 'option' );
						opt.value = o.item( j ).getAttribute( 'v' );
						opt.text = o.item( j ).getFirstChild( ).getNodeValue( );
						try
						{
							el.add( opt, null );
						}
						catch ( ex )
						{
							el.add( opt );	// MSIE
						}
								
						if ( opt.value == val )
							el.selectedIndex = j;
					}
				}
			}
		}
		
		me.pi.ajax.send(	{primitive: 'tui', method: 'getr'},
							{onSuccess: onSuccess},
							null,
							false );
	};
	
	this.focus = function ( )
	{
		var el = document.getElementById( this.pi.tcfg.frm_id + '::keywords' );
		try
		{
			if ( el )
			el.focus( );
		}
		catch( ex )
		{
			/**
			 * This happens only in MSIE due to inability to focus on invisible
			 * elements, e.g. in dynamic dialogs.
			 */
		}
	};
	
	// Renders semitransparent cover effect over the search container during the
	// execution of refresh() method.
	this.effect_show = function ( )
	{
		if ( !me.effect )
			me.effect = document.getElementById( me.pi.tcfg.cnt_id + '.effect' );
		
		if ( me.effect )
		{
			var parent = me.effect.parentNode;
			if ( parent )
			{
				me.effect.style.height = parent.offsetHeight + 'px';
				me.effect.style.width = parent.offsetWidth + 'px';
				me.effect.style.visibility = 'visible';
				me.effect.style.display = 'block';
				
			}
		}
	};
	
	// Hides the cover effect.
	this.effect_hide = function ( )
	{
		if ( !me.effect )
			me.effect = document.getElementById( me.pi.tcfg.cnt_id + '.effect' );
		
		if ( me.effect )	
		{
			me.effect.style.visibility = 'hidden';
			me.effect.style.display = 'none';
			me.effect.style.width = '0px';
			me.effect.style.height = '0px';
		}
	};
	
	// Implements same interface as UICMP search solution to match calls from
	// rendered list content.
	this.reorder = function ( order, dir )
	{
		me.pi.tcfg.o = order;
		me.pi.tcfg.d = dir;
		me.pi.tcfg.p = 1;
		me.refresh( );
	};
	
	// Implements same interface as UICMP search solution to match calls from
	// rendered list content.
	this.browse = function ( page ) {me.pi.tcfg.p = page;me.refresh( );};
	
	// Implements same interface as UICMP search solution to match resizer's
	// requirements.
	this.resize = function ( val ) {me.res_update( val );};
	
	this.res_update = function ( val )
	{
		/**
		 * Update global variable maintained by UICMP.
		 */
		_uicmp_resizer_size = val;
		this.focus( );
		
		function onCreate( ) {me.pi.tcfg.ind.show( 'resizing', '_uicmp_ind_gray' );}
		function onFailure( ) {me.pi.tcfg.ind.show( 'e_unknown', '_uicmp_ind_red' );}
		function onSuccess( data )
		{
			me.pi.tcfg.ind.fade( 'resized', '_uicmp_ind_green' );
			me.res_render( );
			me.pi.tcfg.p = 1;
			me.refresh( );
		}

		me.pi.ajax.send(	{primitive: 'tui', method: 'resize', value: val},
							{onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess},
							null,
							false );
	};
	
	this.res_render = function ( )
	{
		/** @todo cache in the constructor */
		if ( !document.getElementById( this.pi.tcfg.res_id + '.10' ) )
			return;

		var icons = [ 10, 20, 30, 50 ];
		var className = '';
		for ( var i = 0; i < icons.length; ++i )
		{
			if ( icons[i] == _uicmp_resizer_size )
				className = '_uicmp_resizer_' + icons[i] + '_on';
			else
				className = '_uicmp_resizer_' + icons[i];

			document.getElementById ( this.pi.tcfg.res_id + '.' + icons[i] ).className = className;
		}
	};
	
	this.as_click = function ( refresh )
	{
		var sink = document.getElementById(this.pi.tcfg.frm_id + '::sink' );
		if ( !sink )
			return;
			
		if ( document.getElementById(this.pi.tcfg.frm_id + '::as::chkbox' ).checked )
			sink.className = '_uicmp_srch_frm';
		else
			sink.className = '_uicmp_srch_frm _uicmp_data';
		
		this.pi.tcfg.p = 1;
		
		if ( refresh )
			this.refresh( );
	};
	
	// Executes click on an icon representing an action field.
	this.iclick = function ( icon, data )
	{
		var scope = me;
		function onCreate ( ) {scope.effect_show( );scope.pi.tcfg.ind.show( 'executing', '_uicmp_ind_gray' );}
		function onFailure ( ) {scope.effect_hide( );scope.pi.tcfg.ind.show( 'e_unknown', '_uicmp_ind_red' );}
		function onSuccess ( )
		{
			scope.effect_hide( ); 
			scope.pi.tcfg.ind.fade( 'done', '_uicmp_ind_green' );
			scope.refresh( );
		}
		
		scope.pi.ajax.send(	{primitive: 'tui', method: 'iclick', icon: icon, data: data},
							{onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess},
							null,
							false );
	};
}

/**
 * Record UI client side logic. This (default) implementation is dedicated to
 * edit the record.
 */
function _pers_rui ( pi )
{
	/**
	 * Copy scope.
	 */
	var me = this;
	
	/**
	 * Parent Persistence instance. Used to access instance wide data.
	 */
	this.pi = pi;
	
	/**
	 * Index identification for edited record. Empty string for new record.
	 */
	this.index = '';
	
	/**
	 * Called from Persistence instance startup( ) routine.
	 */
	this.startup = function ( )
	{
		if ( me.pi.rcfg !== null )
			disableSelection( document.getElementById( me.pi.rcfg.back_id ) );

		// special treatment of textareas to make them resizable
		for ( field in me.pi.rcfg.f )
			if ( me.pi.rcfg.f[field].m )
			{
				var res_opts = new Object();
				res_opts.afterDrag = me.tah_save;
				res_opts.field = field;
				new TextAreaResizer( document.getElementById( me.pi.rcfg.frm_id + '.rui::' + field ), res_opts );
			}
	};
	
	/**
	 * Sends actual height of a text area that was just resized to the server.
	 * @param res_opts object populated upon creation of TextAreaResizer object
	 */
	this.tah_save = function ( res_opts )
	{
		// field name is carried in res_opts
		me.pi.ajax.send( {method: 'tah', field: res_opts.field, val: document.getElementById( me.pi.rcfg.frm_id + '.rui::' + res_opts.field ).getHeight( )}, {}, null, true );
	};
	
	// Updates form caption with value of the form field given by parameter.
	this.preview = function ( name )
	{
		var txt	= document.getElementById( me.pi.rcfg.frm_id + '.rui::' + name );
		
		/**
		 * Extract proper caption string from embedded data.
		 */
		var cap = '';
		if ( me.index != '' )
			cap = me.pi.rcfg.loc.edit;
		else
			cap = me.pi.rcfg.loc.create;

		/**
		 * Set proper title of the tab.
		 */
		if ( txt )
		{
			document.getElementById( me.pi.rcfg.frmhl_id ).innerHTML = cap + ' <i>' + txt.value + '</i>';

			txt.focus( );
		}
		else
			document.getElementById( me.pi.rcfg.frmhl_id ).innerHTML = cap;
	};
	
	//Interface to update scope with proper value. This should be used from
	// subclass to set proper private member for whole inheritance hierarchy.
	this.cp = function ( alter ) {me = alter;};
	
	// Calls Ajax server with request to remove tag with given ID.
	this.remove = function ( id, list )
	{		
		function onCreate( ) { me.pi.tcfg.ind.show( 'executing', '_uicmp_ind_gray' ); }
		function onFailure( ) { me.pi.tcfg.ind.show( 'e_unknown', '_uicmp_ind_red' ); }
		function onSuccess( data )
		{
			me.pi.tcfg.ind.fade( 'executed', '_uicmp_ind_green' );
			me.pi.tui.refresh( );
		}

		me.pi.ajax.send(	{primitive: 'rui', method: 'remove', id: id},
							{onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess},
							null,
							false );
	};
	
	// Erase the form and set it into initial state.
	this.reset = function ( )
	{
		this.index = '';
		me.pi.rcfg.ind.hide( );
		var field;
		for ( field in me.pi.rcfg.f )
		{
			var el = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + field );

			switch ( me.pi.rcfg.f[field].t )
			{
				case 'string':
				case 'password':
					if ( el )
						el.value = '';
				break;
							
				case 'tag':
				case 'enum':
					if ( el )
					{
						el.selectedIndex = 0;
						el.disabled = false;
					}
				break;
				
				case 'bool':
					el.checked = false;
			}
		}
	};
	
	this.enable = function ( enable )
	{
		
	};
	
	// Composes XML message to be sent to the server to save the data.
	this.message = function ( )
	{
		var writer = new XMLWriter( 'UTF-8', '1.0' );
		
		writer.writeStartDocument( false );
			writer.writeStartElement( 'rui' )
				writer.writeAttributeString( 'index', me.index );
				
				var field;
				for ( field in me.pi.rcfg.f )
				{
					writer.writeStartElement( 'f' );
						writer.writeAttributeString( 'n', field );
						
						var el = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + field );

						switch ( me.pi.rcfg.f[field].t )
						{
							case 'datestamp':
								var dayel = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + field + '.day' );
								var monel = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + field + '.month' );
								var yerel = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + field + '.year' );
								var day = dayel.options[dayel.selectedIndex].value;
								var mon = monel.options[monel.selectedIndex].value;
								var yer = yerel.options[yerel.selectedIndex].value;
								writer.writeAttributeString( 'd', day );
								writer.writeAttributeString( 'm', mon );
								writer.writeAttributeString( 'y', yer );
							break;
							
							case 'string':
							case 'password':
								if ( el )
									writer.writeAttributeString( 'v', el.value );
							break;
							
							case 'tag':
							case 'enum':
								if ( el )
									writer.writeAttributeString( 'v', el.options[el.selectedIndex].value );
							break;
							
							case 'bool':
								if ( el )
									writer.writeAttributeString( 'v', el.checked ? 1 : 0 );
							break;
						}

					writer.writeEndElement( );
				}

			writer.writeEndElement( );
		writer.writeEndDocument( );
		
		return waPlusSignWaEncode( writer.flush() );
	};
	
	// Parses XML returned from load() or defaults() method of server PI.
	// @param xml obtained XML payload
	// @param edit whether form is in editor mode or not
	this.parse = function ( xml, edit )
	{
		var parser = new DOMImplementation( );
		var domDoc = parser.loadXML( xml );

		var rui = domDoc.getDocumentElement( );

		var fs = rui.getElementsByTagName( 'f' );

		for( var i = 0; i < fs.length; ++i )
		{
			var f = fs.item( i );
			var field = me.pi.rcfg.f[f.getAttribute( 'n' )];
			var el = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + f.getAttribute( 'n' ) );
				
			if ( field )
			{
				if ( field.t == 'string' )
					el.value = f.getAttribute( 'v' );
				
				if ( field.t == 'datestamp' )
				{
					var day = f.getAttribute( 'd' );
					var mon = f.getAttribute( 'm' );
					var yer = f.getAttribute( 'y' );
					var dayel = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + f.getAttribute( 'n' ) + '.day' );
					var monel = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + f.getAttribute( 'n' ) + '.month' );
					var yerel = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + f.getAttribute( 'n' ) + '.year' );
					
					for ( j = 0; j < dayel.length; ++j )
						if ( dayel[j].value == day ) { dayel.selectedIndex = j; break; }
					
					for ( j = 0; j < monel.length; ++j )
						if ( monel[j].value == mon ) { monel.selectedIndex = j; break; }
					
					for ( j = 0; j < yerel.length; ++j )
						if ( yerel[j].value == yer ) { yerel.selectedIndex = j; break; }
					
				}
				
				if ( field.t == 'bool' )
					el.checked = f.getAttribute( 'v' ) == '1';
				
				if ( ( field.t == 'tag' ) || ( field.t == 'enum' ) )
				{
					var j = 0;
						
					if ( field.d === true )
					{
						for ( j  = el.length - 1; j >= 0; --j )
							el.remove( j );
							
						var o = f.getElementsByTagName( 'o' );
						for ( j = 0; j < o.length; ++j )
						{
							var opt = document.createElement( 'option' );
							opt.value = o.item( j ).getAttribute( 'v' );
							opt.text = o.item( j ).getFirstChild( ).getNodeValue( );
							try
							{
								el.add( opt, null );
							}
							catch ( ex )
							{
								el.add( opt );	// MSIE
							}
								
							// preselect for dynamic
							if ( opt.value == f.getAttribute( 'v' ) )
								el.selectedIndex = j;
						}
					}
					else // preselect for static
					{
						for ( j = 0; j < el.length; ++j )
							if ( el[j].value == f.getAttribute( 'v' ) )
							{
								el.selectedIndex = j;
								break;
							}
					}
					
					// In edit mode this field has to be disabled if flagged as
					// constant.
					if ( edit )
						el.disabled = field.c;
				}
				
			}
			else
			{
				var idx = me.pi.rcfg.idx[f.getAttribute( 'n' )];
				if ( idx && el )
					el.value = f.getAttribute( 'v' );
			}
		}
	};
	
	this.load = function ( index )
	{
		function onCreate( ) {me.pi.rcfg.ind.show( 'loading', '_uicmp_ind_gray' );};
		function onFailure( ) {me.pi.rcfg.ind.show( 'e_unknown', '_uicmp_ind_red' );};
		function onSuccess( data )
		{
			me.parse( data.responseText, true );
			me.index = index;
			me.pi.rcfg.ind.fade( 'loaded', '_uicmp_ind_green' );
		};

		me.pi.ajax.send(	{primitive: 'rui', method: 'load', index: index},
							{onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess},
							null,
							false );
	};
	
	// Load default values for dynamic enums.
	// @param presets 'field1=val1;field2=val2' formatted presets for enums
	this.defaults = function ( presets )
	{
		function onCreate( ) {me.pi.rcfg.ind.show( 'loading', '_uicmp_ind_gray' );};
		function onFailure( ) {me.pi.rcfg.ind.show( 'e_unknown', '_uicmp_ind_red' );};
		function onSuccess( data )
		{
			
			me.parse( data.responseText, false );
			
			if ( presets != null )
			{
				var pairs = presets.split( ';' );
				var p;
				var el;
				for ( var j = 0; j < pairs.length; ++j )
				{
					
					p = pairs[j].split( '=' );
					
					if ( me.pi.rcfg.f[p[0]].t == 'enum' )
					{
						el = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + p[0] );
						for ( var i = 0; i < el.options.length; ++i )
							if ( el.options[i].value == p[1] )
							{
								el.selectedIndex = i;
								break;
							}
					}
					else
						if ( me.pi.rcfg.f[p[0]].t == 'string' )
						{
							el = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + p[0] );
							if ( el )
								el.value = p[1];
						}
				}
			}
				
			me.pi.rcfg.ind.fade( 'loaded', '_uicmp_ind_green' );
		};

		me.pi.ajax.send(	{primitive: 'rui', method: 'defaults'},
							{onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess},
							null,
							false );
	};
	
	
	// Performs check on values of all fields, which have constraints.
	this.check = function ( )
	{
		var field;
		for ( field in me.pi.rcfg.f )
		{
			var el = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + field );
			switch ( me.pi.rcfg.f[field].t )
			{
				case 'string':
					if ( ( me.pi.rcfg.f[field].e === false ) && ( el && ( el.value.trim() == '' ) ) )
					{
						this.pi.rcfg.ind.show( 'e_invalid', '_uicmp_ind_red' );
						el.focus( );
						return false;
					}
				break;
			}
		}
		return true;
	};
	
	// Event handler for clicking on Save button. Verifies data, collect and
	// send them to the Ajax server.
	this.save = function ( )
	{
		if ( !this.check( ) )
			return;
		
		//alert(me.message());
		function onCreate( ) {me.pi.rcfg.ind.show( 'saving', '_uicmp_ind_gray' );}
		function onFailure( ) {me.pi.rcfg.ind.show( 'e_unknown', '_uicmp_ind_red' );}
		function onSuccess( data )
		{	
			if ( data.responseText == 'OK' )
			{
				me.pi.rcfg.ind.fade( 'saved', '_uicmp_ind_green' );
				me.pi.layout.back( );
			}
			else
				me.pi.rcfg.ind.show( data.responseText, '_uicmp_ind_red' );
		}
		
		var data = me.message( );

		me.pi.ajax.send(	{primitive: 'rui', method: 'save', index: me.index},
							{onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess},
							{data: data},
							false );
	};
	
	this.edit = function ( index )
	{
		me.reset( );
		me.pi.layout.show( me.pi.rcfg.tab_id );
		me.load( index );
		me.preview( me.pi.rcfg.preview );
	};
	
	this.create = function ( presets )
	{
		me.reset( );
		me.pi.layout.show( me.pi.rcfg.tab_id );
		me.defaults( presets );
		me.preview( me.pi.rcfg.preview );
	};
}

/**
 * Client side logic for single table persistence. It comprises both, search
 * features and editor handling.
 */
function _pers_instance ( id, layout, url, params, tcfg, rcfg )
{
	/**
	 * Copy scope.
	 */
	var me = this;
	
	/**
	 * Identifier of the instance across lives, languages and universes. 
	 */
	this.id = id;
	
	/**
	 * Reference to UICMP layout instance
	 */
	this.layout = layout;
	
	/**
	 * Ajax request adaptor for this instance.
	 */
	this.ajax = new _ajax_req_ad( true, url, params );
	
	/**
	 * Configuration of table UI operations, ID's of involved elements, etc.
	 * 
	 * .tab_id .... ID of UICMP tab component renedring the search solution
	 * .frm_id .... HTML ID of the search form component, used for extraction of values
	 * .cnt_id .... HTML ID of the results container component
	 * .res_id .... HTML ID of the resizer component
	 * .ind ....... reference to indicator instance
	 * .as ........ determines whether form is for advanced search or not
	 * .f ......... determines whether form contains field search box
	 * .o ......... order of sorting
	 * .d ......... direction of sorting
	 * .p ......... page
	 * .r ......... associative array of search restrictions configuration (field name is the key)
	 *              .dyn .... whether options are dynamic or not
	 *              .type ... 'bin' or 'multi'
	 */
	this.tcfg = tcfg;
	
	/**
	 * Configuration of record UI operation, ID's of elements, etc.
	 * 
	 * .tab_id .... ID of UICMP tab component rendering the form
	 * .back_id ... HTML ID of 'Back' anchor
	 * .frm_id .... simple form HTML ID (prefix for input elements)
	 * .frmhl_id .. headline HTML ID (for preview method)
	 * .ind ....... reference to indicator instance
	 * .idx ....... indexes (names are keys)
	 * .preview ... field, update on which triggers preview
	 * .f ......... fields configuration
	 *              .d ... specifies if field is dynamic
	 *              .e ... specifies if field can have empty values (e.g. zero-length strings)
	 *              .t ... type ('string','tag','enum','datestamp')
	 *              .m ... multiline (for comment-like .t='string')
	 * .loc ....... localization messages
	 *              .edit ..... for editing a record
	 *              .create ... for creating a record
	 */
	this.rcfg = rcfg;
	
	/**
	 * Reference to instance of table UI logic.
	 */
	this.tui = null;
	
	/**
	 * Reference to instance of record UI logic.
	 */
	this.rui = null;

	/**
	 * Startup routine. Called from page body onLoad event handler as HTML must
	 * be completely rendered before we may manipulate with its elements.
	 */
	this.startup = function ( )
	{
		if ( me.tcfg != null )
			me.tui = new _pers_tui( me );
		
		if ( me.rcfg != null )
			me.rui = new _pers_rui( me );
		
		if ( me.tcfg != null )
			me.tui.startup( );
		
		if ( me.rcfg != null )
			me.rui.startup( );
	};
	
	//Interface to update scope with proper value. This should be used from
	// subclass to set proper private member for whole inheritance hierarchy.
	this.cp = function ( alter ) {me = alter;};
	
	/**
	 * Callback for event of showing the tab. It is bind before the TUI instance
	 * is created, therefore it must be implemented as proxy method.
	 */
	this.refresh = function ( ) {me.tui.refresh( );};
}

// Callback for context remove icon onClick() event.
function _pers_remove ( data )
{
	var instance = data['jsvar'];
	instance.remove( data['id'], data['list'] );

	if ( typeof data['cb'] !== 'undefined' )
		data['cb']();	
}