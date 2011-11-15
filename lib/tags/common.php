<?php

/**
 * @file common.php
 * @author giorno
 * @package Chassis
 * @subpackage Tags
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Definition of constants used in Tags Persistence classes. This is to make
 * constants available in extensions not comptible with PHP namespaces (e.g.
 * Smarty Template Engine).
 */
interface tags
{
	/*
	 * Database table fields names.
	 */
	const FN_UID	= 'UID';
	const FN_ID		= 'CID';
	const FN_SCHEME	= 'scheme';
	const FN_NAME	= 'name';
	const FN_DESC	= 'desc';

}

?>