<?php

/**
 * @file _list_bp.php
 * @author giorno
 * @package Chassis
 * @subpackage List
 *
 * Quasi virtual component creating layout for Batch Processing (BP) form in
 * lists. It creates actual UI by calling UICMP API. As handled content is
 * delivered in Ajax session, client side logic is not OO.
 */

require_once CHASSIS_LIB . 'uicmp/_uicmp_buttons.php';
require_once CHASSIS_LIB . 'uicmp/_uicmp_gi.php';
require_once CHASSIS_LIB . 'uicmp/_vcmp_comp.php';

class _list_bp
{
	/**
	 * Container for control widgets of the form (buttons).
	 * 
	 * @var <_uicmp_buttons> 
	 */
	protected $uicmp = NULL;

	/**
	 * Localization messages for the form UI. Associative array containing keys:
	 * 'bpAll', 'bpNone', 'bpYes', 'bpNo' and 'bpWarning'. When used from
	 * _list_builder, these messages are provided by framework localization
	 * files.
	 *
	 * @var <array>
	 */
	protected $messages = NULL;

	/**
	 * Array of checkboxes HTML ID's (keys) and ID passed to action code
	 * (identifier of atom).
	 *
	 * @var <array>
	 */
	protected $boxes = NULL;

	/**
	 * Action is abstraction of button with title and associated Javascript
	 * code. Action is executed for each (checked) item.
	 *
	 * @var <array>
	 */
	protected $actions = NULL;

	/**
	 * Name of Javascript instance of _uicmp_search() class.
	 *
	 * @var <string>
	 */
	protected $client_var = NULL;

	/**
	 * Used to generate HTML ID's for UICMP container.
	 *
	 * @var <int>
	 */
	protected static $lastId = 0;

	/**
	 * Constructor.
	 * 
	 * @param <string> $client_var name of Javascript variable
	 * @param <array> $messages framework localization messages
	 */
	public function __construct( $client_var, $messages )
	{
		$this->client_var	= $client_var;
		$this->messages		= $messages;
	}

	/**
	 * Lazy initialization of buttons group component.
	 *
	 * @return <_uicmp_buttons>
	 */
	public function getUicmp ( )
	{
		if ( is_null( $this->uicmp ) )
			$this->uicmp = new _uicmp_buttons ( $this, '' );

		return $this->uicmp;
	}

	/**
	 * Registers checkbox with given HTML ID into internal array. This array
	 * is then transformed into Javascript array and passed to control action
	 * method.
	 *
	 * @param <string> $html_id HTML ID of checkbox element
	 * @param <mixed> $id identifier of atom
	 */
	public function addChkbox ( $html_id, $id ) { $this->boxes[$html_id] = $id; }

	/**
	 * Registers code to be executed on each registered ID (checkbox). This
	 * method uses currently registered checkboxes, so first call of this
	 * method had better be performed after all checkboxes were registered.
	 *
	 * @param <string> $display text for button
	 * @param <string> $js_method Javascript method to be called for single atom
	 * @param <string> $warning message to be shown in Yes/No dialog before action is executed
	 */
	public function addAction ( $display, $js_method, $warning = NULL )
	{
		$buttons = $this->getUicmp( );

		/**
		 * Common part of Javascript codes.
		 */
		$var = 'var chkboxes = new Object( );' . $this->toJsArray( 'chkboxes' );
		$cycle = 'for ( html_id in chkboxes ) ';

		/**
		 * Create 'Check all' and 'Uncheck all' widgets.
		 */
		if ( $buttons->isEmpty( ) )
		{
			$js = $var . $cycle . 'document.getElementById( html_id ).checked = true;';
			$buttons->add( new _uicmp_gi( $buttons, '_bp_i_' . self::$lastId++, _uicmp_gi::IT_A, $this->messages['bpAll'], $js ) );

			$buttons->add( new _uicmp_gi( $buttons, '_bp_i_' . self::$lastId++, _uicmp_gi::IT_TXT, '|' ) );

			$js = $var . $cycle . 'document.getElementById( html_id ).checked = false;';
			$buttons->add( new _uicmp_gi( $buttons, '_bp_i_' . self::$lastId++, _uicmp_gi::IT_A, $this->messages['bpNone'], $js ) );
		}

		$js = '';

		if ( !is_null( $warning ) )
		{
			$js = $var;
			$js .= "var data = new Array( );if ( data['checked'] = _list_bp_checked( chkboxes ) ) { ";
			$js .= "data['client_var'] = _uicmp_lookup.lookup( '{$this->client_var}' );";
			$js .= "var yes = new _sd_dlg_bt ( {$js_method}, '{$this->messages['bpYes']}', data );";
			$js .= "var no = new _sd_dlg_bt ( null, '{$this->messages['bpNo']}', null );";
			$js .= "_wdg_dlg_yn.show( '{$this->messages['bpWarning']}', '{$warning}', yes, no );";
			$js .= "}";
		}
		else
		{
			/** @todo implement case when needed */
		}

		$buttons->add( new _uicmp_gi( $buttons, '_bp_i_' . self::$lastId++, _uicmp_gi::IT_TXT, '|' ) );
		$buttons->add( new _uicmp_gi( $buttons, '_bp_i_' . self::$lastId++, _uicmp_gi::IT_BT, $display, $js ) );
	}

	/**
	 * Generates Javascript array from checkboxes data. As there is Javascript
	 * syntax parsing error when using 'serialized' associative array in onClick
	 * event handler in Google Chrome, we had to resort to this.
	 *
	 * @return <string>
	 * @todo use caching to improve computational complexity
	 */
	public function toJsArray ( $jsVar )
	{
		$js = '';
		if ( is_array( $this->boxes ) )
			foreach ( $this->boxes as $html_id => $id )
				$js .= $jsVar . "['{$html_id}']={$id};";

		return $js;
	}
}

?>