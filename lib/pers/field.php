<?php

/**
 * @file field.php
 * @author giorno
 * @package Chassis
 * @subpackage Persistence
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\pers;

require_once CHASSIS_LIB . '_cdes.php';
require_once CHASSIS_LIB . 'pers/common.php';
require_once CHASSIS_LIB . 'session/repo.php';

/**
 * Options container for definition of multivalued field options. Applicable
 * only to multivalued fiels as their data member.
 */
class fopts extends \pers
{
	public $values = NULL;
	public $flags = 0;
	
	/**
	 * Constructor creating empty instance with given flags.
	 * @param type $flags flags to be passed to this options container
	 */
	public function __construct( $flags = 0 ) { $this->flags = $flags; }
	
	/**
	 * Insert new option into options container.
	 * @param string $key identifier of the option displayed in UI
	 * @param string $value vaue for the option
	 */
	public function set ( $key, $value ) { $this->values[$key] = $value; }
}

/**
 * Definition of record field.
 */
class field extends \pers
{
	/**
	 * Database table column name for the field. Unique identifier of the field
	 * in the table instance. 
	 * @var string
	 */
	public $name		= NULL;
	
	/**
	 * Bitmask of the field flags.
	 * @var int
	 */
	public $flags		= 0;
	
	/**
	 * Type of the value stored in the field.
	 * @var int
	 */
	public $type		= 0;
	
	/**
	 * Text displayed in the editor and search results as a name of the field.
	 * @var string
	 */
	public $title		= NULL;
	
	/**
	 * Description of the field (used e.g. in the form).
	 * @var type 
	 */
	public $desc		= NULL;
	
	/**
	 * Specification of width for the field when displayed in the table UI.
	 * Valid sentences are: '*', '100px', '10%'. This is directly passed to
	 * generated HTML code and interpreted by browser rendering engine.
	 * @var string
	 */
	public $width		= '*';
	
	/**
	 * Solspan value for the list header. Directly passed in HTML to the
	 * browser.
	 * @var int
	 */
	public $colspan		= 1;
	
	/**
	 * Alignment of the field header and content. Passed in HTML to the browser.
	 * @var string
	 */
	public $align		= 'left';
	
	/**
	 * Options for enums, lists and other multivalued fields. Optional.
	 * @var fopts
	 */
	public $opts		= NULL;
	
	/**
	 * Used for constant values.
	 * @var mixed
	 */
	public $value		= NULL;
	
	/**
	 * Constructor. Initialized instance.
	 * @param string $name name of the field (table column)
	 * @param int $type type of values stored in the column
	 * @param int $flags flags of the field
	 * @param string $title localized title used in UI
	 * @param string $desc description string, used in UI
	 * @param string $width width for search result list
	 * @param string $colspan colspan for search result list
	 * @param string $align text alignment in UI
	 */
	public function __construct( $name, $type, $flags, $title, $desc = '', $width = '*', $colspan = 1, $align = 'left' )
	{
		$this->name		= $name;
		$this->type		= $type;
		$this->flags	= $flags;
		$this->title	= $title;
		$this->desc		= $desc;
		$this->width	= $width;
		$this->colspan	= $colspan;
		$this->align	= $align;
		
		$this->opts		= new fopts( );
	}
}

/**
 * Foreign key field, having reference on another table.
 */
class fk extends field
{
	/**
	 * Name of the tags table.
	 * @var string
	 */
	public $table = NULL;
	
	/**
	 * Foreign table field used for restriction of the result set.
	 * @var string
	 */
	public $rkey = NULL;
	
	/**
	 * Foreign table field value used for restriction of the result set.
	 * @var string
	 */
	public $rval = NULL;
	
	/**
	 * Foreign table field containing names.
	 * @var string
	 */
	public $names = NULL;
	
	/**
	 * PDO instance for database operation.
	 * @var PDO
	 */
	public $pdo = NULL;
	
	/**
	 * Simple constructor taking instance of field as a prototype and metadata
	 * to produce new instance to replace original instance of field.
	 * @param \io\creat\chassis\pers\field $field reference to original instance
	 * @param string $table foreign table name
	 * @param string $names field of the foreign table containing names
	 * @param array $restr pair of field name (key) and value (value, from foreign table) to restrict the dataset
	 * @param PDO $pdo PDO instance, if NULL (default), global repository PDO is used
	 */
	public function __construct ( &$field, $table, $names, $restr = NULL, $pdo = NULL )
	{
		parent::__construct(	$field->name,
								$field->type,
								$field->flags,
								$field->title,
								$field->desc,
								$field->width,
								$field->colspan,
								$field->align );
		
		$this->table = $table;
		$this->names = $names;
		$this->opts = new fopts( self::FL_FO_DYNAMIC );
		
		if ( is_array( $restr ) )
		{
			$this->rkey = key( $restr );
			$this->rval = $restr[$key];
		}
		
		if ( is_null( $pdo ) )
			$this->pdo = \io\creat\chassis\session\repo::getInstance( )->get(\io\creat\chassis\session\repo::PDO );
		else
			$this->pdo = $pdo;
	}
	
	/**
	 * Loads foreign table content in cached manner.
	 * @return foreign table dataset
	 */
	public function load ( )
	{
		if ( !is_array( $this->opts->values ) )
		{	
			if ( is_null( $this->rkey ) )
			{
				$sql = $this->pdo->prepare( "SELECT `" . $this->name . "`,`" . $this->names . "`
							FROM `" . $this->table . "`
							ORDER BY `" . $this->names . "`" );
				$result = $sql->execute( );
			}
			else
			{
				$sql = $this->pdo->prepare( "SELECT `" . $this->name . "`,`" . $this->names . "`
							FROM `" . $this->table . "`
							WHERE `" . $this->rkey . "` = ?
							ORDER BY `" . $this->names . "`" );
				$result =  $sql->execute( array( $this->rval ) );
			}

			if ( $result )
			{
				while ( $entry = $sql->fetch( \PDO::FETCH_NUM ) )
					$this->opts->set ( $entry[0], $entry[1] );
			}
		}
		
		return $this->opts->values;
	}
}

/**
 * Specialized field for use as tag column. Tag field contains ID from another
 * table of tags (former contexts or labels). These metadata are extra member
 * variables of the object.
 */
class tag extends field
{
	/**
	 * Name of the tags table.
	 * @var string
	 */
	public $table = NULL;
	
	/**
	 * User ID. Standard table of tags maintains tags per user. By this all
	 * results provided on this object configuration are filtered to achieve
	 * security. Negative value means that tags are available to anyone (no
	 * security restrictions).
	 * @var int
	 */
	public $uid = 0;
	
	/**
	 * Array of context instances for all tag values. Used only for specific
	 * tasks.
	 * @var array
	 */
	public $cache = NULL;
	
	/**
	 * PDO instance for database operation.
	 * @var PDO
	 */
	protected $pdo = NULL;
	
	/**
	 * Simple constructor taking instance of field as a prototype and metadata
	 * to produce new instance to replace original instance of field.
	 * @param \io\creat\chassis\pers\field $field reference to original instance
	 * @param string $table tag table name
	 * @param int $uid user ID, pass negative value to turn off per-user security restriction
	 */
	public function __construct ( &$field, $table, $uid, $pdo = NULL )
	{
		parent::__construct(	$field->name,
								$field->type,
								$field->flags,
								$field->title,
								$field->desc,
								$field->width,
								$field->colspan,
								$field->align );
		
		$this->table	= $table;
		$this->uid		= $uid;
		$this->opts		= new fopts( self::FL_FO_DYNAMIC );
		
		if ( is_null( $pdo ) )
			$this->pdo = \io\creat\chassis\session\repo::getInstance( )->get(\io\creat\chassis\session\repo::PDO );
		else
			$this->pdo = $pdo;
	}
	
	/**
	 * (Loads into cache and) provides cached list of _ctx instances.
	 * @return array
	 */
	public function cache ( )
	{
		if ( is_null( $this->cache ) )
		{
			if ( $this->uid < 0 )
				$sql = $this->pdo->prepare( "SELECT * FROM `" . $this->table . "`
						ORDER BY `" . \io\creat\chassis\tags\instance::FN_NAME . "`" );
			else
			{
				$sql = $this->pdo->prepare( "SELECT * FROM `" . $this->table . "`
						WHERE `" . \io\creat\chassis\tags\instance::FN_UID . "` = ?
						ORDER BY `" . \io\creat\chassis\tags\instance::FN_NAME . "`" );
				$sql->bindValue( 1, $this->uid );
			}
			
			if ( $sql->execute( ) )
				while( $row = $sql->fetch( \PDO::FETCH_ASSOC ) )
					$this->cache[$row[\io\creat\chassis\tags\instance::FN_ID]] = new \_ctx (	$row[\io\creat\chassis\tags\instance::FN_ID],
																								$row[\io\creat\chassis\tags\instance::FN_SCHEME],
																								$row[\io\creat\chassis\tags\instance::FN_NAME],
																								$row[\io\creat\chassis\tags\instance::FN_DESC] );
		}
		
		return $this->cache;
	}
}
	
?>