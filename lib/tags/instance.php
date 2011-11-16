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
	 * Lozalized headline text for TUI. This is the only userspace localization
	 * passed to the instance.
	 * @var string
	 */
	//protected $headline = 0;
	
	public function __construct ( $uid, $table, $headline, &$layout, $url, $params, &$proxy )
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
								$proxy );
	}
	
	public function client ( )
	{
		$requirer = $this->layout->getRequirer( );
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative() . 'js/_pers.js', __CLASS__ ) );	// a requirement
		$requirer->call( \io\creat\chassis\uicmp\vlayout::RES_JS, array( $requirer->getRelative() . 'js/_tags.js', __CLASS__ ) );
		parent::client( );
	}
	
	/**
	 * Explicitly changing order and parameters of table fields.
	 */
	protected function explicit ( )
	{
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
		$this->index[] = self::FN_UID;
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
			if ( ( $field->name == self::FN_UID ) && ( $this->uid < 0 ) )	// bypassing security
				return;

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
			return "ORDER BY `" . _db_escape( self::FN_NAME ) . "` " . _db_escape( $search['d'] );
		else
			return parent::orderq( $search );
	}
}

?>