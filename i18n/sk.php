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
$__chassis_msg['srchBtSearch']		= 'Hľadaj';
$__chassis_msg['srchBtShowAll']		= 'Zobraz všetko';

/**
 * List subpackage.
 */
$__chassis_msg['listOf']				= 'z';
$__chassis_msg['listTo']				= '-';
$__chassis_msg['listItems']			= 'Záznamy';
$__chassis_msg['listPage']			= 'Strana';
$__chassis_msg['listEmptyOptions']	= 'Môžete';
$__chassis_msg['listEmptyLastOpt']	= 'alebo';

/**
 * Lists Batch Processing (BP) form.
 */
$__chassis_msg['bpAll']				= 'Označ všetko';
$__chassis_msg['bpNone']				= 'Odznač všetko';
$__chassis_msg['bpYes']				= 'Áno';
$__chassis_msg['bpNo']				= 'Nie';
$__chassis_msg['bpWarning']			= 'Varovanie';

$__chassis_msg['formBtBack']			= 'Späť';
$__chassis_msg['formBtSave']			= 'Uložiť';

/**
 * CDES.
 */
$__chassis_msg['cdesCreateContext']	= 'Vytvoriť nálepku';
$__chassis_msg['cdesEditContext']		= 'Upraviť nálepku';
$__chassis_msg['cdesCaption']			= $__chassis_msg['cdesCreateContext'];
$__chassis_msg['cdesPreview']			= 'Náhľad';
$__chassis_msg['cdesScheme']			= 'Farebná schéma';
$__chassis_msg['cdesDisplay']			= 'Zobraziť text';
$__chassis_msg['cdesDesc']			= 'Popis';
$__chassis_msg['cdesContext']			= 'Nálepka';
$__chassis_msg['cdesRemove']			= 'Odstrániť';
$__chassis_msg['cdesQuestion']		= 'Skutočne chcete zmazať obsah <b>%s</b>? This operation cannot be undone.';
$__chassis_msg['cdesEmpty']			= 'Nie sú vytvorené žiadne nálepky.';
$__chassis_msg['cdesNoMatch']			= 'Vyhľadávanie bolo neúspešné.';
$__chassis_msg['cdesOCreate']			= 'Vytvoriť nálepku';
$__chassis_msg['cdesOShowAll']		= 'Zobraziť všetky nálepky';
$__chassis_msg['cdesOSearch']			= 'Zmeniť výraz a hľadať znovu';
$__chassis_msg['cdesNoCtxs']			= 'Žiadny obsah nie je dostupný';

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
$__chassis_msg['cdes']['preparing']	= 'Pripravujem formulár...';
$__chassis_msg['cdes']['prepared']	= 'Pripravený';
$__chassis_msg['cdes']['saving']		= 'Ukladám nálepku...';
$__chassis_msg['cdes']['saved']		= 'Uložené';
$__chassis_msg['cdes']['executing']	= 'Vykonávam...';
$__chassis_msg['cdes']['executed']	= 'Vykonané';
$__chassis_msg['cdes']['e_format']	= 'Chyba: nesprávny obsah ' . $__chassis_msg['cdesDisplay'] . '!';
$__chassis_msg['cdes']['e_exists']	= 'Chyba: obsah už existuje!';
$__chassis_msg['cdes']['e_unknown']	= 'Chyba: chyba neznáma! Kontaktujte administrátorov.';

/**
 * Search solution status messages.
 *
 * @todo rethink about more messages to indicate error conditions (see CDES part)
 */
$__chassis_msg['srch']['loading']		= 'Načítavam...';
$__chassis_msg['srch']['loaded']		= 'Načítané';
$__chassis_msg['srch']['resizing']	= 'Mením veľkosť...';
$__chassis_msg['srch']['resized']		= 'Veľkosť zmenená';
$__chassis_msg['srch']['executing']	= $__chassis_msg['cdes']['executing'];
$__chassis_msg['srch']['executed']	= $__chassis_msg['cdes']['executed'];
$__chassis_msg['srch']['e_unknown']	= $__chassis_msg['cdes']['e_unknown'];

/**
 * SkyDome messages.
 */
$__chassis_msg['sd']['close']			= 'Zavrieť';

?>