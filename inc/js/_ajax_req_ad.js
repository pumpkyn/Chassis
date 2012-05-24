
/**
 * @file _ajax_req_ad.js
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * @requires prototype.js
 */

_ajax_req_ad.prototype = new Object( );
_ajax_req_ad.prototype.constructor = _ajax_req_ad;

/**
 * Ajax request adaptor. This serves as active interface to underlying routines.
 * Purpose is to make use of Ajax requests simpler within the framework and user
 * routines and to hide implementation, so it may be changed in the future. For
 * now it uses Prototype framework as backend. For callbacks array names from
 * Prototype framework callbacks are used (onCreate, onFailure, onSuccess, ...).
 * 
 * @param async sets mode of request (false=sync/blocking, true=async/non-blocking)
 * @param url URL of Ajax server
 * @param base associative array of base request parameters (passed from server side)
 */
function _ajax_req_ad ( async, url, base )
{
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
}

/**
 * Prepares Ajax request data.
 * 
 * @param extra associative array of extra request parameters (composed by client side/caller)
 * @param cbs associative array of callbacks
 * @param data associative array of additional POST method data parameter (is null in most of cases)
 * @param async method used for the request
 */
_ajax_req_ad.prototype.prepare = function ( extra, cbs, data, async )
{
	var params = '';
		
	for ( var key in this.base )
		params += '&' + key + '=' + this.base[key];
		
	for ( var key in extra )
		params += '&' + key + '=' + extra[key];
		
	var full = cbs;		
		full['asynchronous']	= async;
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
}
	
/**
 * Send-and-Receive data operation. This sends data to Ajax server URL and
 * performs callbacks for given Ajax event.
 * 
 * @param extra associative array of extra request parameters (composed by client side/caller)
 * @param cbs associative array of callbacks
 * @param data associative array of additional POST method data parameter (is null in most of cases)
 * @param async if not null, explicitly changing default blocking behaviour
 */
_ajax_req_ad.prototype.send = function ( extra, cbs, data, async )
{
	return new Ajax.Request( this.url, this.prepare( extra, cbs, data, ( ( async !== null ) ? async : this.async ) ) );
}

/**
 * Send-and-Show operation. This sends data to Ajax server URL, renders
 * response HTML into target element and performs callbacks.
 * 
 * @param extra associative array of extra request parameters (composed by client side/caller)
 * @param cbs associative array of callbacks
 * @param target_id HTML ID of target container for the response content
 * @param data associative array of additional POST method data parameter (is null in most of cases)
 * @param async if not null, explicitly changing default blocking behaviour
 */
_ajax_req_ad.prototype.update = function ( extra, cbs, target_id, data, async )
{
	return new Ajax.Updater( target_id, this.url, this.prepare( extra, cbs, data, ( ( async !== null ) ? async : this.async ) ) );
}
