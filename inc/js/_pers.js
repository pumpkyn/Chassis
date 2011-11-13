
/**
 * @file _pers.js
 * @author giorno
 * @package Chassis
 * @subpackage Persistence
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * @requires _ajax_req_ad.js
 * @requires XMLWriter-1.0.0-min.js
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
	
	this.search = function ( )
	{
		this.pi.tcfg.p = 1;
		this.refresh( );
	};
	
	this.showall = function ( )
	{
		document.getElementById( this.pi.tcfg.frm_id + '::keywords' ).value = '';
		document.getElementById( this.pi.tcfg.frm_id + '::as::chkbox' ).checked = false;
		this.pi.tcfg.p = 1;
		this.as_click( false );
		this.search( );
	};
	
	this.refresh = function ( )
	{
		this.focus( );
		this.res_render( );
		
		function onCreate( ) {me.effect_show( );me.pi.tcfg.ind.show( 'loading', '_uicmp_ind_gray' );};
		function onFailure( ) {me.effect_hide( );me.pi.tcfg.ind.show( 'e_unknown', '_uicmp_ind_red' );};
		function onComplete( ) {me.effect_hide( );};
		function onSuccess( data ) {me.effect_hide( );me.pi.tcfg.ind.fade( 'loaded', '_uicmp_ind_green' );};

		var data = {primitive: 'tui', method: 'refresh'};
		data['k'] = document.getElementById( this.pi.tcfg.frm_id + '::keywords' ).value;
		data['p'] = this.pi.tcfg.p;
		data['o'] = this.pi.tcfg.o;
		data['d'] = this.pi.tcfg.d;
		var as = document.getElementById( this.pi.tcfg.frm_id + '::as::chkbox' ).checked;
		if ( as )
		{
			data['as'] = 'true';
			if ( this.pi.tcfg.f )
			{
				var f = document.getElementById( this.pi.tcfg.frm_id + '::field' );
				if ( f )
					data['f'] = f[f.selectedIndex].value;
			}
			
			// extract values for restrictors
			if ( this.pi.tcfg.r )
			{
				for ( var id in this.pi.tcfg.r )
				{
					var el = document.getElementById( this.pi.tcfg.frm_id + '::restrictor::' + id );
					if ( el )
						data['r_' + id] = el[el.selectedIndex].value;
				}
			}
		}

		me.pi.ajax.update(	data,
							{onCreate: onCreate, onFailure: onFailure, onComplete: onComplete, onSuccess: onSuccess},
							me.pi.tcfg.cnt_id );
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
	
	/**
	 * Renders semitransparent cover effect over the search container during the
	 * execution of refresh() method.
	 */
	this.effect_show = function ( )
	{
				//alert('a');
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
	
	/**
	 * Hides cover effect.
	 */
	this.effect_hide = function ( )
	{
		if ( !me.effect )
			me.effect = document.getElementById( me.pi.tcfg.cnt_id + '.effect' );
		
		if ( this.effect )	
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
	this.resize = function ( val ) { me.res_update( val ); };
	
	this.res_update = function ( val )
	{
		/**
		 * Update global variable maintained by UICMP.
		 */
		_uicmp_resizer_size = val;
		this.focus( );
		
		function onCreate( ) {me.pi.tcfg.ind.show( 'resizing', '_uicmp_ind_gray' );};
		function onFailure( ) {me.pi.tcfg.ind.show( 'e_unknown', '_uicmp_ind_red' );};
		function onSuccess( data )
		{
			me.pi.tcfg.ind.fade( 'resized', '_uicmp_ind_green' );
			me.res_render( );
			me.pi.tcfg.p = 1;
			me.refresh( );
		};

		me.pi.ajax.send(	{primitive: 'tui', method: 'resize', value: val},
							{onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess},
							null,
							false );
	};
	
	this.res_render = function ( )
	{
		if ( !this.pi.tcfg.res_id )
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
	};
	
	this.reset = function ( )
	{
		
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
							case 'string':
								if ( el )
									writer.writeAttributeString( 'v', el.value );
							break;
							
							case 'tag':
								if ( el )
									writer.writeAttributeString( 'v', el.options[el.selectedIndex].value );
							break;
						}
						//writer.writeAttributeString( 'v',  );
					writer.writeEndElement( );
				}
				//writer.writeElementString( 'pl', Base64.encode( document.getElementById( me.form_id + '.loc' ).value ) );
			writer.writeEndElement( );
		writer.writeEndDocument( );
		
		return waPlusSignWaEncode( writer.flush() );
	};
	
	this.parse = function ( xml )
	{
		var parser = new DOMImplementation( );
		var domDoc = parser.loadXML( xml );

		var rui = domDoc.getDocumentElement( );

		var fs = rui.getElementsByTagName( 'f' );
			//alert(fs.length);
			for( var i = 0; i < fs.length; ++i )
			{
				var f = fs.item( i );
			//	alert(f.getAttribute('n'));
				var field = me.pi.rcfg.f[f.getAttribute( 'n' )];
				var el = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + f.getAttribute( 'n' ) );
				
				if ( field )
				{
					
					//alert(me.pi.rcfg.frm_id + '::rui::' + f.getAttribute('n'));
					if ( field.t == 'string' )
						el.value = f.getAttribute( 'v' );
					if ( field.t == 'tag' )
					{
						//var el = document.getElementById( me.pi.rcfg.frm_id + '.rui::' + f.getAttribute('n') );
						var j = 0;
						
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
							if ( opt.value == f.getAttribute( 'v' ) )
								el.selectedIndex = j;
						}
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
			//alert(data.responseText);
			me.parse(data.responseText);
			me.index = index;
			
			me.pi.rcfg.ind.fade( 'loaded', '_uicmp_ind_green' );
		};

		me.pi.ajax.send(	{primitive: 'rui', method: 'load', index: index},
							{onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess},
							null,
							false );
	};
	
	this.save = function ( )
	{
		//alert(me.message());
		function onCreate( ) {me.pi.rcfg.ind.show( 'saving', '_uicmp_ind_gray' );};
		function onFailure( ) {me.pi.rcfg.ind.show( 'e_unknown', '_uicmp_ind_red' );};
		function onSuccess( data )
		{
			//alert(data.responseText);
			//me.parse(data.responseText);
			//me.index = index;
			
			me.pi.rcfg.ind.fade( 'saved', '_uicmp_ind_green' );
			me.pi.layout.back( );
		};
		
		var data = me.message( );

		me.pi.ajax.send(	{primitive: 'rui', method: 'save', index: me.index},
							{onCreate: onCreate, onFailure: onFailure, onSuccess: onSuccess},
							{data: data},
							false );
	};
	
	this.edit = function ( index )
	{
		me.pi.layout.show( me.pi.rcfg.tab_id );
		me.load( index );
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
	 * .ind ....... reference to indicator instance
	 * .idx ....... indexes (names are keys)
	 * .f ......... fields configuration
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
		me.tui = new _pers_tui( me );
		me.rui = new _pers_rui( me );
		
		me.tui.startup( );
		me.rui.startup( );
	};
	
	/**
	 * Callback for event of showing the tab. It is bind before the TUI instance
	 * is created, therefore it must be implemented as proxy method.
	 */
	this.refresh = function ( ) {me.tui.refresh( );};
}