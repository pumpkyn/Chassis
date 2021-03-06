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
$__chassis_msg['srchBtSearch']			= 'Hledej';
$__chassis_msg['srchBtShowAll']			= 'Zobraz vše';

/** List subpackage messages. */
$__chassis_msg['list']['of']		= 'z';
$__chassis_msg['list']['to']		= '-';
$__chassis_msg['list']['items']		= 'Záznamy';
$__chassis_msg['list']['page']		= 'Strana';
$__chassis_msg['list']['opts']		= 'Mužete';
$__chassis_msg['list']['lastopt']	= 'nebo';
$__chassis_msg['list']['sec']		= 'sek.';

/**
 * Lists Batch Processing (BP) form.
 */
$__chassis_msg['bpAll']					= 'Označ vše';
$__chassis_msg['bpNone']				= 'Odznač vše';
$__chassis_msg['bpYes']					= 'Ano';
$__chassis_msg['bpNo']					= 'Ne';
$__chassis_msg['bpWarning']				= 'Varování';

$__chassis_msg['formBtBack']			= 'Zpět';
$__chassis_msg['formBtSave']			= 'Uložit';

/**
 * CDES.
 */
$__chassis_msg['cdesCreateContext']		= 'Vytvořit nálepku';
$__chassis_msg['cdesEditContext']		= 'Upravit nálepku';
$__chassis_msg['cdesCaption']			= $__chassis_msg['cdesCreateContext'];
$__chassis_msg['cdesPreview']			= 'Náhled';
$__chassis_msg['cdesScheme']			= 'Barevná schéma';
$__chassis_msg['cdesDisplay']			= 'Zobrazit text';
$__chassis_msg['cdesDesc']				= 'Popis';
$__chassis_msg['cdesContext']			= 'Nálepka';
$__chassis_msg['cdesRemove']			= 'Odstranit';
$__chassis_msg['cdesQuestion']			= 'Skutečně chcete zmazat nálepku <b>%s</b>? Tato operace je nevratná.';
$__chassis_msg['cdesEmpty']				= 'Nejsou vytvořeny žádné nálepky.';
$__chassis_msg['cdesNoMatch']			= 'Vyhledávání bylo neúspěšné.';
$__chassis_msg['cdesOCreate']			= 'Vytvořit nálepku';
$__chassis_msg['cdesOShowAll']			= 'Zobrazit všechny nálepky';
$__chassis_msg['cdesOSearch']			= 'Změnit výraz a hledat znovu';
$__chassis_msg['cdesNoCtxs']			= 'Žádny obsah není dostupný';

/**
 * Contexts + CDES.
 */
$__chassis_msg['ctx']['dar']			= 'Darth Vader';		// black scheme
$__chassis_msg['ctx']['dfw']			= 'Design Flaw';
$__chassis_msg['ctx']['mpi']			= 'Message Passing';
$__chassis_msg['ctx']['oyl']			= 'Out Of Your League';
$__chassis_msg['ctx']['des']			= 'Desire';			// red scheme
$__chassis_msg['ctx']['anh']			= 'Annihilation';
$__chassis_msg['ctx']['str']			= 'Strain';
$__chassis_msg['ctx']['flw']			= 'Sunflower';		// orange scheme
$__chassis_msg['ctx']['sun']			= 'Sunshine';			// yellow scheme
$__chassis_msg['ctx']['blu']			= 'Blue Wave';		// blue scheme
$__chassis_msg['ctx']['pro']			= 'Propagation';
$__chassis_msg['ctx']['dst']			= 'Depth-First';
$__chassis_msg['ctx']['zon']			= 'Neutral Zone';
$__chassis_msg['ctx']['roq']			= 'Roquefort';		// magenta scheme
$__chassis_msg['ctx']['sky']			= 'Skynet';			// blue scheme
$__chassis_msg['ctx']['wee']			= 'Surak';			// green scheme
$__chassis_msg['ctx']['sya']			= 'Mount Seleya';
$__chassis_msg['ctx']['pgr']			= 'Positive Gradient';
$__chassis_msg['ctx']['cle']			= 'Vogon Poetry';		// darker gray scheme
$__chassis_msg['ctx']['sil']			= 'Argentina';		// gray scheme
$__chassis_msg['ctx']['sio']			= 'Zion Gates';		// white scheme with edges

/**
 * CDES status messages.
 */
$__chassis_msg['cdes']['preparing']		= 'Připravujem formulář...';
$__chassis_msg['cdes']['prepared']		= 'Připravený';
$__chassis_msg['cdes']['saving']		= 'Ukládám nálepku...';
$__chassis_msg['cdes']['saved']			= 'Uložené';
$__chassis_msg['cdes']['executing']		= 'Vykonávám...';
$__chassis_msg['cdes']['executed']		= 'Vykonané';
$__chassis_msg['cdes']['e_format']		= 'Chyba: nesprávný obsah ' . $__chassis_msg['cdesDisplay'] . '!';
$__chassis_msg['cdes']['e_exists']		= 'Chyba: obsah již existuje!';
$__chassis_msg['cdes']['e_unknown']		= 'Chyba: neznámá chyba! Kontaktujte správce.';

/**
 * Search solution status messages.
 *
 * @todo rethink about more messages to indicate error conditions (see CDES part)
 */
$__chassis_msg['srch']['loading']		= 'Načítávám...';
$__chassis_msg['srch']['loaded']		= 'Načteno';
$__chassis_msg['srch']['resizing']		= 'Měním velikost...';
$__chassis_msg['srch']['resized']		= 'Velikost změnena';
$__chassis_msg['srch']['executing']		= $__chassis_msg['cdes']['executing'];
$__chassis_msg['srch']['executed']		= $__chassis_msg['cdes']['executed'];
$__chassis_msg['srch']['e_unknown']		= $__chassis_msg['cdes']['e_unknown'];

/**
 * Persistence.
 */
$__chassis_msg['pers']['tui']['ind']				= $__chassis_msg['srch'];
$__chassis_msg['pers']['tui']['ind']['executing']	= 'Vykonávám...';
$__chassis_msg['pers']['tui']['ind']['done']		= 'Dokončeno';
$__chassis_msg['pers']['as']['on']			= 'rozšířené vyhledávání';
$__chassis_msg['pers']['as']['field']		= 'V poli';
$__chassis_msg['pers']['as']['allfields']	= 'Ve všech polích';
$__chassis_msg['pers']['tui']['ind']				= $__chassis_msg['srch'];
$__chassis_msg['pers']['tui']['as']['on']			= 'rozšířené vyhledávání';
$__chassis_msg['pers']['tui']['as']['field']		= 'V poli';
$__chassis_msg['pers']['tui']['as']['allfields']	= 'Ve všech polích';
$__chassis_msg['pers']['tui']['as']['norestr']		= 'Jakákoliv hodnota';
$__chassis_msg['pers']['rui']['save']				= 'Uložit';
$__chassis_msg['pers']['rui']['back']				= 'Zpět';
$__chassis_msg['pers']['rui']['notag']				= 'Bez nálepky';
$__chassis_msg['pers']['rui']['ind']['loading']		= $__chassis_msg['srch']['loading'];
$__chassis_msg['pers']['rui']['ind']['loaded']		= $__chassis_msg['srch']['loaded'];
$__chassis_msg['pers']['rui']['ind']['saving']		= 'Ukládám...';
$__chassis_msg['pers']['rui']['ind']['saved']		= 'Uloženo';
$__chassis_msg['pers']['rui']['ind']['preparing']	= 'Připravuji...';
$__chassis_msg['pers']['rui']['ind']['prepared']	= 'Připraveno';
$__chassis_msg['pers']['rui']['ind']['opening']		= 'Otevírá...';
$__chassis_msg['pers']['rui']['ind']['opened']		= 'Načteno';
$__chassis_msg['pers']['rui']['ind']['e_unknown']	= $__chassis_msg['srch']['e_unknown'];
$__chassis_msg['pers']['rui']['ind']['e_nan']		= 'Chyba: hodnota v poli %s není číslo!';
$__chassis_msg['pers']['rui']['ind']['e_invalid']	= 'Chyba: nepovolená hodnota ve vybraném poli!';

/**
 * Tags persistence
 */
$__chassis_msg['tags']['desc']						= 'Popis';
$__chassis_msg['tags']['label']						= 'Nálepka';
$__chassis_msg['tags']['scheme']					= 'Barevná schéma';
$__chassis_msg['tags']['name']						= 'Zobrazit text';
$__chassis_msg['tags']['preview']					= 'Náhled';
$__chassis_msg['tags']['palette']					= 'použít paletu';
$__chassis_msg['tags']['tui']['fold']				= 'Nálepky';
$__chassis_msg['tags']['tui']['headline']			= 'Přehled, přidávání a úpravy nálepek';	// should be overriden by custome message in the instance parameter
$__chassis_msg['tags']['tui']['anchor']				= 'Vytvořit nálepku';
$__chassis_msg['tags']['rui']['edit']				= 'Upravit nálepku';
$__chassis_msg['tags']['rui']['create']				= 'Nová nálepka';
$__chassis_msg['tags']['schemes']					= $__chassis_msg['ctx'];
$__chassis_msg['tags']['Q']							= 'Skutečně chcete zmazat nálepku <b>%s</b>? Tato operace je nevratná.';
$__chassis_msg['tags']['remove']					= 'Odstranit';
$__chassis_msg['tags']['empty']						= 'Nemáte žádné nálepky!';
$__chassis_msg['tags']['nomatch']					= 'Žádné nálepky nebyly nalezeny!';
$__chassis_msg['tags']['redo']						= 'Hledat znovu';
$__chassis_msg['tags']['all']						= 'Zobrazit vše';
$__chassis_msg['tags']['create']					= $__chassis_msg['tags']['tui']['anchor'];

/**
 * SkyDome messages.
 */
$__chassis_msg['sd']['close']			= 'Zavřít';

?>