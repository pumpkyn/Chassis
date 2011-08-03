<?php 

/**
 * @file _list_descriptor.php
 * @author giorno
 * @package Chassis
 * @subpackage List
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . 'list/_list_i18n.php';

/**
 * Class to encapsulate methods for manipulating list descriptors. Descriptor
 * is an array describing structure of list header. Descriptor and header are
 * interexchangeable terms. One item of header is called field.
 *
 * Constant values may be used in frontend template hence need to be synced.
 */
class _list_descriptor extends _list_i18n
{
	/**
	 * Internal representation of fields (in header).
	 */
	protected $fields = null;

	/**
	 * Constructor.
	 * 
	 * @param _i18n_loader $i18n_loader instance of localization provider
	 */
	public function __construct ( $i18n_loader )
	{
		parent::__construct( $i18n_loader );
		$this->fields = null;
	}

	/**
	 * Add field to the header.
	 *
	 * @param id identifier/name of field (used for ordering etc.)
	 * @param caption string to represent the field
	 * @param action what should happen on click, default empty string
	 * @param align alignment of column items
	 * @param order defines whether or not field should be orderable
	 * @param order defines whether or not list is ordered by this field
	 * @param direction defines direction of ordering (ASC or DESC)
	 *
	 * @author giorno
	 */
	public function addField ( $id, $caption, $width, $colspan, $align = 'left', $order = false, $ordered = false, $direction = 'ASC' )
	{
		$this->fields[$id] = Array( 'id' => $id,
									'caption' => $caption,
									'width' => $width,
									'colspan' => $colspan,
									'align' => $align,
									'order' => $order,
									'ordered' => $ordered,
									'direction' => $direction );
	}

	/**
	 * Export header as an array.
	 *
	 * @return array
	 * @author giorno
	 */
	public function export ( )
	{
		return $this->fields;
	}
}

?>