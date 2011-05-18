<?php

/**
 * @file _session.php
 * @author giorno
 * @package Chassis
 * @subpackage Session.
 *
 * Session tracking instance.
 */

require_once CHASSIS_CFG . 'class.Config.php';
require_once CHASSIS_LIB . 'libfw.php';
require_once CHASSIS_LIB . 'libdb.php';

class _session extends Config
{
	/**
	 * User Id. Integer number from users table.
	 * 
	 * @var <int>
	 */
	private $UID = NULL;

	/**
	 * User's nickname (=login).
	 * 
	 * @var <string>
	 */
	private $nickName = NULL;

	/**
	 * Indicated if session exists and user is authenticated.
	 * 
	 * @var <bool>
	 */
	private $signed = FALSE;

	/**
	 * Cookie. Client identifier.
	 *
	 * @var <string>
	 */
	private $clientId;

	/**
	 * Cookie. Session identifier.
	 *
	 * @var <string>
	 */
	private $sessionId;

	/**
	 * Cookie. Autologin token identifier.
	 * 
	 * @var <string>
	 */
	private $alToken;

	/**
	 * Constructor.
	 */
	public function __construct ( )
	{
		/**
		 * Load client Id from cookies.
		 */
		$this->handleClientId( );

		/**
		 * Session Id from cookies is invalid.
		 */
		if ( !$this->validateSessionId( ) )
		{
			$this->validateAutologin();
		}
	}

	/**
	 * If there is client ID in cookies, return its value and renew expiration datetime, if ID is not
	 * set in cookies, it shall be created and its value returned.
	 */
	private function handleClientId ( )
	{
		$this->clientId = '';

		if ( array_key_exists( static::COOKIE_CLIENTID, $_COOKIE ) )
			$this->clientId = (string)$_COOKIE[static::COOKIE_CLIENTID];

		/**
		 * There is no cookie for client id. It means it is either expired or
		 * solution was never used from this browser instance.
		 */
		if ( $this->clientId == '' )
		{
			$this->clientId = _fw_rand_hash( );
			//$this->Debug( '[ClientIdCreated] Id=' . $cookie );
		}
		else
		{
			//$this->Debug( '[ClientIdLoaded] Id=' . $cookie );
		}

		_fw_set_cookie( self::COOKIE_CLIENTID, $this->clientId, time() + static::COOKIE_EXPIRATION * 24 * 60 * 60 );
	}

	private function validateSessionId ( )
	{
		$this->wipe( );
		
		/*
		 * try SID from cookies
		 */
		$this->sessionId = '';
		if ( array_key_exists(self::COOKIE_SESSIONID, $_COOKIE) )
			$this->sessionId = (string)$_COOKIE[self::COOKIE_SESSIONID];

		/**
		 * Check if session Id from cookies is valid.
		 */
		if ( $this->sessionId != '' )
		{
			$record = _db_1line( "SELECT * FROM `" . self::T_SESSIONS . "`
							WHERE `" . self::F_SID . "` = \"" . _db_escape( $this->sessionId ) . "\"
							AND `" . self::F_CLID . "` = \"" . _db_escape( $this->clientId ) . "\"");

			if ( $record && count( $record ) )
			{
				$this->UID = $record[self::F_UID];
				$this->signed = true;

				_db_query( "UPDATE `" . self::T_SESSIONS . "`
							SET `" . self::F_VALID . "` = (NOW() + INTERVAL " . self::SESSIONEXPIRATION * 60 . " SECOND)
							WHERE `" . self::F_SID . "` = \"" . _db_escape( $this->sessionId ) . "\"
							AND `" . self::F_CLID . "` = \"" . _db_escape( $this->clientId ) . "\"");

				_fw_set_cookie( self::COOKIE_SESSIONID, $this->sessionId, 0 );
				$this->loadNick( );

				return true;
			}
		}
		return false;
	}

	/**
	 * Validates autologin cookies and tokens.
	 *
	 * @return <bool>
	 */
	private function validateAutologin ( )
	{
		/*
		 * try autologin token from cookies
		 */
		$this->alToken = '';
		if ( array_key_exists(self::COOKIE_TOKENID, $_COOKIE) )
			$this->alToken = (string)$_COOKIE[self::COOKIE_TOKENID];

		if ( $this->alToken != '' )
		{
			if ( $record = _db_1field ( "SELECT `" . self::F_UID . "`
									FROM `" . self::T_SIGNTOKENS . "`
									WHERE `" . self::F_CLID . "` = \"" . _db_escape( $this->clientId ) . "\"
									AND `" . self::F_TOKEN . "` = \"" . _db_escape( $this->alToken ) . "\"" ) )
			{
				$this->UID = $record;

				_db_query( "UPDATE `" . self::T_SIGNTOKENS . "`
								SET `" . self::F_VALID . "` = (NOW() + INTERVAL " . self::COOKIE_EXPIRATION * 24 * 60 * 60 . " SECOND)
								WHERE `" . self::F_TOKEN . "` = \"" . _db_escape( $this->alToken ) . "\" AND
								`" . self::F_UID . "` = \"" . _db_escape( $this->UID ) . "\" AND
								`" . self::F_CLID . "` = \"" . _db_escape( $this->clientId ) . "\" ");

				_fw_set_cookie( self::COOKIE_TOKENID, $this->alToken, time() + self::COOKIE_EXPIRATION * 24 * 60 * 60 );

				$this->create( );
				$this->loadNick( );

				return true;
			}
		}

		return false;
	}

	/**
	 * Performs login operation.
	 *
	 * @param <string> $ns namespace,identifier of user solution
	 * @param <string> $username login
	 * @param <string> $password password
	 * @param <bool> $auto autologin flag
	 * @return <bool>
	 */
	public function login ( $ns, $username, $password, $auto = FALSE )
	{
		$hash =  _fw_hash_passwd( $password );

		/**
		 * Check provided login data.
		 */
		$cache = $this->UID;
		if ( ( $this->UID = _db_1field ( "SELECT `" . self::F_UID . "`
											FROM `" . self::T_USERS . "`
											WHERE `" . self::F_LOGIN . "` = \"" . _db_escape( $username ) . "\"
											AND `" . self::F_PASSWD . "` = \"" . _db_escape( $hash ) . "\"
											AND `" . self::F_ENABLED . "` = '1'" ) ) !== false )
		{
			/**
			 * Create session record.
			 */
			if ( !$this->create( ) )
				return false;

			/**
			 * Create record in autologin tokens table.
			 */
			if ( $auto )
			{
				$this->alToken = _fw_rand_hash( );
				_db_query( "INSERT INTO `" . self::T_SIGNTOKENS . "`
							SET `" . self::F_TOKEN . "` = \"" . _db_escape( $this->alToken ) . "\",
							`" . self::F_UID . "` = \"" . _db_escape( $this->UID ) . "\",
							`" . self::F_CLID . "` = \"" . _db_escape( $this->clientId ) . "\",
							`" . self::F_IP . "` = \"" . _db_escape( ipAddr ) . "\",
							`" . self::F_VALID . "` = (NOW() + INTERVAL " . self::COOKIE_EXPIRATION * 24 * 60 * 60 . " SECOND) ");
				_fw_set_cookie( self::COOKIE_TOKENID, $this->alToken, time( ) + self::COOKIE_EXPIRATION * 24 * 60 * 60 );
			}


			_db_query( "INSERT INTO `" . self::T_LOGINS . "`
						SET `" . self::F_UID . "` = \"" . _db_escape( $this->UID ) . "\",
						`" . self::F_NS . "` = \"" . _db_escape( $ns ) . "\",
						`" . self::F_STAMP . "` = NOW()" );
			return true;
		}
		else
		{
			/*
			 * Revert from cache.
			 */
			$this->UID = $cache;
			return false;
		}
	}

	/**
	 * Perform logout and erase all session, token and cookies.
	 */
	public function logout ( )
	{
		_db_query( "DELETE FROM `" . self::T_SESSIONS . "`
					WHERE `" . self::F_SID . "` = \"" . _db_escape( $this->sessionId ) . "\"
					AND `" . self::F_CLID . "` = \"" . _db_escape( $this->clientId ) . "\"
					AND `" . self::F_UID . "` = \"" . _db_escape( $this->UID ) . "\"" );

		_db_query( "DELETE FROM `" . self::T_SIGNTOKENS . "`
					WHERE `" . self::F_CLID . "` = \"" . _db_escape( $this->clientId ) . "\"" );

		_fw_set_cookie( 	self::COOKIE_SESSIONID, '', 0);
		_fw_set_cookie( 	self::COOKIE_TOKENID, '', time() - self::COOKIE_EXPIRATION * 24 * 60 * 60 );
		_fw_set_cookie( 	self::COOKIE_CLIENTID, '', time() - self::COOKIE_EXPIRATION * 24 * 60 * 60 );
		$this->signed = FALSE;

		return !$this->validateSessionId( );
	}

	/**
	 * Create session for user.
	 *
	 * @return <bool>
	 */
	private function create ( )
	{
		$this->wipe( );
		
		$ipAddr = $_SERVER['REMOTE_ADDR'];

		/**
		 * Creates session records in the table.
		 */
		$this->sessionId = _fw_rand_hash( );
		_db_query( "INSERT INTO `" . self::T_SESSIONS . "`
					SET `" . self::F_SID . "` = \"" . _db_escape( $this->sessionId ) . "\",
					`" . self::F_UID . "` = \"" . _db_escape( $this->UID ) . "\",
					`" . self::F_CLID . "` = \"" . _db_escape( $this->clientId ) . "\",
					`" . self::F_IP . "` = \"" . _db_escape( $ipAddr ) . "\",
					`" . self::F_VALID . "` = (NOW() + INTERVAL " . self::SESSIONEXPIRATION * 60 . " SECOND) ");

		/**
		 * Session was created.
		 */
		if ( _db_1line( "SELECT `" . self::F_SID . "`
							FROM `" . self::T_SESSIONS . "`
							WHERE `" . self::F_SID . "` = \"" . _db_escape( $this->sessionId ) . "\"" ) !== false )
		{
			_fw_set_cookie( self::COOKIE_SESSIONID, $this->sessionId, 0/*time() + self::COOKIE_EXPIRATION * 24 * 60  * 60*/ );

			$this->signed = true;
			return true;
		}
	}

	/**
	 * Wipe all expired sessions.
	 */
	private function wipe ( ) { _db_query( "DELETE FROM `" . self::T_SESSIONS . "` WHERE `" . self::F_VALID . "` < NOW()" ); }

	/**
	 * Load user name (nick).
	 *
	 * @return <string>
	 */
	private function loadNick (  )
	{
		return ( ( $this->nickName = _db_1field ( "SELECT `" . self::F_LOGIN . "`
											FROM `" . self::T_USERS . "`
											WHERE `" . self::F_UID . "` = \"" . _db_escape( $this->UID ) . "\"" ) ) !== false );
	}

	/**
	 * Queries status of session.
	 *
	 * @return <bool>
	 */
	public function isSigned ( ) { return $this->signed === TRUE; }

	/**
	 * Returns nickname of the user.
	 *
	 * @return <string>
	 */
	public function getNick ( ) { return $this->nickName; }

	/**
	 * Returns user Id.
	 *
	 * @return <int>
	 */
	public function getUid ( ) { return $this->UID; }

	/**
	 * Returns session Id.
	 *
	 * @return <string>
	 */
	public function getSid ( ) { return $this->sessionId; }

}

?>
