/**
 * @file _wdg.js
 * @project Manhir Framework
 * @author giorno
 *
 * @requires _sd.js
 *
 * Generic Javascript instances for framework widgets.
 */

/**
 * SkyDome for modal dialogs. This one is supposed to be highest level possible.
 */
var _wdg_dome = null;

/**
 * Yes/No dialog.
 */
var _wdg_dlg_yn = null;

/**
 * OK dialog.
 */
var _wdg_dlg_ok = null;

/**
 * This method should be called from body element onLoad event handler.
 */
function _wdg_create ( )
{
	_wdg_dome	= new _sd_dome( '_wdg_dome' );
	_wdg_dlg_yn	= new _sd_dlg_yn( _wdg_dome, '_wdg_dlg_yn' );
	_wdg_dlg_ok	= new _sd_dlg_ok( _wdg_dome, '_wdg_dlg_ok' );
}
