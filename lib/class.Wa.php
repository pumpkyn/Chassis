<?php

/**
 * @file class.Wa.php
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 */

require_once CHASSIS_CFG . "class.Config.php";

/**
 * Workarounds PHP library class. Some of methods has counterpart written in
 * Javascript. Some code is taked from various sources.
 */
class Wa extends Config
{
	/*
	 * Plus sign workaround. See class.Config.php near XMLREPLACEMENT_PLUSSIGN
	 * definition for details.
	 */
	static function PlusSignWaDecode ( $data )
	{
		return str_replace( self::XMLREPLACEMENT_PLUSSIGN, "+", $data );
	}
	
	/**
	 * Shortens string to have no more than requested number of characters.
	 * 
	 * @param string $string input string
	 * @param int $size desired length
	 * @return string
	 */
	static function CutString( $string, $size )
	{
		if ( $size <= 3 )
			return $string;
		
		if ( strlen( $string ) <= $size )
			return $string;
		
		for ( $i = $size - 3; $i >= 0; --$i )
		{
			if ( $string[$i] == ' ' )
				break;
		}
		
		if ( $i > 0 )
			return substr( $string, 0, ( $i - 1 ) ) . '...';
		else
			return substr( $string, 0, $size ) . '...';
	}

	/**
	 * This is custom WA for escaping slashes and similar entities safely for
	 * purpose in complex and combined (PHP/HTML/Javascript) environment. This
	 * was copied here becouse of not proper function of html* methods of PHP.
	 * 
	 * http://stackoverflow.com/questions/168214/pass-a-php-string-to-a-javascript-variable-including-escaping-newlines
	 *
	 * @param string input struing
	 * @return string
	 * @author Toby Allen
	 */
	static function JsStringEscape ( $string )
	{
		return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
	}

	/**
	 * Recursive method to provide Javascript code to deliver i18n strings.
	 *
	 * @param <array> $input input array
	 * @param <string> $jsVarName name for Javascript variable
	 * @param <mixed> $buf used only internally for recursion
	 * @return string
	 */
	static function JsMessages ( &$input, $jsVarName, &$buf = NULL )
	{
		/**
		 * At the beginning create javascript variable scope.
		 */
		if ( is_null( $buf ) )
			$buf = 'var ';

		$buf .= $jsVarName . ' = new Object( );';

		/**
		 * Iterate through the input.
		 */
		if ( is_array( $input ) )
		{
			foreach ( $input as $key => $value )
			{
				if ( is_string(  $value ) )
					$buf .= $jsVarName . '[\'' . $key . '\'] = \'' . $value . '\';';
				elseif ( is_array( $value ) )
					static::JsMessages( $value, $jsVarName . '[\'' . $key . '\']', $buf );
			}
		}

		return $buf;
	}

}
?>