<?PHP

/**
 * @file _list_cell.php
 * @author giorno
 * @package Chassis
 * @subpackage List
 * 
 * Class to provide instance of cell in tables (lists). For special applications
 * child class is recommended to be created.
 */

require_once CHASSIS_LIB . 'ui/_ctx.php';

class _list_cell
{
	/**
	 * Managers of the cell. Default manager is usposed to be common handler, but
	 * for special cell types special manager (outside of framework) has to be used.
	 * These specials have to be used with their unique name due their identification is
	 * not known to the class. In simplest way manager is template of template engine
	 * (e.g. Smarty) for handling cell data (displaying, icons, Javascript action, etc.).
	 *
	 */
	const MAN_DEFAULT		= 'manDefault';			// default (plain text)
	const MAN_DATETIME		= 'manDateTime';		// date and time values
	const MAN_DATEORTIME	= 'manDateOrTime';		// date or time value (fuzzy - either time for today or date for older)
	const MAN_JAVASCRIPT	= 'manJavascript';		// text with Javascript code to execute onClick()
	const MAN_EMAIL			= 'manEmail';			// email address (in Ui it has to be converted into anchor with 'mailto:' HREF attr)
	const MAN_ICONREMOVE	= 'manIconRemove';		// icon for remove (X) with Javascript code
	const MAN_ICONEDIT		= 'manIconEdit';		// icon for edit (pencil?) with Javascript code
	const MAN_BADGE			= 'manBadge';			// badge of context (category, ... )
	const MAN_CHECKBOX		= 'manCheckbox';		// checkbox for batch processing
	const MAN_DECO			= 'manDeco';			// decorated item (contexts, title, text)

	/**
	 * (array) Data container, indexes and structure have to respect cell manager type.
	 */
	public $data = null;

	/**
	 * Identifier of manager.
	 */
	public $manager = null;

	/**
	 * Constructor. Creates cell itself
	 *
	 * @param data array of data
	 * @param manager manager of the cell
	 */
	public function __construct ( $data, $manager = self::MAN_DEFAULT )
	{
		if ( isset( $data ) && is_array( $data ) )
			$this->data = $data;

		$this->manager = $manager;
	}

	/**
	 * Method to create data array for MAN_DATETIME manager.
	 * E.g.: $cell = new ListCell( ListCell::DateTime( 'Aug 2 2008', '12:44'), MAN_DATETIME );
	 *
	 * @param date (string) date field
	 * @param time (string) time field
	 * @param class custom CSS class
	 * @return array
	 */
	public static function DateTime ( $date, $time, $class = '' )
	{
		return Array( 'date' => $date, 'time' => $time, 'class' => $class );
	}

	/**
	 * Static method to create data array for simple text (MAN_DEFAULT).
	 *
	 * @param text simple text
	 * @param class custom CSS class
	 * @return array
	 */
	public static function Text ( $text, $class = '' )
	{
		return Array( 'text' => $text, 'class' => $class );
	}

	/**
	 * Method to create data array for MAN_JAVASCRIPT manager.
	 *
	 * @param text (string) title field
	 * @param code (string) script field
	 * @param class custom CSS class
	 * @return array
	 */
	public static function Javascript ( $text, $code, $class = '' )
	{
		return Array( 'text' => $text, 'code' => $code, 'class' => $class );
	}

	/**
	 * Prepare data for Javascript, e.g. icons managers.
	 *
	 * @param code (string) script field
	 * @param class custom CSS class
	 * @return array
	 */
	public static function Code ( $code, $alt= '', $class = '' )
	{
		return Array( 'code' => $code, 'alt' => $alt, 'class' => $class );
	}

	/**
	 * Prepare data for context badge.
	 *
	 * @param code (string) script field
	 * @param class custom CSS class
	 * @return array
	 */
	public static function Badge ( $id, $scheme, $disp, $desc = '' , $action = '', $class = '' )
	{
		return Array( 'ctx' => new _ctx( $id, $scheme, $disp, $desc, $action ), 'class' => $class );
	}

	/**
	 * Data for checkbox cell.
	 *
	 * @param <string> $html_id HTML ID of checkbox element
	 * @param <string> $id HTML atom ID for Javascript action of BP form
	 */
	public static function chkbox ( $html_id, $id ) { return Array ( 'html_id' => $html_id, 'id' => $id ); }

	/**
	 * Method to create data array for MAN_DECO manager.
	 *
	 * @param <string> $title title/subject part of displayed info
	 * @param <string> $text PLAIN text appended to main title
	 * @param <array> $ctxs instances of _ctx to provide contexts badges data
	 * @param <string> $class_td custom CSS class to decorate item's <TD> container
	 * @param <string> $custom_do custom Javascript to replace frmPrDo() in template code
	 * @param <string> $class_div custom CSS class to decorate item's <DIV> container
	 * @return <array>
	 */
	public static function deco ( $title, $text, $ctxs, $class_td = '', $custom_do = '', $class_div = '' )
	{
		return array( 'title' => $title, 'text' => $text, 'ctxs' => $ctxs, 'class' => $class_td, 'class_div' => $class_div, 'do' => $custom_do );
	}
}

?>