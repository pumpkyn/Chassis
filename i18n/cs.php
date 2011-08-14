<?php

/**
 * @file cs.php
 * @author pumpkyn
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * Czech language messages for Chassis framework resources.
 */

/**
 * Search solution.
 */
$__chassis_msg['srchBtSearch']		= 'Hledej';
$__chassis_msg['srchBtShowAll']		= 'Zobraz vše';

/**
 * List subpackage.
 */
$__chassis_msg['listOf']				= 'z';
$__chassis_msg['listTo']				= '-';
$__chassis_msg['listItems']			= 'Záznamy';
$__chassis_msg['listPage']			= 'Strana';
$__chassis_msg['listEmptyOptions']	= 'Mužete';
$__chassis_msg['listEmptyLastOpt']	= 'nebo';

/**
 * Lists Batch Processing (BP) form.
 */
$__chassis_msg['bpAll']				= 'Označ vše';
$__chassis_msg['bpNone']				= 'Odznač vše';
$__chassis_msg['bpYes']				= 'Ano';
$__chassis_msg['bpNo']				= 'Ne';
$__chassis_msg['bpWarning']			= 'Varování';

$__chassis_msg['formBtBack']			= 'Zpět';
$__chassis_msg['formBtSave']			= 'Uložit';

/**
 * CDES.
 */
$__chassis_msg['cdesCreateContext']	= 'Vytvořit nálepku';
$__chassis_msg['cdesEditContext']		= 'Upravit nálepku';
$__chassis_msg['cdesCaption']			= $__chassis_msg['cdesCreateContext'];
$__chassis_msg['cdesPreview']			= 'Náhled';
$__chassis_msg['cdesScheme']			= 'Farebná schéma';
$__chassis_msg['cdesDisplay']			= 'Zobrazit text';
$__chassis_msg['cdesDesc']			= 'Popis';
$__chassis_msg['cdesContext']			= 'Nálepka';
$__chassis_msg['cdesRemove']			= 'Odstranit';
$__chassis_msg['cdesQuestion']		= 'Skutočne chcete zmazat obsah? <b>%s</b>? Tato operace je nevratná.';
$__chassis_msg['cdesEmpty']			= 'Nejsou vytvořené žádne nálepky.';
$__chassis_msg['cdesNoMatch']			= 'Vyhledávání bylo neúspěšné.';
$__chassis_msg['cdesOCreate']			= 'Vytvořit nálepku';
$__chassis_msg['cdesOShowAll']		= 'Zobrazit všechny nálepky';
$__chassis_msg['cdesOSearch']			= 'Změnit výraz a hledat znovu';
$__chassis_msg['cdesNoCtxs']			= 'Žádny obsah není dostupný';

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
$__chassis_msg['cdes']['preparing']	= 'Připravujem formulář...';
$__chassis_msg['cdes']['prepared']	= 'Připravený';
$__chassis_msg['cdes']['saving']		= 'Ukládám nálepku...';
$__chassis_msg['cdes']['saved']		= 'Uložené';
$__chassis_msg['cdes']['executing']	= 'Vykonávám...';
$__chassis_msg['cdes']['executed']	= 'Vykonané';
$__chassis_msg['cdes']['e_format']	= 'Chyba: nesprávný obsah ' . $__chassis_msg['cdesDisplay'] . '!';
$__chassis_msg['cdes']['e_exists']	= 'Chyba: obsah už existuje!';
$__chassis_msg['cdes']['e_unknown']	= 'Chyba: chyba neznámá! Kontaktujte správcov.';

/**
 * Search solution status messages.
 *
 * @todo rethink about more messages to indicate error conditions (see CDES part)
 */
$__chassis_msg['srch']['loading']		= 'Načítávám...';
$__chassis_msg['srch']['loaded']		= 'Načítané';
$__chassis_msg['srch']['resizing']	= 'Měním velikost...';
$__chassis_msg['srch']['resized']		= 'Velikost změnená';
$__chassis_msg['srch']['executing']	= $__chassis_msg['cdes']['executing'];
$__chassis_msg['srch']['executed']	= $__chassis_msg['cdes']['executed'];
$__chassis_msg['srch']['e_unknown']	= $__chassis_msg['cdes']['e_unknown'];

/**
 * SkyDome messages.
 */
$__chassis_msg['sd']['close']			= 'Zavřít';

?>