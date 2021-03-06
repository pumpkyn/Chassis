<?php

/**
 * @file instance.php
 * @author giorno
 * @package Chassis
 * @subpackage Tags
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\tags;

require_once CHASSIS_LIB . 'libdb.php';

require_once CHASSIS_LIB . 'pers/instance.php';

require_once CHASSIS_LIB . 'tags/common.php';
require_once CHASSIS_LIB . 'tags/rui.php';

/**
 * Specialization of Persistence instance for operations on tables of tags.
 */
class instance extends \io\creat\chassis\pers\instance implements \tags
{
	/**
	 * User ID used for security restrictions on the table. Security is turned
	 * off for negative values of user ID, e.g. for user ID 64 it keeps value
	 * -64.
	 * @var int
	 */
	protected $uid = 0;
	
	/**
	 * Constructor.
	 * 
	 * @param int $uid user ID
	 * @param string $table database table name
	 * @param string $headline text to display as TUI headline
	 * @param \io\creat\chassis\uicmp\layout $layout parent UICMP instance
	 * @param string $url Ajax server URL (a channel)
	 * @param array $params Ajax request base parameters
	 * @param \io\creat\chassis\tags\settproxy $proxy proxy instance for tags
	 * @param PDO $pdo PDO instance for this table
	 * @todo throw if proxy is not instanceof \io\creat\chassis\tags\settproxy
	 */
	public function __construct ( $uid, $table, $headline, &$layout, $url, $params, &$proxy, $pdo = NULL )
	{
		$this->uid		= $uid;	// needed in explicit(), therefore setting it here
		$this->jsClass	= '_tags_instance';
		
		$messages = $layout->getMsgs( );
		$messages['tags']['tui']['headline'] = $headline;
		
		parent::__construct(	$table,
								\pers::FL_PI_TUI | \pers::FL_PI_RUI | \pers::FL_PI_RESIZE | \pers::FL_PI_CREATE,
								$layout,
								$messages['tags'],
								$url,
								$params,
								$proxy,
								$pdo );
	}
	
	/**
	 * Decorating superclass method to explicitly load necessary Javascript
	 * libraries for client instances.
	 */
	public function client ( )
	{
		$requirer = $this->layout->getRequirer( );
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative( ) . 'js/_pers.js', __CLASS__ ) );	// a requirement
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative() . 'js/_tags.js', __CLASS__ ) );
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_CSS, array( $requirer->getRelative() . 'css/_tags.css', __CLASS__ ) );
		parent::client( );
	}
	
	/**
	 * Explicitly changing order and parameters of table fields.
	 */
	protected function explicit ( )
	{
		$this->fields[self::FN_REMOVE] = new \io\creat\chassis\pers\field(	self::FN_REMOVE,
																			\io\creat\chassis\pers\field::FT_ICON,
																			\io\creat\chassis\pers\field::FL_FD_VIEW | \io\creat\chassis\pers\field::FL_FD_HIDDEN,
																			'', '', '16px' );
				
		foreach ( $this->fields as $field )
			if ( $field instanceof \io\creat\chassis\pers\field )
			{
				switch ( $field->name )
				{
					case self::FN_NAME:
						$field->flags &= ~\io\creat\chassis\pers\field::FL_FD_VIEW;
						$field->flags &= ~\io\creat\chassis\pers\field::FL_FD_SEARCH;
						$field->flags |= \io\creat\chassis\pers\field::FL_FD_PREVIEW;
						$field->title = $this->messages['name'];
						$field->opts->flags |= \io\creat\chassis\pers\field::FL_FO_NE;
					break;
					
					case self::FN_DESC:
						$field->flags &= ~\io\creat\chassis\pers\field::FL_FD_ORDER;
						$field->title = $this->messages['desc'];
					break;

					case self::FN_ID:
						$field->flags &= ~\io\creat\chassis\pers\field::FL_FD_MODIFY;
						$field->flags &= ~\io\creat\chassis\pers\field::FL_FD_SEARCH;
						$field->flags |= \io\creat\chassis\pers\field::FL_FD_ANCHOR;
						$field->flags |= \io\creat\chassis\pers\field::FL_FD_HIDDEN;
						$field->type = \io\creat\chassis\pers\field::FT_TAG;
						$field->width = '120px';
						$field->title = $this->messages['label'];
						// circular reference to itself
						$this->fields[$field->name] = new \io\creat\chassis\pers\tag( $field, $this->table, $this->uid );
					break;
				
					case self::FN_UID:
						$field->flags |= \io\creat\chassis\pers\field::FL_FD_HIDDEN;
						$field->flags |= \io\creat\chassis\pers\field::FL_FD_CONST;
						$field->flags &= ~\io\creat\chassis\pers\field::FL_FD_MODIFY;
						$field->flags &= ~\io\creat\chassis\pers\field::FL_FD_SEARCH;
						$field->value = abs( $this->uid );
					break;
				
					case self::FN_SCHEME:
						$field->flags &= ~\io\creat\chassis\pers\field::FL_FD_VIEW;
						$field->flags &= ~\io\creat\chassis\pers\field::FL_FD_SEARCH;
						$field->flags |= \io\creat\chassis\pers\field::FL_FD_PREVIEW;
						$field->type = \io\creat\chassis\pers\field::FT_ENUM;
						$field->title = $this->messages['scheme'];

						foreach( $this->messages['schemes'] as $scheme => $name )
							$field->opts->set ( $scheme, $name );
					break;
				}
			}
		
		// overriding order of table fields
		$this->map[] = self::FN_ID;
		$this->map[] = self::FN_SCHEME;
		$this->map[] = self::FN_NAME;
		$this->map[] = self::FN_DESC;
		$this->map[] = self::FN_REMOVE;
		$this->index[] = self::FN_UID;
	}
	
	/**
	 * Overrides superclass implementation to catch remove operation.
	 */
	public function handle()
	{
		if ( $_POST['primitive'] == 'rui' )
		{
			switch ( $_POST['method'] )
			{
				// Remove tag from the table.
				case 'remove':
					if ( $this->uid < 0 )
						$this->pdo->prepare( "DELETE FROM `" . $this->table . "`
									WHERE `" . self::FN_ID . "` = ?")->execute( array( $_POST['id'] ) );
					else
						$this->pdo->prepare( "DELETE FROM `" . $this->table . "`
									WHERE `" . self::FN_ID . "` = ? AND `" . self::FN_UID . "` = ?")->execute( array( $_POST['id'], $this->uid ) );
					return;
				break;
				
				// Update setting of palette (visible/hidden)
				case 'palette':
					$this->settproxy->setl( 'usr.palette.shown.' . $this->table, ( ( (int)$_POST['shown'] == 0 ) ? 'false' : 'true' ) );
					return;
				break;
			}
		}
		parent::handle( );
	}
	
	/**
	 * Overrides superclass implementation to use customized elements
	 * (e.g. preview).
	 */
	protected function buildRui ( )
	{
		if ( $this->flags | self::FL_PI_RUI )
			$this->rui = new rui( $this, $this->layout );
	}
	
	/**
	 * Overriding superclass to add extra functionality for handling disabled
	 * security.
	 * @param array $search reference to parsed search query
	 * @param \io\creat\chassis\pers\field $field
	 * @param array $and reference to array, where to put AND clauses
	 * @param array $or reference to array, where to put OR clauses
	 */
	protected function fieldq ( &$search, &$field, &$and, &$or )
	{
		if ( $field instanceof \io\creat\chassis\pers\field )
		{
			if ( ( $field->name == self::FN_UID ) && ( $this->uid < 0 ) )	// bypassing security
				return;
			
			if ( $field->name == self::FN_REMOVE )
				return;
		}

		return parent::fieldq( $search, $field, $and, $or );
	}
	
	/**
	 * Overrides superclass ordering for tag badge.
	 * @param array $search reference to parsed search query
	 * @return string
	 */
	protected function orderq ( &$search )
	{
		if ( $search['o'] == self::FN_ID )
			return "ORDER BY " . $this->cacheq( self::FN_NAME ) . " " . $this->cacheq( $search['d'] );
		else
			return parent::orderq( $search );
	}
	
	/**
	 * Overrides superclass row item to add special handling of custom fields.
	 * @param \io\creat\chassis\pers\field $field configuration of the field
	 * @param array $record database fields for particular record
	 * @param array $search reference to parsed search query
	 */
	protected function listri ( &$field, &$record, &$search )
	{
		if ( $field->name == self::FN_REMOVE )
		{
			/** @todo implement remove callback when needed along with add callback */
			$rm_cb = '';
			/** @todo implement factory method for framework messages (usable also in other interfaces of this class) */
			$messages = $this->layout->getMsgs( );
			
			$code = "var data = new Array();";
			$code .= "data['id']=" . $record[self::FN_ID];
			$code .= ";data['jsvar']=" . $search['jsvar'] . ".rui;";
			$code .= "data['list']=_uicmp_lookup.lookup('" . $search['jsvar'] . ".tui');";
			$code .= ( ( $rm_cb != '' ) ? "data['cb']= {$rm_cb};" : "" );
			$code .= "var yes = new _sd_dlg_bt ( _tags_remove, '{$messages['bpYes']}', data );";
			$code .= "var no = new _sd_dlg_bt ( null, '{$messages['bpNo']}', null );";
			$code .= "_wdg_dlg_yn.show( '{$messages['bpWarning']}', ";
			$code .= "'" . sprintf( $this->messages['Q'], \Wa::JsStringEscape( $record[self::FN_NAME], ENT_QUOTES ) ) . "', yes, no );";
			
			return new \_list_cell(	\_list_cell::Code(	$code, $this->messages['remove'] ), \_list_cell::MAN_ICONREMOVE );
		}
		else	
			return parent::listri( $field, $record, $search );
	}
}

?>