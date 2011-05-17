<?PHP

/**
 * @file libdb.php
 * @author giorno
 * @package Chassis
 *
 * API for access to the MySQL database. Backend may change, and this library should
 * be changed respectively.
 */

/**
 * Create connection to the database. Global variable $__SQL_CONN is created and
 * assigned. Should be used only by methods of this library.
 *
 * @param server MySQL server address
 * @param user MySQL username
 * @param passwd user password
 * @param db database
 */
function _db_connect ( $server, $user, $passwd, $db )
{
	global $__SQL_CONN;

	$__SQL_CONN = mysql_connect( $server, $user, $passwd );

	mysql_select_db( $db, $__SQL_CONN );
}

/**
 * Close the connection.
 */
function _db_close ( )
{
	global $__SQL_CONN;

	mysql_close( $__SQL_CONN );
}

/**
 * Perform SQL query. Result resource is passed on return.
 *
 * @param query SQL query
 * @return result of query
 */
function _db_query ( $query )
{
	global $__SQL_CONN;
	//echo $query;
	return mysql_query( $query, $__SQL_CONN );
}

/**
 * Fetch one row from query results (first one). This facilitates
 * 'fetch array' method of backend. Pointer in result resource is moved
 * forward.
 *
 * @param result resource from _db_query()
 * @return array of row values
 */
function _db_fetchrow ( $result )
{
	return mysql_fetch_array( $result, MYSQL_BOTH );
}

/**
 * Perform query and return array of first row from results or false on failure.
 *
 * @param query SQL query
 * @return row array or false
 */
function _db_1line ( $query )
{
	$res = _db_query( $query );
	if ( $res && _db_rowcount( $res ) )
		return _db_fetchrow( $res );
	else
		return false;
}

/**
 * Perform query and return first field from array of first row from results or false on failure.
 *
 * @param query SQL query
 * @return cell value or false
 */
function _db_1field ( $query )
{
	if ( ( $line = _db_1line( $query ) ) && ( count( $line ) > 0 ) )
		return $line[0];
	else
		return false;
}

/**
 * Computes size of result (row count).
 *
 * @param result resource from _db_query()
 */
function _db_rowcount ( $result )
{
	return mysql_num_rows( $result );
}

/**
 * Safety mechanism. All data (variables) passed to the query should (must) be escaped.
 *
 * @param input PLAIN text
 */
function _db_escape ( $input )
{
	global $__SQL_CONN;
	return mysql_real_escape_string( $input, $__SQL_CONN );
}

/**
 * Returns last insered ID.
 * 
 * @global resource $__SQL_CONN
 * @return int 
 */
function _db_lastid ()
{
	global $__SQL_CONN;
	return mysql_insert_id( $__SQL_CONN );
}
?>