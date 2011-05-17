<?PHP

/**
 * @file config.php
 * @author giorno
 * @package Chassis
 *
 * Self-configuration of Chassis Framework.
 */

/*
 * Path to the Chassis Framework root directory.
 */
define( 'CHASSIS_ROOT',		dirname( __FILE__ ) . '/../' );

/*
 * Path to configuration folder.
 */
define( 'CHASSIS_CFG',		CHASSIS_ROOT . 'cfg/' );

/*
 * Path to PHP library folder.
 */
define( 'CHASSIS_LIB',		CHASSIS_ROOT . 'lib/' );

/*
 * Localization resources.
 */
define( 'CHASSIS_I18N',		CHASSIS_ROOT . 'i18n/' );

/*
 * User interface folder.
 */
define( 'CHASSIS_UI',		CHASSIS_ROOT . 'ui/' );
define( 'CHASSIS_UICMP',	CHASSIS_UI . 'uicmp/' );

/*
 * 3rd party libraries
 */
define( 'CHASSIS_3RD',		CHASSIS_ROOT . '3rd/' );

/*
 * Temporary files directory used for generated content (e.g. Smarty) or
 * temporary caches.
 */
define( 'CHASSIS_TMP',		CHASSIS_ROOT . 'tmp/' );

?>