<?PHP

/**
 * @file class.Config.php
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 */

/**
 * Configuration of Chassis Framework core database tables and fields names,
 * cookies used for session tracing and framework specific constants.
 * 
 * @todo rethink name of the class, it may be misleading as it is uncertain
 * config of what it refers to
 */
class Config
{
	/**
	 * Database tables and fields names
	 */
	const T_USERS				= 'tUsers';
	const F_UID					= 'uid';
	const F_LOGIN				= 'login';
	const F_PASSWD				= 'passwd';
	const F_EMAIL				= 'email';
	const F_ENABLED				= 'enabled';
	
	const T_SESSIONS			= 'tSessions';
	const F_SID					= 'sid';
	const F_CLID				= 'clid';
	const F_IP					= 'ip';
	const F_VALID				= 'valid';

	const T_SETTINGS			= 'tSettings';
	const F_SCOPE				= 'scope';
	const F_NS					= 'ns';
	const F_KEY					= 'key';
	const F_VALUE				= 'value';
	const F_ID					= 'id';

	const T_SIGNTOKENS			= 'tSignTokens';
	const F_TOKEN				= 'token';

	const T_LOGINS				= 'tLogins';
	const F_APP					= 'app';
	const F_STAMP				= 'stamp';

	/**
	 * Time in minutes to keep session valid (should apply only for database
	 * record in T_SESSIONS table).
	 */
	const SESSIONEXPIRATION		= 60;


	/**
	 * Cookies names, this may and is recommended to be changed for each
	 * deployment.
	 */
	const COOKIE_CLIENTID		= 'urhc_xj4zmdjs8v3skf2aas';
	const COOKIE_TOKENID		= 'urht_8d1gh4jas7d2oz34ekz93ef8c4';
	const COOKIE_SESSIONID		= 'urhs_mc44s7xz9-aa';

	/**
	 * Cookie expiration time in days.
	 */
	const COOKIE_EXPIRATION		= 30;

	/*
	 * This is workaround for plus sign passed in XML from the client (Ajax). On
	 * the Javascript side - method waPlusSignWaEncode() - all plus signs in
	 * XML document are replaced with value of this constant and in scheme
	 * instances (company or person) this operation is reverted using same
	 * constant.
	 *
	 * It is strange that in the way of using XML documents for contact details
	 * plus signs cannot pass. In Ethereal/Wireshark trace it is present, but
	 * in $_POST global variable it is missing. This is reason for this
	 * workaround. Other math characters passes without problem.
	 *
	 * This sign is international call prefix (for contact's phone numbers).
	 */
	const XMLREPLACEMENT_PLUSSIGN  = '(-)*(-)_=_plus';
}

?>