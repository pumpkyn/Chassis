<?php

/**
 * @file common.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Collection of constants used by UICMP components. Separated here due to
 * Smarty template engine lack of support for PHP namespaces.
 */
interface _uicmp
{
	/** Simple form item. Simple text input. */
	const FIT_TEXT		= 0;
	/** Simple form item. Password field. Value for it is ignored. */
	const FIT_PASSWORD	= 1;
	/** Simple form item. Checkbox field. Value is boolean. Description for it is ignored. */
	const FIT_CHECKBOX	= 2;
	/** Simple form item. Multioption chooser. */
	const FIT_SELECT	= 3;
	/** Simple form item. Textarea. */
	const FIT_TEXTAREA	= 4;
	/** Simple form item. Read-only text input. */
	const FIT_ROTEXT	= 5;
	/** Date picker (SELECT box style). */
	const FIT_DATE		= 6;
	
	/** Group item. Plain text, e.g. pipe character for separator. */
	const GIT_TEXT		= 32;
	/** Group item. Indicator widget. */
	const GIT_INDICATOR	= 33;
	/** Group item. Button input. */
	const GIT_BUTTON	= 34;
	/** Group item. Checkbox input. */
	const GIT_CHECKBOX	= 35;
	/** Group item. Anchor-like control. Uses event callbacks to execute Javascript handler. */
	const GIT_ANCHOR	= 36;
	/** Group item. Enumeration, multioption chooser. */
	const GIT_SELECT	= 37;
}

?>