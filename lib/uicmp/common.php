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
	/**
	 * Form item types.
	 */
	const FIT_TEXT		= 0;	// Simple text input.
	const FIT_PASSWORD	= 1;	// Password field. Value for it is ignored.
	const FIT_CHECKBOX	= 2;	// Checkbox field. Value is boolean. Description for it is ignored.
	const FIT_SELECT	= 3;	// Multioption chooser. 
	const FIT_TEXTAREA	= 4;	// Textarea.
	const FIT_ROTEXT	= 5;	// Read-only text input.
}

?>