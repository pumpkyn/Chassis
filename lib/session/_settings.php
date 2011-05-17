<?php

/**
 * @file _settings.php
 * @author giorno
 * @package Chassis
 * @subpackage Session
 *
 * Class responsible for accessing and manipulating variable scope settings. It
 * is configured only by scope of the settings. Security identifiers are
 * extracted internaly from _session_wrapper instance.
 */

require_once CHASSIS_CFG . 'class.Config.php';
require_once CHASSIS_LIB . 'session/_session_wrapper.php';

abstract class _settings extends Config
{
	/**
	 * Constants to identify scope of the instance. These should correspond with
	 * values used in database table.
	 */
	const SCOPE_GLOBAL		= 'G';
	const SCOPE_USER		= 'U';
	const SCOPE_SESSION		= 'S';

	/**
	 * Database table to operate on. Its structure should be consistent with
	 * tSettings table.
	 *
	 * @var <string>
	 */
	protected $tableName = self::T_SETTINGS;

	/**
	 * Security identifier. Its value type depends on scope. For USER scope it
	 * will be user Id, for SESSION scope session Id. GLOBAL scope does not use
	 * identifier as it is reserved for solution wide settings.
	 * 
	 * @var <mixed> 
	 */
	protected $id = NULL;

	/**
	 * Scope of the instance. NULL value indicates security problem.
	 * 
	 * @var <char>
	 */
	protected $scope = NULL;

	/**
	 * Namespace. Additional index for database table.
	 *
	 * @var <string>
	 */
	protected $ns = NULL;

	/**
	 * Internal cache for settings.
	 *
	 * @var <array>
	 */
	protected $table = NULL;

	/**
	 * Constructor
	 *
	 * @param <char> $scope defines scope of instance
	 * @param <string> $ns namespace (e.g. solution)
	 */
	public function __construct ( $scope, $ns )
	{
		switch ( $scope )
		{
			case static::SCOPE_GLOBAL:
				$this->id = '';
			break;
		
			case static::SCOPE_USER:
				if ( _session_wrapper::getInstance( )->isSigned( ) )
					$this->id = _session_wrapper::getInstance( )->getUid( );
			break;

			case static::SCOPE_SESSION:
				if ( _session_wrapper::getInstance( )->isSigned( ) )
					$this->id = _session_wrapper::getInstance( )->getSid( );
			break;

			default:
				return;
			break;
		}

		$this->scope = $scope;
		$this->ns = $ns;
		$this->load( );
	}

	/**
	 * Cleans table part matching given namespace and global scope. Loads
	 * settings presets SQL file, binds table name and namespace and runs
	 * queries to create globally defined defaults.
	 * 
	 * @param <string> $file path to SQL file with default settings
	 * @param <string> $ns namespace to clean and replace
	 * @param <bool> $tr true if caller use transaction
	 * @param <string> $table table to modify, default taken from framework config
	 * @return <bool>
	 */
	public static function run ( $file, $ns, $tr = true, $table = self::T_SETTINGS )
	{
		if ( file_exists(  $file ) )
		{
			$script = file_get_contents( $file );
			$comments = array( '/\s*--.*\n/' );
			$script = preg_replace( $comments, "\n", $script );
			$script = str_replace( '{$__1}', $table, $script );
			$script = str_replace( '{$__2}', $ns, $script );
			$statements = explode( ";\n", $script );

			echo $script;
			if ( !$tr ) _db_query( "BEGIN" );
			if ( is_array( $statements ) )
				foreach( $statements as $statement )
					_db_query( $statement );
			if ( !$tr ) _db_query( "COMMIT" );

			return true;
		}

		return false;
	}

	/**
	 * Load all settings to inner array.
	 */
	public function load ( )
	{
		if ( $this->scope == self::SCOPE_USER )
			$fallback = " OR (`" . self::F_SCOPE . "` = \"" . _db_escape( self::SCOPE_GLOBAL ) . "\") ";
		else
			$fallback = '';
		
		$res = _db_query( "SELECT * FROM `" . self::T_SETTINGS . "`
					WHERE ( (`" . self::F_SCOPE . "` = \"" . _db_escape( $this->scope ) . "\"
							AND `" . self::F_ID . "` = \"" . _db_escape( $this->id ) ."\") {$fallback} )
							AND (`" . self::F_NS . "` = \"" . _db_escape( $this->ns ) . "\")
							" );

		if ( $res && _db_rowcount( $res ) )
		{
			while ( $record = _db_fetchrow( $res ) )
			{
				$this->table[$record[self::F_KEY]] = $record[self::F_VALUE];
			}
		}
	}

	/**
	 * Saves value of setting in transaction.
	 *
	 * @param key key
	 * @param value value
	 */
	public function saveOne ( $key, $value )
	{
		_db_query( "BEGIN" );
			/*_db_query( "DELETE FROM `" . self::T_SETTINGS . "`
						WHERE `" . self::F_ID . "` = \"" . _db_escape( $this->id ) ."\" AND `" . self::F_NS . "` = \"" . _db_escape( $this->ns ) . "\" AND `" . self::F_SCOPE . "` = \"" . _db_escape( $this->scope ) . "\" AND `" . self::F_KEY . "` = \"" . _db_escape( $key ) . "\"" );
			_db_query( "INSERT INTO `" . self::T_SETTINGS . "`
						SET `" . self::F_KEY . "` = \"" . _db_escape( $key ) . "\", `" . self::F_VALUE . "` = \"" . _db_escape( $value ) . "\", `" . self::F_ID . "` = \"" . _db_escape( $this->id ) ."\", `" . self::F_NS . "` = \"" . _db_escape( $this->ns ) . "\", `" . self::F_SCOPE . "` = \"" . _db_escape( $this->scope ) . "\"" );*/
			$this->saveAtom( $key, $value );
		_db_query( "COMMIT" );
	}

	/**
	 * Transaction-less
	 *
	 * @param <type> $key
	 * @param <type> $value
	 */
	private function saveAtom ( $key, $value )
	{
		_db_query( "DELETE FROM `" . self::T_SETTINGS . "`
					WHERE `" . self::F_ID . "` = \"" . _db_escape( $this->id ) ."\" AND `" . self::F_NS . "` = \"" . _db_escape( $this->ns ) . "\" AND `" . self::F_SCOPE . "` = \"" . _db_escape( $this->scope ) . "\" AND `" . self::F_KEY . "` = \"" . _db_escape( $key ) . "\"" );

		_db_query( "INSERT INTO `" . self::T_SETTINGS . "`
					SET `" . self::F_KEY . "` = \"" . _db_escape( $key ) . "\", `" . self::F_VALUE . "` = \"" . _db_escape( $value ) . "\", `" . self::F_ID . "` = \"" . _db_escape( $this->id ) ."\", `" . self::F_NS . "` = \"" . _db_escape( $this->ns ) . "\", `" . self::F_SCOPE . "` = \"" . _db_escape( $this->scope ) . "\"" );
	}

	/**
	 * Saves configuration of list.
	 *
	 * @param <type> $id
	 * @param <type> $keywords
	 * @param <type> $order
	 * @param <type> $dir
	 * @param <type> $page
	 */
	public function saveListCfg ( $id, $keywords, $order, $dir, $page )
	{
		_db_query( "BEGIN" );
			$this->saveAtom( 'list.' . $id . '.k', $keywords );
			$this->saveAtom( 'list.' . $id . '.o', $order );
			$this->saveAtom( 'list.' . $id . '.d', $dir );
			$this->saveAtom( 'list.' . $id . '.p', $page );
		_db_query( "COMMIT" );
	}

	/**
	 * Returns array with list configuration.
	 * 
	 * @param <string> $id list configuration identifier
	 * @return <array>
	 */
	public function getListCfg ( $id )
	{
		return Array(	'k' => $this->get( 'list.' . $id . '.k' ),
						'p' => $this->get( 'list.' . $id . '.p' ),
						'o' => $this->get( 'list.' . $id . '.o' ),
						'd' => $this->get( 'list.' . $id . '.d' ));
	}


	/**
	 * Creates default setting for the list configuration.
	 *
	 * @param <string> $id identifier of list configuration
	 * @param <string> $order default order field
	 * @param <string> $dir default order direction
	 */
	public function presetListCfg ( $id, $order = '', $dir = 'ASC' )
	{
		$this->table['_list_cfg_' . $id . '_keywords'] = '';
		$this->table['_list_cfg_' . $id . '_page'] = 1;
		$this->table['_list_cfg_' . $id . '_order'] = $order;
		$this->table['_list_cfg_' . $id . '_dir'] = $dir;
	}

	/**
	 * Return value by key.
	 *
	 * @param key key
	 * @return mixed
	 */
	public function get ( $key )
	{
		if ( array_key_exists( $key, $this->table ) )
			return $this->table[$key];
		else
			return null;
	}
}

?>