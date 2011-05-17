
/**
 * @file _uicmp.js
 * @author giorno
 * @subpackage UICMP
 *
 * Logic for UICMP components. Represents Javascript counterpart for PHP
 * components.
 */

/**
 * Initial value for the resizer. Should be re-set by embedded script through
 * _requirer instance.
 */
var _uicmp_resizer_size = 10;

/**
 * Instance of lookup table for search solutions and their ids.
 */
var _uicmp_lookup = new _uicmp_search_lookup_table( );

/**
 * Client side logic for UICMP layout.
 */
function _uicmp_layout (  )
{
	/**
	 * Array holding information about tab components.
	 */
	this.tabs = new Object( );

	/**
	 * A Brief History of Time.
	 */
	this.stack = new _uicmp_tab_stack( );

	/**
	 * Index of current shown tab. Performance improvement.
	 */
	this.current = null;

	/**
	 * Method called from <body> onLoad event.
	 */
	this.startup = function ( )
	{
		this.show( this.current );

		for ( var tab in this.tabs )
			this.tabs[tab].startup( );
	};

	/**
	 * Registers new tab into layout.
	 */
	this.addTab = function ( id, hidden, stackable, foldId )
	{
		this.tabs[id] = new _uicmp_tab( id, hidden, stackable, foldId );

		if ( hidden == false )
			this.current = id;
	};

	/**
	 * Registers new callback for tab with given Id.
	 */
	this.registerTabCb = function ( id, event, cb )
	{
			//alert(id);
		if ( this.tabs[id] )
		{
			//alert('a');
			this.tabs[id].registerCb( event, cb );
		}
	};

	/**
	 * Shows tab with given id.
	 */
	this.show = function ( id )
	{
		if ( this.tabs[id] )
		{
			this.tabs[this.current].hide( );
			this.tabs[id].show( );
			this.current = id;
			this.stack.push( id );
			return;
		}
	};

	/**
	 * Display previous tab.
	 */
	this.back = function ( )
	{
		var id = this.stack.pop( );

		while ( ( id != null ) && ( this.tabs[id].stackable == false ) )
			id = this.stack.pop( );

		if ( id != null )
				this.show ( id );
	};
}

/**
 * Client side logic for UICMP tab instance.
 */
function _uicmp_tab ( id, hidden, stackable, foldId )
{
	/**
	 * HTML id of the tab.
	 */
	this.id = id;

	/**
	 * Actual visibility status.
	 */
	this.hidden = hidden;
	
	/**
	 * Stackability of the tab.
	 */
	this.stackable = stackable;

	/**
	 * HTML id of tab's fold component.
	 */
	this.foldId = foldId;

	/**
	 * Associative array of callbacks for events (=keys).
	 */
	this.cbs = new Object( );
	this.cbs['onLoad'] = new Array( );
	this.cbs['onShow'] = new Array( );
	this.cbs['onHide'] = new Array( );

	/**
	 * Registers new callback for given event.
	 */
	this.registerCb = function ( event, cb )
	{
		this.cbs[event][this.cbs[event].length] = cb;
	};

	/**
	 * Calls all callbacks for given event.
	 */
	this.callCbs = function ( event )
	{
		for ( var i = 0; i < this.cbs[event].length; ++i )
			this.cbs[event][i]();
	};

	/**
	 * Called from parent (_uicmp_layout) on startup.
	 */
	this.startup = function ( )
	{
		if ( this.foldId != null )
			disableSelection ( document.getElementById( this.foldId ) );
		
		this.callCbs( 'onLoad' );
	};

	/**
	 * Hides tab and calls all callbacks associated with the event.
	 */
	this.hide = function ( )
	{
		var tabEl = document.getElementById( this.id );

		if ( tabEl )
		{
			this.hidden = true;
			tabEl.className = '_uicmp_tab_hidden';
			
			if ( this.foldId )
			{
				var foldEl = document.getElementById( this.foldId );
				if ( foldEl )
					foldEl.className = '_uicmp_fold_hidden';
			}
			this.callCbs( 'onHide' );
		}
	};

	/**
	 * Displays tab and calls all callbacks associated with the event.
	 */
	this.show = function ( )
	{
		var tabEl = document.getElementById( this.id );
		if ( tabEl )
		{
			this.hidden = false;
			tabEl.className = '_uicmp_tab';

			if ( this.foldId )
			{
				var foldEl = document.getElementById( this.foldId );
				if ( foldEl )
					foldEl.className = '_uicmp_fold';
			}

			this.callCbs( 'onShow' );
		}
	};
}

/**
 * Client side logic for search instance.
 */
function _uicmp_search ( id, tabId, ind, url, params, config, formId, container_id, resizer_id )
{
	/**
	 * Hack to cope with event controlled methods where instance scope may be
	 * unavailable.
	 */
	var me = this;

	/**
	 * Instance of _uicmp_ind providing messaging UI for the solution.
	 */
	this.ind = ind;

	/**
	 * Registers instance into lookup table.
	 */
	_uicmp_lookup.register( id, me );
	
	/**
	 * Identification of search instance. This is sent to Ajax server
	 * implementation to identify requester of the content.
	 */
	this.id = id;

	/**
	 * HTML id of corresponding tab.
	 */
	this.tabId = tabId;
	this.url = url;
	this.params = params;
	this.formId = formId;
	this.container_id = container_id;

	/**
	 * HTML ID of resizer widget. If emty or null, resizer features are not
	 * used.
	 */
	this.resizer_id = resizer_id;
	

	this.keywords = ( config != null ) ? config.k : '';
	this.page = ( config != null ) ? Number( config.p ) : 1;
	this.order = ( config != null ) ? config.o : null;
	this.dir = ( config != null ) ? config.d : 'ASC';
	
	/**
	 * Reference to cover effect element instance.
	 */
	this.effect = null;

	this.startup = function ( )
	{
		var el = document.getElementById( me.resizer_id );
		if ( el )
			disableSelection( el );
	};

	this.focus = function ( )
	{
		var el = document.getElementById( this.formId + ':input' );
		if ( el )
			el.focus( );
	};
	
	/**
	 * Callback for tab being shown event.
	 */
	this.tabShown = function ( )
	{
		//alert('a');
		me.focus( );
		me.render_resizer( );
		me.refresh( );
	};

	this.search = function ( )
	{
		this.keywords = document.getElementById( this.formId + ':input' ).value;
		this.page = 1;
		//alert('search');
		this.focus( );
		this.refresh( );
	};

	this.showAll = function ( )
	{
		document.getElementById( this.formId + ':input' ).value = '';
		//alert('showAll');
		this.search( );
	};
	
	/**
	 * Renders semitransparent cover effect over the search container during the
	 * execution of refresh() method.
	 */
	this.effect_show = function ( )
	{
				//alert('a');
		if ( !this.effect )
			this.effect = document.getElementById( this.container_id + '.effect' );
		
		if ( this.effect )
		{
		
			
			var parent = this.effect.parentNode;
			if ( parent )
			{
				this.effect.style.height = parent.offsetHeight + 'px';
				this.effect.style.width = parent.offsetWidth + 'px';
				this.effect.style.visibility = 'visible';
				this.effect.style.display = 'block';
				
			}
		}
	};
	
	/**
	 * Hides cover effect.
	 */
	this.effect_hide = function ( )
	{
		if ( !this.effect )
			this.effect = document.getElementById( this.container_id + '.effect' );
		
		if ( this.effect )	
		{
			this.effect.style.visibility = 'hidden';
			this.effect.style.display = 'none';
			this.effect.style.width = '0px';
			this.effect.style.height = '0px';
		}
	};

	this.refresh = function ( )
	{
		/**
		 * Compose request parameters.
		 */
		var reqParams = '';
		for ( var key in this.params )
			reqParams += '&' + key + '=' + this.params[key];
		reqParams += '&method=refresh' +
					 '&id=' + this.id +
					 '&keywords=' + this.keywords +
					 '&page=' + this.page +
					 '&dir=' + this.dir +
					 '&order=' + this.order;

		/**
		 * Copy Ajax indicator into this scope.
		 */
		var scope = this;
		var ind = this.ind;
				 
		var sender = new Ajax.Updater( scope.container_id, this.url,
										{
											asynchronous: true,
											method: 'post',
											parameters: reqParams,
											onCreate: function ( )
											{
												scope.effect_show( );
												ind.show( 'loading', '_uicmp_ind_gray' );
												//document.getElementById( scope.container_id ).innerHTML = '';
											},
											onFailure: function ( )
											{
												scope.effect_hide( );
												ind.show( 'e_unknown', '_uicmp_ind_red' );
											},
											onComplete: function ( )
											{
												scope.effect_hide( );
											},
											onSuccess: function ( data )
											{
												scope.effect_hide( );
												ind.fade( 'loaded', '_uicmp_ind_green' );
											}
										}
							);
		return sender;
	};

	this.resize = function ( newSize )
	{
		this.focus( );
		//alert(newSize);
		_uicmp_resizer_size = newSize;

		/**
		 * Compose request parameters.
		 */
		var reqParams = '';
		for ( var key in this.params )
			reqParams += '&' + key + '=' + this.params[key];
		
		reqParams += '&method=resize' +
					 '&size=' + _uicmp_resizer_size;

		/**
		 * Copy this object into this scope.
		 */
		var _this = this;
		
		/**
		 * Copy Ajax indicator into this scope.
		 */
		var ind = this.ind;

		var sender = new Ajax.Request( this.url,
										{
											asynchronous: false,
											method: 'post',
											parameters: reqParams,
											onCreate: function ( )
											{
												ind.show( 'resizing', '_uicmp_ind_gray' );
											},
											onFailure: function ( )
											{
												ind.show( 'e_unknown', '_uicmp_ind_red' );
											},
											onComplete: function ( )
											{
												// component refresh method should clear indicator
											},
											onSuccess: function ( data )
											{
												ind.fade( 'resized', '_uicmp_ind_green' );
												_this.render_resizer( );
												_this.page = 1;
												_this.refresh( );
											}
										}
							);
		return sender;

		/*this.page = 1;
		this.refresh( );*/
		
	};

	this.render_resizer = function ( )
	{
		if ( !this.resizer_id )
			return;

		var icons = [ 10, 20, 30, 50 ];
		var className = '';
		for ( var i = 0; i < icons.length; ++i )
		{
			if ( icons[i] == _uicmp_resizer_size )
				className = '_uicmp_resizer_' + icons[i] + '_on';
			else
				className = '_uicmp_resizer_' + icons[i];

			document.getElementById ( this.resizer_id + '.' + icons[i] ).className = className;
		}
	};

	this.reorder = function ( order, dir )
	{
		this.order = order;
		this.dir = dir;
		this.page = 1;
		this.refresh( );
	};

	this.browse = function ( page )
	{
		this.page = page;
		this.refresh( );
	};
}

/**
 * Object serving as a lookup table for search instances and their ids.
 */
function _uicmp_search_lookup_table ( )
{
	/**
	 * Lookup table.
	 */
	this.instances = new Object( );

	/**
	 * Adds new instance into table.
	 */
	this.register = function ( id, instance )
	{
		this.instances[id] = instance;
	};

	/**
	 * Lookup for the instance with given id.
	 */
	this.lookup = function ( id )
	{
		//alert(id);
		return this.instances[id];
	};
}

/**
 * Callback for context remove icon onClick() event.
 */
function _uicmp_cdes_remove( data )
{
	var client_var = data['client_var'];
	client_var.remove( data['id'], data['list'] );
}

function _uicmp_cdes_editor ( id, layout, tabId, captionId, previewId, /*captionId,*/ ind, url, params )
{
	var me = this;
	this.id = id;
	this.layout = layout;
	this.tabId = tabId;
	this.previewId = previewId;
	this.url = url;
	this.params = params;
	this.captionId = captionId;
	
	/**
	 * Cache for Id of edited context.
	 */
	this.ctxId = 0;
	
	/**
	 * Form has two basic modes: edit and create. Difference from editor point
	 * of view is need to send also context Id when editing and to display
	 * proper strings.
	 */
	this.editing = false;
	//this.captionId = captionId;

	/**
	 * Javascript instance for indicator component.
	 */
	this.ind = ind;

	/**
	 * Resets form to default state, clears fields, etc.
	 */
	this.reset = function ( )
	{
		document.getElementById( me.id + '.scheme' ).selectedIndex = 0;
		document.getElementById( me.id + '.disp' ).value = '';
		document.getElementById( me.id + '.desc' ).value = '';
		me.ind.hide( );
		me.ctxId = 0;
		//document.getElementById( me.buttonsId + '.S1' ).style.visibility = 'hidden';
		me.preview( );
	};

	this.preview = function ( )
	{
		var prevEl	= document.getElementById( me.previewId );
		var prTextEl	= document.getElementById( me.previewId + ':txt' );
		var txtEl	= document.getElementById( me.id + '.disp' );
		var schEl	= document.getElementById( me.id + '.scheme' );

		var scheme = 'dar';
		if ( schEl )
			scheme = schEl.options[schEl.selectedIndex].value;

		if ( prTextEl && txtEl )
		{
			if ( txtEl.value == '' )
				prTextEl.innerHTML = schEl.options[schEl.selectedIndex].text;
			else
				prTextEl.innerHTML = txtEl.value;
		}

		prevEl.className = '_ctx_scheme _ctx_scheme_' + scheme;

		/**
		 * Extract proper caption string from embedded data.
		 */
		var cap = '';
		if ( me.ctxId != 0 )
			cap = document.getElementById( this.id + '.msg.edit' ).innerHTML;
		else
			cap = document.getElementById( this.id + '.msg.create' ).innerHTML;

		/**
		 * Set proper title of the tab.
		 */
		document.getElementById( this.captionId  ).innerHTML = cap + ' <i>' + txtEl.value + '</i>';
		
		txtEl.focus( );
	};

	this.edit = function ( id, sch, disp, desc )
	{
		me.layout.show( me.tabId );
		me.ctxId = id;
		this.ind.show( 'preparing', '_uicmp_ind_gray' );
		var schEl = document.getElementById( me.id + '.scheme' );
		for ( var i = 0; i < schEl.options.length; ++i )
			if ( schEl.options[i].value == sch )
			{
				schEl.selectedIndex = i;
				break;
			}
		//.selectedIndex = 0;
		document.getElementById( me.id + '.disp' ).value = disp;
		document.getElementById( me.id + '.desc' ).value = desc;
		this.ind.fade( 'prepared', '_uicmp_ind_green' );
		this.preview();
	};

	this.create = function (  )
	{
		me.layout.show( me.tabId );
//		this.ind.show( 'preparing', '_uicmp_ind_gray' );
		this.ind.fade( 'prepared', '_uicmp_ind_green' );
		this.preview();
	};

	this.close = function ( )
	{
		this.layout.back();
	};

	this.save = function ( )
	{
		/**
		 * Copy scope.
		 */
		var scope = this;

		/**
		 * Compose request parameters.
		 */
		var reqParams = '';
		for ( var key in this.params )
			reqParams += '&' + key + '=' + this.params[key];

		var schEl	= document.getElementById( me.id + '.scheme' );

		reqParams += '&method=save' +
					 '&id=' + this.id +
					 '&ctx=' + Number( this.ctxId ) +
					 '&sch=' + schEl.options[schEl.selectedIndex].value +
					 '&disp=' + document.getElementById( me.id + '.disp' ).value +
					 '&desc=' + document.getElementById( me.id + '.desc' ).value;

		/**
		 * Copy Ajax indicator into this scope.
		 */
		var ind = this.ind;

		var sender = new Ajax.Request( scope.url,
										{
											asynchronous: false,
											method: 'post',
											parameters: reqParams,
											onCreate: function ( )
											{
												ind.show( 'saving', '_uicmp_ind_gray' );
											},
											onComplete: function ( )
											{
												
											},
											onFailure: function ()
											{
												ind.show( 'e_unknown', '_uicmp_ind_red' );
											},
											onSuccess: function ( data )
											{
												if ( data.responseText == 'saved' )
												{
													ind.fade( 'saved', '_uicmp_ind_green' );
													scope.layout.back( );
												}
												else
												{
													ind.show( data.responseText, '_uicmp_ind_red' );
												}
											}
										}
							);
		return sender;
	};

	this.remove = function ( ctx, list )
	{
		/**
		 * Copy scope.
		 */
		var scope = this;

		/**
		 * Compose request parameters.
		 */
		var reqParams = '';
		for ( var key in this.params )
			reqParams += '&' + key + '=' + this.params[key];

		//var schEl	= document.getElementById( me.id + '.scheme' );

		reqParams += '&method=remove' +
					 '&id=' + this.id +
					 '&ctx=' + ctx;

		/**
		 * Copy Ajax indicator into this scope.
		 */
		var ind = this.ind;

		var sender = new Ajax.Request( scope.url,
										{
											asynchronous: false,
											method: 'post',
											parameters: reqParams,
											onCreate: function ( )
											{
												//ind.show( 'saving', '_uicmp_ind_gray' );
												ind.show( 'executing', '_uicmp_ind_gray' );
											},
											onComplete: function ( )
											{

											},
											onFailure: function ()
											{
												ind.show( 'e_unknown', '_uicmp_ind_red' );
												//ind.show( 'e_unknown', '_uicmp_ind_red' );
											},
											onSuccess: function ( data )
											{
												ind.fade( 'executed', '_uicmp_ind_green' );
												//scope.layout.show( scope.tabId );
												list.refresh( );
												/*if ( data.responseText == 'saved' )
												{
													ind.fade( 'saved', '_uicmp_ind_green' );
													layout.back( );
												}
												else
												{
													ind.show( data.responseText, '_uicmp_ind_red' );
												}*/
											}
										}
							);
		return sender;
	};
}

/**
 * Structure maintaining order of tabs shown.
 */
function _uicmp_tab_stack ( )
{
	this.a = new Array( );
	
	this.push = function ( id )
	{
		/**
		 * Cleanup.
		 */
		if ( this.a.length > 16 )
			this.a.splice( 0, 1 );
		
		/**
		 * Prevent duplicates.
		 */
		if ( this.a[this.a.length - 1] == id )
			return;
		
		this.a[this.a.length] = id;
	};

	this.pop = function ( )
	{
		if ( this.a.length > 1 )
		{
			this.a.splice( this.a.length - 1, 1 );
			return this.a[this.a.length - 1];
		}
		else
			return null;
	};
}

/**
 * CDES contexts cloud client side logic.
 */
function _uicmp_cdes_cloud ( js_var, html_id, url, params )
{
	var me = this;
	this.url = url;
	this.params = params;
	
	/**
	 * Contexts internal representation array.
	 */
	this.ctxs = new Object( );
	
	/**
	 * HTML id of container element.
	 */
	this.html_id = html_id;

	/**
	 * Name of global variable holding this instance.
	 */
	this.js_var = js_var;

	/**
	 * Temporarily hides cloud to prevent changes.
	 */
	this.disable = function ( ) {document.getElementById( this.html_id ).style.visibility = 'hidden';};

	/**
	 * Restores cloud visibility after .disable() was called.
	 */
	this.enable = function ( ) {document.getElementById( this.html_id ).style.visibility = 'visible';};

	/**
	 * Loads cloud of contexts from the server.
	 */
	this.get = function ( )
	{
		/**
		 * Copy me into this scope. Awkward, but
		 */
		var scope = me;

		/**
		 * Compose request parameters.
		 */
		var reqParams = '';
		for ( var key in scope.params )
			reqParams += '&' + key + '=' + scope.params[key];

		reqParams += '&method=get' +
					 '&id=' + scope.html_id +
					 '&js_var=' + scope.js_var	;

		var sender = new Ajax.Updater( scope.html_id, this.url,
										{
											asynchronous: false,
											method: 'post',
											parameters: reqParams,
											onSuccess: function ( )
											{
												scope.ctxs = new Object( );
												disableSelection( document.getElementById( scope.html_id ) );
											}
										}
								);
		return sender;
	};

	this.set = function ( id )
	{
		if ( this.ctxs[id] == null )
		{
			var data_el = document.getElementById( this.html_id + '.' + id + '.sch' );
			if ( data_el )
			{
				this.ctxs[id] = new Array( );
				this.ctxs[id][0] = data_el.innerHTML;
				this.ctxs[id][1] = false;
			}
			else
				return false;
		}

		var el = document.getElementById( this.html_id + '.' + id );
		if ( this.ctxs[id][1] == false )
			el.className = '_ctx_scheme _ctx_scheme_' + this.ctxs[id][0];
		else
			el.className = '_ctx_scheme _ctx_scheme_dum';

		this.ctxs[id][1] = !this.ctxs[id][1];
	};

	/*
	 * Import contexes from plain text of numbers separated by comma.
	 */
	this.set_batch = function ( batch )
	{
		//this.on = new Object( );
		if ( batch != '' )
		{
			var ids = batch.split( ',' );

			for ( i = 0; i < ids.length ; i++ )
			{
				if ( ( this.ctxs[ids[i]] == null ) || ( this.ctxs[ids[i]][1] === false ) )
					this.set( ids[i] );

				//this.colorize( arr[i] );
			}
		}
	};

	this.write = function ( writer )
	{
		writer.writeStartElement( 'ctxs' );
		for ( i in this.ctxs )
		{
			if ( this.ctxs[i][1] == true )
			{
				writer.writeStartElement( 'ctx' );
					writer.writeAttributeString( 'id', i );
				writer.writeEndElement( );
			}
		}
		writer.writeEndElement( );
	};
}

/**
 * Logic for _uicmp_gi_ind.
 */
function _uicmp_ind ( id, sep_id, messages )
{
	var me = this;
	this.id = id;
	this.sep_id = sep_id;
	this.messages = messages;
	this.timer = null;

	/**
	 * Hides indicator immediately;
	 */
	this.hide = function ( )
	{
		clearTimeout( this.timer );
		document.getElementById( me.sep_id ).style.visibility = 'hidden';
		document.getElementById( me.id ).style.visibility = 'hidden';
	};

	this.show = function ( msg, style )
	{
		this.hide( );
		this.opacity( me.id, 1 );
		this.opacity( me.sep_id, 1 );
		var el = document.getElementById( me.id );
		if ( this.messages[msg] )
			el.innerHTML = this.messages[msg];

		document.getElementById( me.sep_id ).style.visibility = 'visible';
		el.style.visibility = 'visible';
		
		if ( style )
			el.className = style;
		//this.fade( );
	};
	
	this.fade = function ( msg, style )
	{
		this.show( msg, style );
		this.do_fade( 1 );
	};

	this.opacity = function ( id, level )
	{
		var el = document.getElementById( id );
		el.style.opacity = level;
		el.style.MozOpacity = level;
		el.style.KhtmlOpacity = level;
		el.style.filter = "alpha(opacity=" + (level * 100) + ");";
	};

	this.do_fade = function ( level )
	{
		var _this = this;
		this.opacity( this.id, level );
		this.opacity( this.sep_id, level );

		var delay = 50;

		/**
		 * Set extra yield for first shade.
		 */
		if ( level >= 1 )
			delay = 8 * delay;

		if ( level >= 0 )
		{
			this.timer = setTimeout( function() {_this.do_fade( level - 0.025 )}, delay );
		}
		else
		{
			this.opacity( id, 0 );
			this.opacity( sep_id, 0 );
			clearTimeout( this.timer );
			this.timer = null;
		}
	}
}

/**
 * Checks checkboxes with given HTML ID's whether they are checked or not and
 * returns array of checked atom ID's.
 *
 * @todo move into Lists Javascript resources
 */
function _list_bp_checked ( chkboxes )
{
	var ret = new Array( );
	for ( html_id in chkboxes )
	{
		var el = document.getElementById( html_id );
		if ( el && el.checked )
			ret[ret.length] = chkboxes[html_id];
	}

	if ( ret.length <= 0 )
		return false;

	return ret;
}
