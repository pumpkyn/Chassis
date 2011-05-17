/* 
 * @file wa.js
 *
 * Workaround hacks for various purposes. A lot of them is QnD and others are
 * copied from the Web.
 *
 * @author giorno
 */

/*
 * This variable MUST (!) have same value as framework class.Config.php constant
 * XMLREPLACEMENT_PLUSSIGN
 */
var __mFwXmlReplacementPlusSign = '(-)*(-)_=_plus';

/*
 * Enhance string prototype with trim() function.
 * 
 * @url http://wonko.com/post/how_not_to_write_a_javascript_trim_function
 * @author Douglas Crockford
 */
String.prototype.trim = function ( )
{
	return this.replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
};

/*
 * Encode data for PHP decoder plus sign. See class.Wa.php for details.
 */
function waPlusSignWaEncode ( data )
{
	return data.replace( /\+/g, __mFwXmlReplacementPlusSign );
}

/*
 * Return keycode of key from onKey* events.
 */
function waEventKeycode ( e ) {return e?e.which:event.keyCode;}

/**
 * Get element position. Code stolen from http://www.quirksmode.org/js/findpos.html
 */
function waFindPos ( obj )
{
	var curleft = curtop = 0;
	if ( obj.offsetParent )
	{
		do
		{
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		}
		while ( obj = obj.offsetParent );
	}
	return [curleft,curtop];
}

/**
 * Scroll to given element
 */
function waScrollToEl ( elId )
{
	var el = document.getElementById( elId );
	if ( el )
	{
		var coor = waFindPos( el );
		window.scrollTo( 0, coor[1] );
	}
}