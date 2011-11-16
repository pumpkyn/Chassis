
/**
 * @file _pers.js
 * @author giorno
 * @package Chassis
 * @subpackage Tags
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * @requires _pers.js
 */

_tags_instance.prototype = new _pers_instance;
_tags_rui.prototype = new _pers_rui;

_tags_rui.prototype.constructor = _tags_rui;

function _tags_rui( pi )
{
	var me = this;
	this.pi = pi;
	//s_pers_rui.prototype.constructor( this );
	
	_tags_rui.prototype.startup = function ( )
	{
		me.cp( me );
		if ( me.pi.rcfg !== null )
			disableSelection( document.getElementById( me.pi.rcfg.back_id ) );
	};
	
	_tags_rui.prototype.preview = function ( )
	{
		var preview	= document.getElementById( me.pi.rcfg.preview_id );
		var ptxt	= document.getElementById( me.pi.rcfg.preview_id + ':txt' );
		var txt	= document.getElementById( me.pi.rcfg.frm_id + '.rui::name' );
		var sch	= document.getElementById( me.pi.rcfg.frm_id + '.rui::scheme' );

		var scheme = 'dar';
		if ( sch )
			scheme = sch.options[sch.selectedIndex].value;

		if ( ptxt && txt )
		{
			if ( txt.value == '' )
				ptxt.innerHTML = sch.options[sch.selectedIndex].text;
			else
				ptxt.innerHTML = txt.value;
		}

		preview.className = '_ctx_scheme _ctx_scheme_' + scheme;

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
		document.getElementById( me.pi.rcfg.frmhl_id ).innerHTML = cap + ' <i>' + txt.value + '</i>';
		
		txt.focus( );
	};
}

_tags_instance.prototype.constructor = _tags_instance;
_tags_instance.prototype.parent = _pers_instance.prototype;
function _tags_instance ( id, layout, url, params, tcfg, rcfg )
{
	var me = this;
	this.id = id;
	this.layout = layout;
	this.ajax = new _ajax_req_ad( true, url, params );
	this.tcfg = tcfg;
	
	// Extra parameter .preview_id for badge HTML ID.
	this.rcfg = rcfg;
	this.tui = null;
	this.rui = null;
	
	// Must be here as scopes are not properly resolved when called from event
	// handler.
	_tags_instance.prototype.startup = function ( )
	{
		me.tui = new _pers_tui( me );
		me.rui = new _tags_rui( me );

		me.tui.startup( );
		me.rui.startup( );
		me.cp( me );
	};
}
