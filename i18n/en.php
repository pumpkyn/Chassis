<?php

/**
 * @file en.php
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * English language messages for Chassis framework resources.
 */

/**
 * Search solution.
 */
$__chassis_msg['srchBtSearch']		= 'Search';
$__chassis_msg['srchBtShowAll']		= 'Show all';

/**
 * List subpackage.
 */
$__chassis_msg['listOf']				= 'of';
$__chassis_msg['listTo']				= '-';
$__chassis_msg['listItems']			= 'Records';
$__chassis_msg['listPage']			= 'Page';
$__chassis_msg['listEmptyOptions']	= 'You may want to';
$__chassis_msg['listEmptyLastOpt']	= 'or';

/**
 * Lists Batch Processing (BP) form.
 */
$__chassis_msg['bpAll']				= 'Select all';
$__chassis_msg['bpNone']				= 'Deselect all';
$__chassis_msg['bpYes']				= 'Yes';
$__chassis_msg['bpNo']				= 'No';
$__chassis_msg['bpWarning']			= 'Warning';

$__chassis_msg['formBtBack']			= 'Back';
$__chassis_msg['formBtSave']			= 'Save';

/**
 * CDES.
 */
$__chassis_msg['cdesCreateContext']	= 'Create label';
$__chassis_msg['cdesEditContext']		= 'Edit label';
$__chassis_msg['cdesCaption']			= $__chassis_msg['cdesCreateContext'];
$__chassis_msg['cdesPreview']			= 'Preview';
$__chassis_msg['cdesScheme']			= 'Color scheme';
$__chassis_msg['cdesDisplay']			= 'Text to display';
$__chassis_msg['cdesDesc']			= 'Description';
$__chassis_msg['cdesContext']			= 'Label';
$__chassis_msg['cdesRemove']			= 'Remove';
$__chassis_msg['cdesQuestion']		= 'Do you really want to remove context <b>%s</b>? This operation cannot be undone.';
$__chassis_msg['cdesEmpty']			= 'You do not have any labels created.';
$__chassis_msg['cdesNoMatch']			= 'Not match found for search request.';
$__chassis_msg['cdesOCreate']			= 'Create new label';
$__chassis_msg['cdesOShowAll']		= 'Show all labels';
$__chassis_msg['cdesOSearch']			= 'Change keywords and search again';
$__chassis_msg['cdesNoCtxs']			= 'No contexts available';

/**
 * Contexts + CDES.
 */
$__chassis_msg['ctx']['dar']			= 'Darth Vader';		// black scheme
$__chassis_msg['ctx']['des']			= 'Desire';			// red scheme
$__chassis_msg['ctx']['blu']			= 'Blue Wave';		// blue scheme
$__chassis_msg['ctx']['wee']			= 'Surak';			// green scheme
$__chassis_msg['ctx']['roq']			= 'Roquefort';		// magenta scheme
$__chassis_msg['ctx']['flw']			= 'Sunflower';		// orange scheme
$__chassis_msg['ctx']['sky']			= 'Skynet';			// blue scheme
$__chassis_msg['ctx']['cle']			= 'Vogon Poetry';		// darker gray scheme
$__chassis_msg['ctx']['sun']			= 'Sunshine';			// yellow scheme
$__chassis_msg['ctx']['sil']			= 'Argentina';		// gray scheme
$__chassis_msg['ctx']['sio']			= 'Zion Gates';		// white scheme with edges

/**
 * CDES status messages.
 */
$__chassis_msg['cdes']['preparing']	= 'Preparing form...';
$__chassis_msg['cdes']['prepared']	= 'Prepared';
$__chassis_msg['cdes']['saving']		= 'Saving context...';
$__chassis_msg['cdes']['saved']		= 'Saved';
$__chassis_msg['cdes']['executing']	= 'Executing...';
$__chassis_msg['cdes']['executed']	= 'Executed';
$__chassis_msg['cdes']['e_format']	= 'Error: incorrect format of ' . $__chassis_msg['cdesDisplay'] . '!';
$__chassis_msg['cdes']['e_exists']	= 'Error: context already exists!';
$__chassis_msg['cdes']['e_unknown']	= 'Error: unknown error! Contact administrators.';

/**
 * Search solution status messages.
 *
 * @todo rethink about more messages to indicate error conditions (see CDES part)
 */
$__chassis_msg['srch']['loading']		= 'Loading...';
$__chassis_msg['srch']['loaded']		= 'Loaded';
$__chassis_msg['srch']['resizing']	= 'Resizing...';
$__chassis_msg['srch']['resized']		= 'Resized';
$__chassis_msg['srch']['executing']	= $__chassis_msg['cdes']['executing'];
$__chassis_msg['srch']['executed']	= $__chassis_msg['cdes']['executed'];
$__chassis_msg['srch']['e_unknown']	= $__chassis_msg['cdes']['e_unknown'];

/**
 * SkyDome messages.
 */
$__chassis_msg['sd']['close']			= 'Close';

?>