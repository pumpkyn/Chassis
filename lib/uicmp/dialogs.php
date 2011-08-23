<?php

/**
 * @file dialogs.php
 * @author giorno
 * @package Chassis
 * @subpackage UICMP
 * @license Apache License, Version 2.0, see LICENSE file
 */

namespace io\creat\chassis\uicmp;

require_once CHASSIS_LIB . 'uicmp/vlayout.php';

/**
 * Special component holding references to dialog widgets, their templates and
 * internal data are used in rendering. There is usually only one instance at
 * the time.
 */
class dialogs extends vlayout
{
	/**
	 * Constructor. Creates empty array of uicmps. Each layout should contain at
	 * least one tab.
	 *
	 * @param _requirer $requirer reference to requirer instance
	 * @param _i18n_loader $i18n_loader instance of localization provider
	 */
	public function  __construct ( $requirer, $i18n_loader )
	{
		parent::__construct( $requirer, $i18n_loader );

		if ( !is_null( $requirer ) )
			$requirer->call( vlayout::RES_BODYCHILD, array( CHASSIS_UICMP . 'dlgs.html', '' ) );
	}

	public function init ( )
	{
		/**
		 * Call all children and ask them to render their Javascript code for
		 * <head> element.
		 */
		if ( is_array( $this->uicmps ) )
			foreach ( $this->uicmps as $tab )
				$tab->generateReqs( );
	}
}

?>