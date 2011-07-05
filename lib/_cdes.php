<?php

require_once CHASSIS_CFG . 'class.Config.php';
require_once CHASSIS_LIB . 'class.Wa.php';
require_once CHASSIS_LIB . 'libdb.php';

require_once CHASSIS_LIB . 'list/_list_builder.php';
require_once CHASSIS_LIB . 'list/_list_cell.php';

require_once CHASSIS_LIB . 'ui/_ctx.php';
require_once CHASSIS_LIB . 'ui/_smarty_wrapper.php';

/**
 * @file _cdes.php
 * @author giorno
 * @package Chassis
 *
 * Class for CDES (Context Displaying and Editing Solution) backend operations.
 */
class _cdes
{
	/*
	 * Database table fields names.
	 */
	const F_CTXUID    = Config::F_UID;
	const F_CTXID     = 'CID';
	const F_CTXSCHEME = 'scheme';
	const F_CTXNAME   = 'name';
	const F_CTXDESC   = 'desc';

	/*
	 * Error codes.
	 */
	const E_ALREADYEXISTS = 2;
	const E_INVALIDNAME   = 4;
	const E_UNKNOWN       = 8;

	/*
	 * Name of database (MySQL) table.
	 */
	protected $tableName = '';

	/*
	 * (Array) Details of the context.
	 */
	protected $Context = null;

	/*
	 * (Array) Error indications. Should be null for no errors.
	 */
	public $Errors = null;

	/*
	 * UserId.
	 */
	protected $UID = null;

	protected $messages = NULL;

	/*
	 * Set database table as contexts storage.
	 */
	function __construct ( $UID, $table, $lang = 'en' )
	{
		$this->UID = $UID;
		$this->tableName = $table;

		$i18n = CHASSIS_I18N . 'uicmp/' . $lang . '.php';
		if (file_exists( $i18n ) )
			include $i18n;
		else
			include CHASSIS_I18N . 'uicmp/en.php';

		$this->messages = $_uicmp_i18n;
	}

	/**
	 * Perform search on contexts.
	 *
	 * @param _list_cfg $list_cfg backend for saving configuration of the list
	 * @param string $js_id search solution client side id (for _list_builder)
	 * @param string $cdes_ed Javascript variable for client side of editor solution
	 * @param int $pageSize number of entries per page
	 * @param int $pager_half half-size of pager
	 * @param string $keyword search phrase
	 * @param int $page page of the results list to display
	 * @param string $order field to order list by
	 * @param string $dir direction of ordering
	 * @param string $rm_cb extra callback to be called after remove operation 
	 * @return mixed array with results or false
	 */
	public function display( $list_cfg, $js_id, $cdes_ed, $pageSize, $pager_half, $keyword, $page, $order = self::F_CTXNAME, $dir = 'ASC', $rm_cb = '' )
	{
		if ( $dir != 'DESC' ) $dir = 'ASC';

		if ( trim( $keyword ) != '' )
			$where = "AND ( `" . self::F_CTXNAME . "` LIKE \"%" . _db_escape( $keyword ) . "%\"
							OR `" . self::F_CTXDESC . "` LIKE \"%" . _db_escape( $keyword ) . "%\" )";
		else
			$where = '';

		switch ( $order )
		{
			default:	$order = self::F_CTXNAME;	break;
		}

		$orderBy = "`" . $order . "` {$dir}";

		/**
		 * Create list builder and it header.
		 */
		$builder = new _list_builder( $js_id );
			$builder->addField( self::F_CTXNAME, $this->messages['cdesContext'], '150px', 1, 'left', true, ( $order == self::F_CTXNAME ), $dir );
			$builder->addField( self::F_CTXDESC, $this->messages['cdesDesc'], '*', 1, '', false );
			$builder->addField( '__rem', '', '0px', 1, '', false );

		$itemCount = _db_1field( "SELECT COUNT(*) FROM `" . _db_escape( $this->tableName ) . "` WHERE `" . self::F_CTXUID . "` = \"" . _db_escape( $this->UID ) . "\" {$where}" );

		$pageCount = ceil( $itemCount / $pageSize );
		if ( $page > $pageCount )
			$page = $pageCount;
		elseif ( $page < 1 )
			$page = 1;

		$firstItem = ( $page - 1 ) * $pageSize;

		$builder->computePaging( $pageSize, $itemCount, $page, $pageCount, $pager_half);

		/*if ( !is_null( $cfg_be ) )
			$cfg_be->saveListCfg( $cfg_id, $keyword, $order, $dir, $page );*/
		$list_cfg->save( $keyword, $order, $dir, $page );

		/**
		 * There is no match for search parameters.
		 * 
		 * @todo rethink if this breaks view concept.
		 */
		if ( $itemCount < 1 )
		{
			if ( trim( $keyword ) != '' )
			{
				$empty = new _list_empty( $this->messages['cdesNoMatch'] );
				$empty->add( $this->messages['cdesOSearch'], "_uicmp_lookup.lookup( '{$js_id}' ).focus();" );
				$empty->add( $this->messages['cdesOShowAll'], "_uicmp_lookup.lookup( '{$js_id}' ).showAll();" );
			}
			else
			{
				$empty = new _list_empty( $this->messages['cdesEmpty'] );
				$empty->add( $this->messages['cdesOCreate'], "{$cdes_ed}.create();" );
			}
			$empty->render( );
			return;// false;
		}

		$res = _db_query( "SELECT * FROM `" . _db_escape( $this->tableName ) . "` WHERE `" . self::F_CTXUID . "` = \"" . _db_escape( $this->UID ) . "\" {$where} ORDER BY {$orderBy}
							LIMIT " . _db_escape( $firstItem ) . "," . _db_escape( $pageSize ) );

		if ( $res && _db_rowcount( $res ) )
		{
			while( $row = _db_fetchrow( $res ) )
			{
				$builder->addRow(	new _list_cell(	_list_cell::Badge(	$row[self::F_CTXID],
																		$row[self::F_CTXSCHEME],
																		$row[self::F_CTXNAME],
																		$row[self::F_CTXDESC],
																		"{$cdes_ed}.edit('" . Wa::JsStringEscape( $row[self::F_CTXID] ) . "','" . Wa::JsStringEscape( $row[self::F_CTXSCHEME] ) . "','" . Wa::JsStringEscape( $row[self::F_CTXNAME] ) . "','" . Wa::JsStringEscape( $row[self::F_CTXDESC] ) . "')" ),
													_list_cell::MAN_BADGE ),

									new _list_cell(	_list_cell::Text(	$row[self::F_CTXDESC] ),
													_list_cell::MAN_DEFAULT ),

									new _list_cell(	_list_cell::Code(	"var data = new Array();data['id']=" . $row[self::F_CTXID] . ";data['client_var']={$cdes_ed};data['list']=_uicmp_lookup.lookup('{$js_id}'); " . ( ( $rm_cb != '' ) ? "data['cb']= {$rm_cb};" : "" ) . "var yes = new _sd_dlg_bt ( _uicmp_cdes_remove, '{$this->messages['bpYes']}', data );var no = new _sd_dlg_bt ( null, '{$this->messages['bpNo']}', null );_wdg_dlg_yn.show( '{$this->messages['bpWarning']}', '" . sprintf( $this->messages['cdesQuestion'], Wa::JsStringEscape( $row[self::F_CTXNAME], ENT_QUOTES ) ) . "', yes, no );",
										/*"ctxHelper.removeAsk( " . $row[self::F_CTXID] . ", 'ctxList" . $row[self::F_CTXID] . "' );",*/
																		$this->messages['cdesRemove'] ),
													_list_cell::MAN_ICONREMOVE ) );
			}
		}

		//return $builder->export( );
		
		_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'USR_LIST_DATA', $builder->export( ) );
		_smarty_wrapper::getInstance( )->setContent( CHASSIS_UI . '/list/list.html' );
		_smarty_wrapper::getInstance( )->render( );
	}

	/**
	 * Check if there is already context with the name in the table.
	 *
	 * @return <bool> false if not, CID if yes
	 */
	public function exists ( $disp )
	{
		return _db_1field( "SELECT `" . self::F_CTXID ."` FROM `" . _db_escape( $this->tableName ) . "` WHERE `" . self::F_CTXUID . "` = \"" . _db_escape( $this->UID ) . "\" AND `" . self::F_CTXNAME . "` = \"" . _db_escape( $disp ) . "\"" );
	}

	/**
	 * Save context into database.
	 *
	 * @return <int> id of context
	 */
	public function add ( $id, $scheme, $disp, $desc )
	{
		$data = "`" . self::F_CTXUID . "` = \"" . _db_escape( $this->UID ) . "\",
				`" . self::F_CTXSCHEME . "` = \"" . _db_escape( $scheme ) . "\",
				`" . self::F_CTXNAME . "` = \"" . _db_escape( $disp ) . "\",
				`" . self::F_CTXDESC . "` = \"" . _db_escape( $desc ) . "\"";
		
		if ( (int)$id == 0 )
		{
			_db_query( "INSERT INTO `" . _db_escape( $this->tableName ) . "`
						SET {$data}" );
			$id = _db_1field( "SELECT LAST_INSERT_ID()" );
		}
		else
		{
			_db_query( "UPDATE `" . _db_escape( $this->tableName ) . "`
						SET {$data}
						WHERE `" . self::F_CTXUID . "` = \"" . _db_escape( $this->UID ) . "\" AND `" . self::F_CTXID . "` = \"" . _db_escape( $id ) . "\"" );
		}

		return $id;
	}

	/**
	 * Provide all user's contexts as array.
	 *
	 * @param <int> UserId
	 * @param <string> name of contexts table
	 * @return <array> or <null>
	 */
	public static function allCtxs ( $UID, $table )
	{
		$ret = null;
		$res = _db_query( "SELECT * FROM `" . _db_escape( $table ) . "` WHERE `" . self::F_CTXUID . "` = \"" . _db_escape( $UID ) . "\" ORDER BY `" . self::F_CTXNAME . "`" );

		if ( $res && _db_rowcount( $res ) )
		{
			while ( $row = _db_fetchrow( $res ) )
			{
				$ret[$row[self::F_CTXID]] = new _ctx( $row[self::F_CTXID], $row[self::F_CTXSCHEME], $row[self::F_CTXNAME] );
			}
		}
		return $ret;
	}

	/**
	 * Provide array with data for badges composition (e.g. in lists).
	 *
	 * @param <array> all contexts for user and table
	 * @param <string> (custom) serialized item contexts
	 * @return <array> or <null> if there are not contexts in $serialized
	 */
	public static function badges ( $bank, $serialized )
	{
		$contexts = self::unserialize( $serialized );
		$ctx = null;
		if ( is_array( $contexts ) && ( is_array( $bank ) ) )
		{
			foreach ( $bank as $cid => $val )
			{
				if ( array_key_exists( $cid , $contexts ) )
					$ctx[$cid] = $bank[$cid];
			}
		}
		else
			$ctx = null;
		return $ctx;
	}

	/*
	 * Remove context from database.
	 *
	 * @param ContextId
	 */
	public function remove ( $id )
	{
		_db_query( "DELETE FROM `" . $this->tableName . "` WHERE `" . self::F_CTXUID . "` = \"" . _db_escape( $this->UID ) . "\" AND `" . self::F_CTXID . "` = \"" . _db_escape( $id ) . "\"" );
	}

	/*
	 * Very simple serializer of array with context Id's as keys.
	 */
	static public function serialize ( $contexts )
	{
		$ret = '';
		if ( is_array( $contexts ) )
		{
			$ret = '|';
			foreach ( $contexts as $cid => $val )
			{
				$ret .= $cid . '|';
			}
		}
		return $ret;
	}

	/*
	 * Very simple unserializer - counterpart to self::Serialize().
	 */
	static public function unserialize ( $plain )
	{
		$ret = null;
		if ( strlen( $plain ) > 0 )
		{
			$arr = explode( '|', $plain );
			if ( is_array ( $arr ) )
			{
				foreach ( $arr as $cid )
				{
					if ( (int)$cid != 0 )
						$ret[$cid] = true;
				}
			}
		}

		return $ret;
	}

}

?>