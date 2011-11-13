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
	public function __construct( $flags ) { $this->flags = $flags; }
	
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
	 * Simple constructor taking instance of field as a prototype and metadata
	 * to produce new instance to replace original instance of field.
	 * @param \io\creat\chassis\pers\field $field reference to original instance
	 * @param string $table tag table name
	 * @param int $uid user ID, pass negative value to turn off per-user security restriction
	 */
	public function __construct ( &$field, $table, $uid )
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
				$res = _db_query( "SELECT * FROM `" . _db_escape( $this->table ) . "`
									ORDER BY `" . \_cdes::F_CTXNAME . "`" );
			else
				$res = _db_query( "SELECT * FROM `" . _db_escape( $this->table ) . "`
									WHERE `" . \_cdes::F_CTXUID . "` = \"" . _db_escape( $this->uid ) . "`
									ORDER BY `" . \_cdes::F_CTXNAME . "`" );
			if ( $res && _db_rowcount( $res ) )
				while( $row = _db_fetchrow( $res ) )
					$this->cache[$row[\_cdes::F_CTXID]] = new \_ctx (	$row[\_cdes::F_CTXID],
																		$row[\_cdes::F_CTXSCHEME],
																		$row[\_cdes::F_CTXNAME],
																		$row[\_cdes::F_CTXDESC] );
		}
		
		return $this->cache;
	}
}
	
?>