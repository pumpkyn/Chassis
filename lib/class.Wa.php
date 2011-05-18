<?PHP

require_once CHASSIS_CFG . "class.Config.php";

/**
 * @file class.Wa.php
 * @author giorno
 * @package Chassis
 *
 * Workarounds PHP library. Some of methods has counterpart written in
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