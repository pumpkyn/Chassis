<?PHP

/**
 * @file _list_descriptor.php
 * @author giorno
 * @package Chassis
 * @subpackage List
 *
 * Class to encapsulate methods for manipulating list descriptors. Descriptor
 * is an array describing structure of list header. Descriptor and header are
 * interexchangeable terms. One item of header is called field.
 *
 * Constant values may be used in frontend template hence need to be synced.
 */
class _list_descriptor
{
	/**
	 * Internal representation of fields (in header).
	 */
	protected $fields = null;

	/**
	 * Constructor.
	 */
	public function __construct ( )
	{
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