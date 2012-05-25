<?php

/**
 * @file libpdo.php
 * @author giorno
 * @package Chassis
 * @license Apache License, Version 2.0, see LICENSE file
 * 
 * Wrapper functions for PDO calls.
 */

namespace io\creat\chassis;

/**
 * Extracts first row of the result set. This call has point only for SELECT
 * statements. To reduce the overhead, query should contain LIMIT 0,1 clause.
 * 
 * @param PDOStatement $sql prepared statement
 * @param int $mode one of the PDO::FETCH_* constants
 * @return first line of output or FALSE
 */
function pdo1l ( $sql, $mode = \PDO::FETCH_BOTH )
{
	$sql->execute( );	
	return $sql->fetch( $mode );
}

/**
 * Same as pdo1l, but allows to specify array of values for binding.
 * 
 * @param PDOStatement $sql prepared statement
 * @param array $params SQL parameters
 * @param int $mode one of the PDO::FETCH_* constants
 * @return first line of output or FALSE
 */
function pdo1lp ( $sql, $params, $mode = \PDO::FETCH_BOTH )
{
	$sql->execute( $params );	
	return $sql->fetch( $mode );
}

/**
 * Extracts first field of the first row of the statement query result set. This
 * call has point only for SELECT statements. To reduce the overhead, query
 * should contain LIMIT 0,1 clause and SELECT only 1 field.
 * @param PDOStatement $sql prepared statement
 * @param array $params binding values, can be NULL
 * @return first column of first row of output or FALSE 
 */
function pdo1f ( $sql, $params = NULL )
{
	if ( is_null( $params ) )
		$row = pdo1l( $sql, \PDO::FETCH_NUM );
	else
		$row = pdo1lp( $sql, $params, \PDO::FETCH_NUM );
	
	if ( is_array( $row ) )
		return $row[0];
	
	return FALSE;
}



?>