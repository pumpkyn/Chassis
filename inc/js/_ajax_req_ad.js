
/**
 * @file _ajax_req_ad.js
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * @requires prototype.js
 */

/**
 * Ajax request adaptor. This serves as active interface to underlying routines.
 * Purpose is to make use of Ajax requests simpler within the framework and user
 * routines and to hide implementation, so it may be changed in the future. For
 * now it uses Prototype framework as backend. For callbacks array names from
 * Prototype framework callbacks are used (onCreate, onFailure, onSuccess, ...).
 * 
 * @param bool async sets mode of request (false=sync/blocking, true=async/non-blocking)
 * @param string url URL of Ajax server
 * @param array base associative array of base request parameters (passed from server side)
 */
function _ajax_req_ad ( async, url, base )
{
	/**
	 * Copy scope.
	 */
	var me = this;
	
	/**
	 * Request synchronization. True means non-blocking mode.
	 */
	this.async = async;
	
	/**
	 * Ajax server URL.
	 */
	this.url = url;
	
	/**
	 * Common set of Ajax request parameters.
	 */
	this.base = base;
	
	/**
	 * Prepares Ajax request data.
	 * 
	 * @param array extra associative array of extra request parameters (composed by client side/caller)
	 * @param array cbs associative array of callbacks
	 * @param array data associative array of additional POST method data parameter (is null in most of cases)
	 */
	this.prepare = function ( extra, cbs, data )
	{
		var params = '';
		
		for ( var key in this.base )
			params += '&' + key + '=' + this.base[key];
		
		for ( var key in extra )
			params += '&' + key + '=' + extra[key];
		
		var full = cbs;		
			full['asynchronous']	= me.async;
			full['method']			= 'post';
			full['parameters']		= params;
			
			/**
			 * Extra POST data.
			 */
			if ( data != null )
			{
				post_body = params;
				for ( var key in data )
					post_body += '&' + key + '=' + data[key];
				full['postBody'] = post_body;
			}
			
		return full;
	};

	/**
	 * Send-and-Receive data operation. This sends data to Ajax server URL and
	 * performs callbacks for given Ajax event.
	 * 
	 * @param array extra associative array of extra request parameters (composed by client side/caller)
	 * @param array cbs associative array of callbacks
	 * @param array data associative array of additional POST method data parameter (is null in most of cases)
	 */
	this.send = function ( extra, cbs, data )
	{
		var req = new Ajax.Request(	me.url, me.prepare( extra, cbs, data ) );
		return req;
	};
	
	/**
	 * Send-and-Show operation. This sends data to Ajax server URL, renders
	 * response HTML into target element and performs callbacks.
	 * 
	 * @param array extra associative array of extra request parameters (composed by client side/caller)
	 * @param array cbs associative array of callbacks
	 * @param string target_id HTML ID of target container for response content
	 * @param array data associative array of additional POST method data parameter (is null in most of cases)
	 */
	this.update = function ( extra, cbs, target_id, data )
	{
		var req = new Ajax.Updater( target_id, me.url, me.prepare( extra, cbs, data ) );
		return req;
	};
}