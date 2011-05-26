<?php

/**
 * @file en.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * 
 * @todo as it already contains all framework localization it should be named
 *        so and placed one dir upwards (apply for all languages)
 *
 * English language messages for UICMP framework.
 */

/**
 * Search solution.
 */
$_uicmp_i18n['srchBtSearch']		= 'Search';
$_uicmp_i18n['srchBtShowAll']		= 'Show all';

/**
 * List subpackage.
 */
$_uicmp_i18n['listOf']				= 'of';
$_uicmp_i18n['listTo']				= '-';
$_uicmp_i18n['listItems']			= 'Records';
$_uicmp_i18n['listPage']			= 'Page';
$_uicmp_i18n['listEmptyOptions']	= 'You may want to';
$_uicmp_i18n['listEmptyLastOpt']	= 'or';

/**
 * Lists Batch Processing (BP) form.
 */
$_uicmp_i18n['bpAll']				= 'Select all';
$_uicmp_i18n['bpNone']				= 'Deselect all';
$_uicmp_i18n['bpYes']				= 'Yes';
$_uicmp_i18n['bpNo']				= 'No';
$_uicmp_i18n['bpWarning']			= 'Warning';

$_uicmp_i18n['formBtBack']			= 'Back';
$_uicmp_i18n['formBtSave']			= 'Save';

/**
 * CDES.
 */
$_uicmp_i18n['cdesCreateContext']	= 'Create label';
$_uicmp_i18n['cdesEditContext']		= 'Edit label';
$_uicmp_i18n['cdesCaption']			= $_uicmp_i18n['cdesCreateContext'];
$_uicmp_i18n['cdesPreview']			= 'Preview';
$_uicmp_i18n['cdesScheme']			= 'Color scheme';
$_uicmp_i18n['cdesDisplay']			= 'Text to display';
$_uicmp_i18n['cdesDesc']			= 'Description';
$_uicmp_i18n['cdesContext']			= 'Label';
$_uicmp_i18n['cdesRemove']			= 'Remove';
$_uicmp_i18n['cdesQuestion']		= 'Do you really want to remove context <b>%s</b>? This operation cannot be undone.';
$_uicmp_i18n['cdesEmpty']			= 'You do not have any labels created.';
$_uicmp_i18n['cdesNoMatch']			= 'Not match found for search request.';
$_uicmp_i18n['cdesOCreate']			= 'Create new label';
$_uicmp_i18n['cdesOShowAll']		= 'Show all labels';
$_uicmp_i18n['cdesOSearch']			= 'Change keywords and search again';

/**
 * Contexts + CDES.
 */
$_uicmp_i18n['ctx']['dar']			= 'Darth Vader';		// black scheme
$_uicmp_i18n['ctx']['des']			= 'Desire';			// red scheme
$_uicmp_i18n['ctx']['blu']			= 'Blue Wave';		// blue scheme
$_uicmp_i18n['ctx']['wee']			= 'Surak';			// green scheme
$_uicmp_i18n['ctx']['roq']			= 'Roquefort';		// magenta scheme
$_uicmp_i18n['ctx']['flw']			= 'Sunflower';		// orange scheme
$_uicmp_i18n['ctx']['sky']			= 'Skynet';			// blue scheme
$_uicmp_i18n['ctx']['cle']			= 'Vogon Poetry';		// darker gray scheme
$_uicmp_i18n['ctx']['sun']			= 'Sunshine';			// yellow scheme
$_uicmp_i18n['ctx']['sil']			= 'Argentina';		// gray scheme
$_uicmp_i18n['ctx']['sio']			= 'Zion Gates';		// white scheme with edges

/**
 * CDES status messages.
 */
$_uicmp_i18n['cdes']['preparing']	= 'Preparing form...';
$_uicmp_i18n['cdes']['prepared']	= 'Prepared';
$_uicmp_i18n['cdes']['saving']		= 'Saving context...';
$_uicmp_i18n['cdes']['saved']		= 'Saved';
$_uicmp_i18n['cdes']['executing']	= 'Executing...';
$_uicmp_i18n['cdes']['executed']	= 'Executed';
$_uicmp_i18n['cdes']['e_format']	= 'Error: incorrect format of ' . $_uicmp_i18n['cdesDisplay'] . '!';
$_uicmp_i18n['cdes']['e_exists']	= 'Error: context already exists!';
$_uicmp_i18n['cdes']['e_unknown']	= 'Error: unknown error! Contact administrators.';

/**
 * Search solution status messages.
 *
 * @todo rethink about more messages to indicate error conditions (see CDES part)
 */
$_uicmp_i18n['srch']['loading']		= 'Loading...';
$_uicmp_i18n['srch']['loaded']		= 'Loaded';
$_uicmp_i18n['srch']['resizing']	= 'Resizing...';
$_uicmp_i18n['srch']['resized']		= 'Resized';
$_uicmp_i18n['srch']['executing']	= $_uicmp_i18n['cdes']['executing'];
$_uicmp_i18n['srch']['executed']	= $_uicmp_i18n['cdes']['executed'];
$_uicmp_i18n['srch']['e_unknown']	= $_uicmp_i18n['cdes']['e_unknown'];

?>