<?php

/**
 * @file common.php
 * @author giorno
 * @package Chassis
 * @subpackage Persistence
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Definition of constants used in Persistence classes. This is to make
 * constants available in extensions not comptible with PHP namespaces (e.g.
 * Smarty Template Engine).
 */
class pers
{
	/**
	 * Flags for table (Persistence) instance.
	 */
	const FL_PI_TUI			= 1;	// turns on rendering of table UI
	const FL_PI_RUI			= 2;	// turns on rendering of record UI
	const FL_PI_CREATE		= 4;	// renders (in resizer area) anchor to create new record and allows creation mode in the editor (RUI)
	const FL_PI_RESIZE		= 8;	// allows resizer and resizing of search results page
	const FL_PI_ANCHORS		= 16;	// defines ability of TUI to show anchors (if FL_RESIZE is not in place, it will create dummy resizer instance, otherwise uses existing resizer)
	const FL_PI_AS			= 32;	// TUI uses Advanced Search layout

	/**
	 * Field options flags.
	 */
	const FL_FO_DYNAMIC		= 1;	// options are subject to change (Ajax refresh must be implemented in client side logic)
	const FL_FO_BINARY		= 2;	// restrictor has only 2 values (it implies checkbox to be used)
	const FL_FO_MULTIVAL	= 4;	// restrictor has more than 2 values (it implies select box to be used)
	const FL_FO_NE			= 8;	// field must have value (for string it means non-empty string)
	
	/**
	 * Field definition flags.
	 */
	const FL_FD_MODIFY		= 1;	// field is modifiable (in the form)
	const FL_FD_VIEW		= 2;	// field is displayed in search result
	const FL_FD_ORDER		= 4;	// field is used for sorting
	const FL_FD_SEARCH		= 8;	// field is used for searching
	const FL_FD_RESTRICT	= 16;	// field can restrict searching (mutual exclusion with FL_SEARCH)
	const FL_FD_PK			= 32;	// field is a primary key
	const FL_FD_AUTO		= 64;	// field value is automatically generated
	const FL_FD_FK			= 128;	// field is a foreign key
	const FL_FD_CONST		= 256;	// field is constant (i.e. user ID), this renders field permanently unmodifiable
	const FL_FD_ANCHOR		= 512;	// field will be used as anchor for editing the record
	const FL_FD_HIDDEN		= 1024;	// field is not displayed in the RUI
	const FL_FD_PREVIEW		= 2048;	// field value change enforces preview update (in RUI)
	
	/**
	 * Constants for description of the field type.
	 */
	const FT_UNKNOWN		= 0;	// field type is unknown, this renders field not being used
	const FT_ICON			= 1;	// type of pseudofields reserved for list icons (for actions)
	const FT_INT			= 32;	// field is an integer number
	//const FT_REAL			= 64;	// field is a real number
	const FT_STRING			= 96;	// field is a string
	const FT_PASSWORD		= 97;	// field is a string containing password (specially treated)
	const FT_ENUM			= 128;	// field is a single value from list of options, can be an FK
	const FT_TAG			= 160;	// field is a reference to single tag
	const FT_TAGS			= 192;	// field is a serialized list of tags
	const FT_TIMESTAMP		= 224;	// 
	const FT_DATE			= 256;	// 
	const FT_TIME			= 288;	// 
	const FT_DATETIME		= 320;	//
	const FT_BOOL			= 353;	// Boolean type, represented in database as 0 or 1 integer
}

?>