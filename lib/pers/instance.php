<?php

/**
 * @file instance.php
 * @author giorno
 * @package Chassis
 * @subpackage Persistence
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\pers;

require_once CHASSIS_3RD . 'class.SimonsXmlWriter.php';

require_once CHASSIS_LIB . 'libdb.php';
require_once CHASSIS_LIB . 'class.Wa.php';

require_once CHASSIS_LIB . 'i18n/_i18n_loader.php';

require_once CHASSIS_LIB . 'pers/common.php';
require_once CHASSIS_LIB . 'pers/field.php';
require_once CHASSIS_LIB . 'pers/tui.php';
require_once CHASSIS_LIB . 'pers/rui.php';

require_once CHASSIS_LIB . 'list/_list_builder.php';

/**
 * Database table persistence object. Factory, which spawns objects creating UI,
 * client side logic and performing backend operations and which holds
 * definitions of the table.
 */
class instance extends \pers
{
	/**
	 * Name of database table controlled by this instance.
	 * @var string 
	 */
	protected $table = NULL;
	
	/**
	 * Array of column names used for unique identification of the record. It
	 * may contain single name (e.g. for primary key field).
	 * @var array
	 */
	protected $index = NULL;
	
	/**
	 * Flags bitmask for this instance. See constants in Persistence common.php.
	 * @var int 
	 */
	protected $flags = NULL;
	
	/**
	 * Definition of table fields.
	 * @var array 
	 */
	protected $fields = NULL;
	
	/**
	 * If not NULL, it is used as a map for fields layout in the search results.
	 * Elements of the array are names of fields. Array is integer indexed.
	 * @var array
	 */
	protected $map = NULL;
	
	/**
	 * Name of Javascript class, which is instantiated by this instance client
	 * requirements. Override in subclass to provide different client behaviour.
	 * @var string
	 */
	protected $jsClass = '_pers_instance';
	
	/**
	 * Reference to UICMP layout instance. It is also provider of framework
	 * localization.
	 * @var \io\creat\chassis\uicmp\layout
	 */
	protected $layout = NULL;
	
	/**
	 * Localization messages specific for the instance (framework own
	 * localization is provided internally). Associative array.
	 * @var array
	 */
	protected $messages = NULL;
	
	/**
	 * URL of Ajax server.
	 * @var string
	 */
	protected $url = NULL;
	
	/**
	 * Associative array of base set of Ajax parameters. These values should
	 * be used in application Ajax server implementation routine handling all
	 * persistence instances.
	 * @var array
	 */
	protected $params = NULL;
	
	/**
	 * Reference to table UI instance.
	 * @var io\creat\chassis\pers\tui
	 */
	protected $tui = NULL;
	
	/**
	 * Reference to record UI instance.
	 * @var io\creat\chassis\pers\rui
	 */
	protected $rui = NULL;
	
	/**
	 * Reference to settings proxy instance.
	 * @var \io\creat\chassis\pers\settproxy
	 */
	protected $settproxy = NULL;
	
	/**
	 * Constructor. Initializes Persistence instance.
	 * @param string $table name of the database table
	 * @param int $flags flags of the Persistence Instance.
	 * @param \io\creat\chassis\uicmp\layout $layout reference to UICMP layout instance
	 * @param array $messages array of localization messages
	 * @param string $url Ajax server URL
	 * @param array $params Ajax request parameters, identify Ajax server delivery channel
	 * @param \io\creat\chassis\pers\settproxy $settproxy instance of setting proxy providing access to specific setting entries
	 */
	public function __construct ( $table, $flags, &$layout, $messages, $url, $params, $settproxy = NULL )
	{
		$this->table			= $table;
		$this->flags			= $flags;
		$this->layout			= $layout;
		$this->messages			= $messages;
		$this->url				= $url;
		$this->params			= $params;
		$this->params['table']	= $this->table;		// identification of the instance (this could be a problem if two instances are used over single table)
		$this->params['jsvar']	= $this->jsVar( );	// name of Javascript variable holding my instance
		$this->settproxy		= $settproxy;
		
		$this->settproxy->pi( $this );
		$this->implicit( );
		$this->explicit( );
	}
	
	/**
	 * Provides identifier stub for derived UICMP components.
	 * @return string
	 * @todo cache
	 */
	public function id ( ) { return 'pers_' . $this->table; }
	
	/**
	 * Getter of table name for this instance.
	 * @return string
	 */
	public function name ( ) { return $this->table; }
	
	/**
	 * Provides array of indexes.
	 * @return array
	 */
	public function idx ( ) { return $this->index; }
	
	/**
	 * Generator of Javascript variable name.
	 * @return string
	 * @todo cache
	 */
	public function jsVar ( ) { return '_pers_i_' . $this->table; }
	
	/**
	 * Provides reference to fields definitions.
	 * @return array
	 */
	public function def ( ) { return $this->fields; }
	
	/**
	 * Provides reference to custom localization array.
	 * @return array
	 */
	public function msg ( ) { return $this->messages; }
	
	/**
	 * Checks if flag is set.
	 * @param int $flag flag to check
	 * @return bool
	 */
	public function has ( $flag ) { return ( $this->flags & $flag ) > 0; }
	
	/**
	 * Getter for explicit map of fields.
	 * @return array
	 */
	public function map ( ) { return $this->map; }
	
	/**
	 * Returns reference to current settings proxy instance.
	 * @return \io\creat\chassis\settproxy
	 */
	public function settproxy ( ) { return $this->settproxy; }
	
	/**
	 * Initialization of the client side logic instance. Sets up CSS and
	 * Javascript code executed on the client side. Implicitly initializes TUI
	 * and RUI.
	 */
	public function client ( )
	{
		$var = $this->jsVar( );
		$requirer = $this->layout->getRequirer( );
		$tcfg = ( !is_null( $this->tableUi( ) ) ) ? $this->tui->jsCfg( ) : 'null';
		$rcfg = ( !is_null( $this->recordUi( ) ) ) ? $this->rui->jsCfg( ) : 'null';
		$init = 'var ' . $var . " = new {$this->jsClass}( '{$this->table}', {$this->layout->getJsVar( )}, '{$this->url}', " . \io\creat\chassis\uicmp\vcmp::toJsArray( $this->params ) . ", {$tcfg}, {$rcfg} );";
		
		$this->tui->generateReqs( );
		if ( $this->rui instanceof rui )
			$this->rui->generateReqs( );
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_CSS, array( $requirer->getRelative() . 'css/_uicmp.css', __CLASS__ ) );	// a requirement
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_CSS, array( $requirer->getRelative() . 'css/_list.css', __CLASS__ ) );	// a requirement
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_CSS, array( $requirer->getRelative() . 'css/_tags.css', __CLASS__ ) );	// a requirement
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative() . 'js/_pers.js', __CLASS__ ) );
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative() . '3rd/XMLWriter-1.0.0-min.js', __CLASS__ ) );	// a requirement
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative() . 'js/wa.js', __CLASS__ ) );	// a requirement
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, $init );
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, $this->layout->getJsVar( ) . '.registerTabCb( \'' . $this->tui->id( ) . '\', \'onShow\', ' . $this->jsVar( ) . '.refresh );' );
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JSPLAIN, $this->layout->getJsVar( ) . '.registerTabCb( \'' . $this->tui->id( ) . '\', \'onLoad\', ' . $this->jsVar( ) . '.startup );' );
	}
	
	/**
	 * Provides associative array of options (value->display pairs) for
	 * a dynamic restrictor field. This implementation provides empty list
	 * (false). Overrride in the subclass.
	 * @param string $restrictor name (identifier) of the field
	 * @return mixed
	 */
	public function restrictions ( $restrictor ) { return false; }
	
	/**
	 * Extracts search data and return them in hierarchical array to be used to
	 * build a search query. Restrictors are omitted as they are parsed
	 * dynamically when building the query.
	 * @return array 
	 */
	protected function searchp ( )
	{
		$search = unserialize( $this->settproxy->lcfg( ) );
		$search['as'] = ( array_key_exists( 'as', $_POST ) && ( $_POST['as'] == 'true' ) ) ? true : false;
		
		if ( $search['as'] )
			$search['f'] = $_POST['f'];
		
		$search['k'] =  _db_escape( trim( $_POST['k'] ) );
		$search['p'] =  (int)$_POST['p'];
		$search['o'] =  $_POST['o'];
		$search['d'] =  $_POST['d'];
		$search['jsvar'] =  $_POST['jsvar'];
		
		return $search;
	}
	
	/**
	 * Populates AND and OR clauses of a search query for particular field. It
	 * is separated, so it can be overridden in the subclass to provide 
	 * alternate behaviour.
	 * @param array $search reference to parsed search query
	 * @param \io\creat\chassis\pers\field $field
	 * @param array $and reference to array, where to put AND clauses
	 * @param array $or reference to array, where to put OR clauses
	 */
	protected function fieldq ( &$search, &$field, &$and, &$or )
	{
		if ( $field instanceof field )
		{
			if ( $field->flags & field::FL_FD_CONST )
			{
				$and[] = "`{$field->name}` = \"" . _db_escape( $field->value ) . "\"";
			}
			elseif ( ( $search['as'] ) && ( $field->flags & field::FL_FD_RESTRICT ) )
			{
				if ( array_key_exists( 'r_' . $field->name, $_POST ) )
				{
					// populate parsed search query with restrictor setting
					$val = $search['r'][$field->name] = $_POST['r_' . $field->name];
					
					if ( $val != '[norestr]' )
						$and[] = "`{$field->name}` = \"" . _db_escape( $val ) . "\"";
				}
			}
			elseif ( ( ( $search['k'] != '' ) && ( $field->flags & field::FL_FD_SEARCH ) ) && ( !$search['as'] || ( ( $search['as'] ) && ( ( $search['f'] == '[allfields]' ) || ( $search['f'] == $field->name ) ) ) ) )
			{
				switch ( $field->type )
				{
					case field::FT_INT:
						$or[] = "`{$field->name}` = \"{$search['k']}\"";
					break;
				
					case field::FT_STRING:
						$or[] = "`{$field->name}` LIKE \"%{$search['k']}%\"";
					break;
				}
			}
		}
	}
	
	/**
	 * Builds ORDER BY clause of the search query. Separated, so it can be
	 * overridden in the subclass to provide alternate statement.
	 * @param array $search reference to parsed search query
	 * @return string
	 */
	protected function orderq ( &$search ) { return "ORDER BY `" . _db_escape( $search['o'] ) . "` " . _db_escape( $search['d'] ); }
	
	/**
	 * Builds core of the SQL query for searching in the table.
	 * @param array $search parsed search query, output of searchp()
	 * @return string
	 */
	protected function searchq ( &$search )
	{
		$and = '';
		$or = '';
		
		if ( is_array( $this->fields ) )
		{
			foreach( $this->fields as $field )
				$this->fieldq( $search, $field, $and, $or );
		}
		
		$where = '';
		
		if ( is_array( $or ) )
			$and[] = ' ( ' . implode( ' OR ', $or ) . ' ) ';
		if ( is_array( $and ) )
			$where = 'WHERE ' . implode( ' AND ', $and );
		
		return "FROM `{$this->table}` {$where}";
	}
	
	/**
	 * Builds a list header. Exposed as separate method to allow override in the
	 * subclass.
	 * @param \_list_builder $builder reference to List Builder instance
	 * @param array $search reference to parsed search query
	 */
	protected function listh ( &$builder, &$search )
	{
		/**
		 * Insert new header item.
		 * @param \_list_builder $builder reference to list builder instance
		 * @param \io\creat\chassis\pers\field $field field to test and insert
		 * @param array $search parsed search query
		 */
		function listhi( &$builder, &$field, &$search )
		{
			if ( $field instanceof field )
				if ( $field->flags & field::FL_FD_VIEW )
					$builder->addField(	$field->name,
										$field->title,
										$field->width,
										$field->colspan,
										$field->align,
										( ( $field->flags & field::FL_FD_ORDER ) > 0 ),
										( $search['o'] == $field->name ),
										$search['d'] );
		}
		
		if ( $builder instanceof \_list_builder )
		{
			if ( is_array( $this->map ) )
			{
				foreach ( $this->map as $name )
					if ( array_key_exists( $name, $this->fields ) )
						listhi( $builder, $this->fields[$name], $search );
			}
			else
				foreach ( $this->fields as $field )
					listhi( $builder, $field, $search );
		}
	}
	
	/**
	 * Produces row item instance (a cell) for a builder. Exposed as separate
	 * method to allow override in the subclass.
	 * @param \io\creat\chassis\pers\field $field configuration of the field
	 * @param array $record database fields for particular record
	 * @param array $search reference to parsed search query
	 */
	protected function listri ( &$field, &$record, &$search )
	{
		if ( $field instanceof field )
			if ( $field->flags & field::FL_FD_VIEW )
			{
				
				switch ( $field->type )
				{			
					case field::FT_TAG:
						if ( $field instanceof tag )
						{
							$field->cache( );

							if ( is_array( $field->cache ) )
								if ( ( (int)$record[$field->name] > 0 ) && ( array_key_exists( (int)$record[$field->name], $field->cache ) ) )
								{
									if ( ( $field->flags & field::FL_FD_ANCHOR ) && ( is_array( $this->index ) ) )
									{
										foreach ( $this->index as $i )
											$index[] = $record[$i];
										
										return new \_list_cell( \_list_cell::Badge( $field->cache[(int)$record[$field->name]]->id, $field->cache[(int)$record[$field->name]]->sch, $field->cache[(int)$record[$field->name]]->disp, '', $search['jsvar'] . '.rui.edit( \'' . implode( '::', $index ) . '\' );' ),
														\_list_cell::MAN_BADGE );
									}
									else
										return new \_list_cell( \_list_cell::Badge( $field->cache[(int)$record[$field->name]]->id, $field->cache[(int)$record[$field->name]]->sch, $field->cache[(int)$record[$field->name]]->disp ),
														\_list_cell::MAN_BADGE );
									break;
								}
						}
						return new \_list_cell( \_list_cell::Text( '' ) );
					break;
					
					default:
						if ( ( $field->flags & field::FL_FD_ANCHOR ) && ( is_array( $this->index ) ) )
						{
							foreach ( $this->index as $i )
								$index[] = $record[$i];
							return new \_list_cell( \_list_cell::deco( $record[$field->name], '', null, '', $search['jsvar'] . '.rui.edit( \'' . implode( '::', $index ) . '\' );' ), \_list_cell::MAN_DECO );
						}
						else
							return new \_list_cell( \_list_cell::Text( $record[$field->name] ) );
					break;
				}
			}	
	}
	
	/**
	 * Retrieves record from the table.
	 * @param array $index values for index map to identify the record in the SQL query
	 * @return mixed record array on success, FALSE on failure
	 */
	protected function record ( $index )
	{
		$and = NULL;
		if (is_array( $this->index ) )
			foreach( $this->index as $i => $name )
				if ( $i < count( $index ) )
					$and[] = "`" . _db_escape( $name ) . "` = \"" . _db_escape ( $index[$i] ) . "\"";
		
		if ( !is_array( $and ) )
			return FALSE;
		else
		{
			_db_query( "BEGIN" );
			$record = _db_1line( "SELECT * FROM `" . $this->table . "` WHERE " . implode( " AND ", $and ) );
			_db_query( "COMMIT" );
			return $record;
		}
	}
	
	/**
	 * Parses incoming request and produces XML document carrying data for
	 * the record editor (RUI).
	 * @return string
	 */
	protected function load ( )
	{
		$writer = new \SimonsXmlWriter( "\t" );
		
		$record = NULL;
		if ( array_key_exists( 'index', $_POST ) )
		{
			// loading specific record identified by index combination
			$index = explode( ',', $_POST['index'] );
			$record = $this->record( $index );

			$writer->push( 'rui', array( 'index' => $_POST['index'] ) );
		}
		else
			$writer->push( 'rui' );
		
		$fw_msg = $this->layout->getMsgs( );

		foreach ( $this->fields as $field )
		{
			if ( $field->flags & field::FL_FD_HIDDEN )
				continue;
			
			if ( is_array( $record ) )
				$writer->push( 'f', array( 'n' => $field->name, 'v' => $record[$field->name] ) );
			else
				$writer->push( 'f', array( 'n' => $field->name ) );
				
			if ( $field instanceof tag )
			{
				$writer->element ( 'o', $fw_msg['pers']['rui']['notag'], array( 'v' => '0' ) );
				$field->cache( );
				
				if ( is_array( $field->cache ) )
					foreach ( $field->cache as $tag )
						$writer->element ( 'o', $tag->disp, array( 'v' => $tag->id ) );
			}
			elseif ( $field instanceof field )
			{
				
			}
			
			$writer->pop( );
		}
		
		$writer->pop( );
		
		return $writer->getXml( );
	}
	
	/**
	 * Parses the client editor form save request XML and performs the save
	 * (INSERT or UPDATE) operation.
	 */
	protected function save ( )
	{
		$pairs = $keys = $vals = NULL;
		if ( array_key_exists( 'index', $_POST ) )
		{	
			if ( (string)$_POST['index'] != '' )
			{
				$index = explode( ',', $_POST['index'] );
				if ( is_array( $this->index ) )
					foreach( $this->index as $i => $name )
						if ( $i < count( $index ) )
						{
							$keys[] = "`" . _db_escape( $name ) . "`";
							$vals[] = "\"" . _db_escape ( $index[$i] ) . "\"";
						}
			}
			else
			{
				// This should be adding new record, hence we try to extract
				// constants for index values.
				if ( is_array( $this->index ) )
					foreach( $this->index as $i => $name )
						if ( $this->fields[$name]->flags & self::FL_FD_CONST )
						{
							$keys[] = "`" . _db_escape( $name ) . "`";
							$vals[] = "\"" . _db_escape ( $this->fields[$name]->value ) . "\"";
						}
			}
		}
		
		// parse XML message and extract data
		if ( ( $doc = simplexml_load_string( str_replace( ' standalone="false"', '', \Wa::PlusSignWaDecode( $_POST['data'] ) ) ) ) !== false )
		{
			$f = $doc->xpath( '//rui/f' );
			for ( $i = 0; $i < count( $f ); ++$i )
			{
				if ( in_array( $f[$i]['n'], $this->index ) )
					continue;
				
				$pairs[] = "`" . _db_escape( $f[$i]['n'] ) . "` = \"" . _db_escape ( $f[$i]['v'] ) . "\"";
				$keys[] = "`" . _db_escape( $f[$i]['n'] ) . "`";
				$vals[] = "\"" . _db_escape ( $f[$i]['v'] ) . "\"";
			}
		}
		
		_db_query( "BEGIN" );
		/**
		 * @todo validate result of the operation and return appropriate result
		 */
		_db_query( "INSERT INTO `" . $this->table . "` (" . implode( ',', $keys ) . ") VALUES (" . implode( ',', $vals ) . ") ON DUPLICATE KEY UPDATE " . implode( ',', $pairs ) . "" );
		_db_query( "COMMIT" );
	}
	
	/**
	 * Handles incoming POST request. This method should be called from Ajax
	 * delivery channel (e.g. Ajax application instance). Requires existence of
	 * _i18n_loader Singleton instance (its getInstance() method must be called
	 * with parameter at least once before call to this method).
	 */
	public function handle ( )
	{
		if ( array_key_exists( 'method', $_POST ) )
			switch ( $_POST['method'] )
			{
				// perform list length change
				case 'resize':
					$new = (int)$_POST['value'];
					if (array_key_exists( $new, array( 10 => 10, 20 => 20, 30 => 30, 50 => 50 ) ) )
						$this->settproxy->llen( $new );
				break;
				
				// loads form data, either for editing the user or only dynamic
				// content (for SELECT boxes)
				case 'load':
					if ( $_POST['primitive'] == 'rui' )
						echo $this->load( );
				break;
				
				// saves data from the form
				case 'save':
					if ( $_POST['primitive'] == 'rui' )
						echo ( $this->save( ) ? 'OK' : 'KO' );
				break;
				
				// to obtain restrictors values, it requires subclass to
				// implement the restrictions() method
				case 'getr':
					$writer = new \SimonsXmlWriter( '\t' );
					$writer->push( 'tui' );
					foreach( $this->fields as $field )
					{
						if ( $field->opts->flags & field::FL_FO_DYNAMIC )
						{
							$writer->push( 'r', array( 'n' => $field->name ) );
								if ( is_array( $options = $this->restrictions( $field->name ) ) )
									foreach( $options as $value => $name )
										$writer->element( 'o', $name, array( 'v' => $value ) );
							$writer->pop( );
						}
					}
					$writer->pop( );
					echo $writer->getXml( );
				break;
			
				// perform search by conditions passed from table UI
				case 'refresh':
					$params = $this->searchp( );
					$qstub = $this->searchq( $params );
					$query = "SELECT COUNT(*) " . $qstub;
					
					_db_query( "BEGIN" );
					$count = _db_1field( $query );
					_db_query( "COMMIT" );

					if ( $count !== false )
					{
						$llen = $this->settproxy->llen( );
						$pages = ceil( $count / $llen );
						if ( $pages < (int)$params['p'] )
							$page = $pages;
						else
							$page = (int)$params['p'];

						if ( $page < 1 )
							$page = 1;

						$first = ( $page - 1 )  * $llen;

						// foolproof the ordering information
						if ( ( !array_key_exists( $params['o'], $this->fields ) || ( ( $this->fields[$params['o']]->flags & field::FL_FD_ORDER ) == 0 ) ) && ( is_array( $this->fields ) ) )
							foreach ( $this->fields as $field )
								if ( ( $field instanceof field ) && ( $field->flags & field::FL_FD_ORDER ) )
								{
									$params['o'] = $field->name;
									$params['d'] = 'ASC';
									break;
								}
									
						if ( $params['d'] != 'DESC' )
							$params['d'] = 'ASC';
						
						$this->settproxy->lcfg( serialize( $params ) );
						
						// compose search SQL
						$order = $this->orderq( $params );
						$query = "SELECT * " . $qstub . " {$order} LIMIT {$first},{$llen}";
						
						_db_query( "BEGIN" );
						$res = _db_query( $query );
						_db_query( "COMMIT" );

						if ( $res && _db_rowcount( $res ) )
						{
							$builder = new \_list_builder( $params['jsvar'] . '.tui', \_i18n_loader::getInstance( ) );
								$this->listh( $builder, $params );
								$builder->computePaging( $llen, $count, $page, $pages, $this->settproxy->ph( ) );
							
							while ( $row = _db_fetchrow( $res ) )
							{
								if ( is_array( $this->fields ) )
								{
									$parameters = NULL;
									
									if ( is_array( $this->map ) )
										foreach ( $this->map as $name )
											$parameters[] = $this->listri( $this->fields[$name], $row, $params );
									else
										foreach ( $this->fields as $field )								
											$parameters[] = $this->listri( $field, $row, $params );

									if ( is_array( $parameters ) )
									{
										$str = NULL;
										for( $i = 0; $i< count( $parameters ); ++$i )
											if ( $parameters[$i] instanceof \_list_cell )
												$str[] = "\$parameters[" . $i . "]";
										
										eval( "\$builder->addRow(" . implode( ',', $str ) . ");" );
									}
								}
							}
							
							\_smarty_wrapper::getInstance( )->getEngine( )->assignByRef( 'USR_LIST_DATA', $builder->export( ) );
							\_smarty_wrapper::getInstance( )->setContent( CHASSIS_UI . '/list/list.html' );
							\_smarty_wrapper::getInstance( )->render( );
							
							return;
						}
						else // no results to display
						{
							// Framework localization strings.
							$lmsgs = $this->layout->getMsgs( );
							$empty['o1'] = $lmsgs['listEmptyOptions'];
							$empty['o2'] = $lmsgs['listEmptyLastOpt'];
							
							if ( trim( $params['k'] ) == '' )
							{
								$empty['msg'] = $this->messages['empty'];
								if ( $this->has( self::FL_PI_CREATE ) ) // make offer only if it makes sense
									$empty['act'] = array( $params['jsvar'] . '.rui.create();' => $this->messages['create'] ) ;
							}
							else
							{
								$empty['msg'] = $this->messages['nomatch'];
								$empty['act'] = array( $params['jsvar'] . '.tui.refresh();' => $this->messages['redo'], $params['jsvar'] . '.tui.showall();' => $this->messages['all'] ) ;
							}
							
							\_smarty_wrapper::getInstance( )->getEngine()->assignByRef( 'USR_PERS_EMPTY', $empty );
							\_smarty_wrapper::getInstance( )->setContent( CHASSIS_UI . '/pers/empty.html' );
							\_smarty_wrapper::getInstance( )->render( );
						}
					}
				break;
			}
	}

	/**
	 * Simple builder for whole table UI. This implementation provides search
	 * solution frontend by means of UICMP with configuration base on fields
	 * definition. Subclasses may override to provide alternate kind of objects.
	 * Method does not have a return value, but modifies member variable `tui`.
	 */
	protected function buildTui ( )
	{
		if ( $this->flags | self::FL_PI_TUI )
			$this->tui = new tui( $this, $this->layout );
	}
	
	/**
	 * Simple builder of single record UI. This implementation provides editor
	 * form instance by means of UICMP. Subclasses may override to provide
	 * different kind of objects. Method does not have a return value, but
	 * modifies member variable `rui`.
	 */
	protected function buildRui ( )
	{
		if ( $this->flags | self::FL_PI_RUI )
			$this->rui = new rui( $this, $this->layout );
	}
	
	/**
	 * Provider of table UI. Conditionally calls buildTableUi( ) for lazy
	 * initialization.
	 * 
	 * @return \io\creat\chassis\pers\tui
	 */
	public function tableUi ( )
	{
		if ( ( $this->flags & self::FL_PI_TUI ) && is_null( $this->tui ) )
			$this->buildTui( );
		
		return $this->tui;
	}
	
	/**
	 * Provider of record UI. Conditionally calls buildRecordUi( ) for lazy
	 * initialization.
	 * 
	 * @return \io\creat\chassis\pers\rui
	 */ 
	public function recordUi ( )
	{
		if ( ( $this->flags & self::FL_PI_RUI ) && is_null( $this->rui ) )
			$this->buildRui( );
		
		return $this->rui;
	}
	
	/**
	 * This method must be implemented in the subclass to create explicit
	 * definitions for the table or augment implictly detected ones.
	 * Implementation may have empty body.
	 */
	protected function explicit ( ) { }
	
	/**
	 * Creates implicit configuration of the table by extracting information
	 * about the table using database calls. Can be disabled in subclass by
	 * overriding with empty body.
	 */
	protected function implicit ( )
	{
		$res = _db_query( "DESCRIBE `" . _db_escape( $this->table ) . "`" );
		
		if ( $res && _db_rowcount( $res ) )
		{
			while ( $field = _db_fetchrow( $res ) )
			{
				$entry = new field( $field["Field"], field::FT_UNKNOWN, 0, $field["Field"] );
				$type = explode( "(", $field["Type"] );
				
				/**
				 * Default set of flags.
				 */
				$flags = field::FL_FD_MODIFY | field::FL_FD_ORDER | field::FL_FD_SEARCH | field::FL_FD_VIEW;
				
				switch ( $type[0] )
				{
					case 'int':
					case 'tinyint':
					case 'bigint':
					case 'mediumint':
						$entry->type = field::FT_INT;
					break;
				
					case 'char':
					case 'varchar':
					case 'text':
					case 'tinytext':
					case 'mediumtext':
					case 'bigtest':
						$entry->type = field::FT_STRING;
					break;
				}
				
				switch ( $field["Key"] )
				{
					case 'PRI':
						$flags |= field::FL_FD_PK;
						
						// subclass may override this
						$this->index = array( $entry->name );
					break;
				}
				
				$entry->flags = $flags;
				$this->fields[$entry->name] = $entry;
			}
		}
	}
}

?>