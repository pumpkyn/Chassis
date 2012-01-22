<?php

/**
 * @file _list_builder.php
 * @author giorno
 * @package Chassis
 * @subpackage List
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_LIB . "list/_list_cell.php";
require_once CHASSIS_LIB . "list/_list_bp.php";

/**
 * [i18n]
 * Class to encapsulate methods for manipulating list. It means composing
 * all data and their output to be at disposal for higher level (UI frontend).
 * This encapsulates _list_descriptor class to provide complete list with both,
 * header and data.
 */
class _list_builder
{
	/**
	 * Internal representation of fields (in header).
	 * @var array
	 */
	protected $fields = NULL;
	
	/**
	 * Internal representation of data rows.
	 * @var array
	 */
	private $rows = NULL;

	/**
	 * BP form instance for this list.
	 * @var _list_bp
	 */
	private $bp = NULL;

	/**
	 * Paging widget data.
	 * @var array
	 */
	private $pager = NULL;

	/**
	 * Name of Javascript variable holding client side logic instance.
	 * @var string
	 */
	private $client_var = '';

	/*
	 * Array with reference to custom managers. Manager Id is key and jumpover
	 * number is value. This is used in AddRow() method to pass cycle at certain
	 * point.
	 * @var array
	 */
	private $jumpers = NULL;
	
	/**
	 * Constructor.
	 * @param string $client_id client search instance name
	 */
	public function __construct ( $client_id )
	{
		$this->rows = null;
		$this->client_var = $client_id;
		$this->registerJumper( _list_cell::MAN_DATETIME );
	}
	
	/**
	 * Add field to the header.
	 * @param string $id identifier/name of field (used for ordering etc.)
	 * @param string $caption string to represent the field
	 * @param string $action what should happen on click, default empty string
	 * @param string $align alignment of column items ('left','right','center')
	 * @param bool $order defines whether or not field should be orderable
	 * @param bool $ordered defines whether or not list is actually being ordered by this field
	 * @param string $direction defines direction of ordering ('ASC', 'DESC')
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
	 * Register new manager into jumpers. See AddRow() for more.
	 * @param string $manager identifier of item's rendering manager
	 * @param int $jump number of cells occupied by items using this manager
	 */
	public function registerJumper ( $manager, $jump = 1 ) { $this->jumpers[$manager] = $jump; }

	/**
	 * Creates instance of _list_bp form for batch processing.
	 * @return _list_bp
	 */
	public function getBp ( )
	{
		if ( is_null( $this->bp ) )
			$this->bp = new _list_bp( $this->client_var );
		
		return $this->bp;
	}

	/**
	 * Add row cells to the list. Function accepts variable length
	 * of parameters, missing ones are replaced with null objects.
	 * Cells must be instances of ListCell class or its childs.
	 * E.g.: ListBuilder->AddRow( new ListCell( ListCell::DateTime( 'Aug 2 2008', '12:44'), MAN_DATETIME ), ... );
	 *
	 * @author giorno
	 */
	public function addRow ( )
	{
		$pos = $i = 0;
		$count = func_num_args( );
		$row = null;

		if ( is_array( $this->fields ) && count( $this->fields ) > 0 )
			foreach ( $this->fields as $key=>$field )
			{
				if ( $i < $count )
				{
					for ( $j = 0; $j < $field['colspan']; $j++ )
					{
						if ( $pos < $count )
						{
							$cell = func_get_arg( $pos );

							if ( is_object( $cell ) )
							{
								$row[] = $cell;

								/**
								 * Special cases.
								 */
								switch ( $cell->manager )
								{
									/**
									 * Autotmatically register checkbox into
									 * BP form.
									 */
									case _list_cell::MAN_CHECKBOX:
										$this->getBp( );
										$this->bp->addChkbox( $cell->data['html_id'], $cell->data['id'] );
									break;
								}

								/*
								 * For particular managers there is need to jump
								 * over some cycles here. Couter $pos has to be
								 * increased by (value_of_real_field_colspan - 1),
								 * e.g. for MAN_DATETIME colspan is 2.
								 *
								 * If this is not properly solved for custom
								 * managers, layout might be broken due to added
								 * null values.
								 */
								if ( $cell->manager == _list_cell::MAN_DATETIME )
								{
									$pos++;
									break;
								}
								elseif( is_array( $this->jumpers ) && array_key_exists( $cell->manager, $this->jumpers ) )
								{
									$pos += $this->jumpers[$cell->manager];
									break;
								}
							}
							else
								$row[] = null;
						}
						else
							$row[] = null;

						$pos++;
					}
				}
				else
				{
					$row[] = null;
					$pos++;
				}

				$i++;
			}
		$this->rows[] = $row;
	}

	/**
	 * Export complete list (descriptor+data+localization) as an array.
	 *
	 * @return array
	 * @author giorno
	 */
	public function export ( )
	{
		return Array(	'instance' => $this->client_var,
						'header' => $this->fields,
						'rows' => $this->rows,
						'pager' => $this->pager,
						'bp' => $this->bp );
	}

	/**
	 * Provides array of values for paging widget.
	 *
	 * @param pageSize length of page
	 * @param itemCount length of the list
	 * @param page actual page number
	 * @param pageCount number of pages
	 * @param width number of shown page numbers on sides of current page, e.g. 2 may mean [ 4, 5, 6, 7, 8 ] for actual page 6
	 * @param float $elapsed time it took extracting data (in seconds), will not be displayed if NULL
	 */
	public function computePaging ( $pageSize, $itemCount, $page, $pageCount, $width = 2, $elapsed = NULL )
	{
		$this->pager['elapsed'] = $elapsed;
		
		// No records.
		if ( $itemCount == 0 )
		{
			$firstItem = 0;
			$lastItem = 0;
		}
		else
		{
			$firstItem = ( ( $page - 1 ) * $pageSize) + 1;
			$lastItem = $firstItem + $pageSize - 1;

			if ( $lastItem > $itemCount )
				$lastItem = $itemCount;
		}
		
		$this->pager['firstItem'] = $firstItem;
		$this->pager['lastItem'] = $lastItem;
		$this->pager['itemCount'] = $itemCount;

		$this->pager['page'] = $page;
		$this->pager['pageCount'] = $pageCount;

		$firstPage = $page - $width;
		$lastPage = $page + $width;

		if ( $firstPage < 1 )
		{
			$lastPage += abs( $firstPage ) + 1;
			$firstPage = 1;
		}

		if ( $lastPage > $pageCount )
		{
			if ( $firstPage > 1 )
				$firstPage -= ( $lastPage - $pageCount );

			$lastPage = $pageCount;
		}

		if ( $firstPage < 1 ) $firstPage = 1;
		
		$this->pager['firstPage'] = $firstPage;
		$this->pager['lastPage'] = $lastPage;

		for ( $i = $firstPage; $i <= $lastPage; $i++ )
		{
			$this->pager['numbers'][] = $i;
		}
	}
}

?>