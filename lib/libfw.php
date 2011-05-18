<?PHP

/**
 * @file libfw.php
 * @author giorno
 * @package Chassis
 *
 * Common used algorithms of the framework and its users.
 */

/**
 * Random number generator initialization. Should be called before any usage
 * of _fw_rand_hash() or rand() methods.
 *
 * @usage srand( _fw_rand_init( ) );
 */
function _fw_rand_init ( )
{
	list( $usec, $sec ) = explode( ' ', microtime( ) );
	return (float) $sec + ( (float) $usec * 100000 );
}

/**
 * Generates random hash. Method _fw_rand_hash() should be called prior to this one.
 *
 * @return string
 */
function _fw_rand_hash ( )
{
	return md5( uniqid( rand( ), true ) );
}

/**
 * Frontend for setting the coookies. Since there were errors with development on localhost domains,
 * relative path was stripped to single slash, so cookies are valid for all relative paths in URL.
 *
 * @link http://www.aeonity.com/frost/php-setcookie-localhost-apache
 *
 * @param name cookie name
 * @param value cookie value
 * @param expiration expiration of cookie
 */
function _fw_set_cookie ( $name, $value, $expiration )
{
		setcookie( $name, $value, $expiration, '/' );
}

/**
 * Password generator.
 * Inspired by http://www.webtoolkit.info/php-random-password-generator.html
 *
 * @param <int> $length number of characters in the password
 * @return string
 */
function _fw_gen_passwd ( $length = 8 )
{
	$pool = '23456789auyeAUYEbdghjmnpqrstvzBDGHJLMNPQRSTVWXZ@#$%';

	$password = '';
	for ( $i = 0; $i < $length; $i++ )
	{
		$password .= $pool[( rand( ) % strlen( $pool ) )];
	}

	return $password;
}

/**
 * Transform plain text password into its hash.
 *
 * @param <string> $password plain text password
 */
function _fw_hash_passwd ( $password )
{
	return sha1( $password );
}

?>