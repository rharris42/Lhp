<?php
  /**
   * Copyright 2015 Last Hit Productions, Inc.
   */

  /**
   * Class LhpMysql
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpMysql extends mysqli {

    /**
	 * @var array - List of mysql functions and their uses
	 */
	private $function_list = array(
      'ABS' => 'Return the absolute value',
      'ACOS' => 'Return the arc cosine',
      'ADDDATE' => 'Add time values (intervals) to a date value',
      'ADDTIME' => 'Add time',
      'AES_DECRYPT' => 'Decrypt using AES',
      'AES_ENCRYPT' => 'Encrypt using AES',
      'Area' => 'Return Polygon area',
      'AsBinary' => 'Convert from internal geometry format to WKB',
      'AsWKB' => 'Convert from internal geometry format to WKB',
      'ASCII' => 'Return numeric value of left-most character',
      'ASIN' => 'Return the arc sine',
      'AsText' => 'Convert from internal geometry format to WKT',
      'AsWKT' => 'Convert from internal geometry format to WKT',
      'ATAN2' => 'Return the arc tangent of the two arguments',
      'ATAN' => 'Return the arc tangent',
      'AVG' => 'Return the average value of the argument',
      'BENCHMARK' => 'Repeatedly execute an expression',
      'BIN' => 'Return a string containing binary representation of a number',
      'BINARY Cast a string to a binary string',
      'BIT_AND' => 'Return bitwise and',
      'BIT_COUNT' => 'Return the number of bits that are set',
      'BIT_LENGTH' => 'Return length of argument in bits',
      'BIT_OR' => 'Return bitwise or',
      'BIT_XOR' => 'Return bitwise xor',
      'Boundary' => 'Return geometry boundary',
      'CAST' => 'Cast a value as a certain type',
      'CEIL' => 'Return the smallest integer value not less than the argument',
      'CEILING' => 'Return the smallest integer value not less than the argument',
      'Centroid' => 'Return centroid as a point',
      'CHAR_LENGTH' => 'Return number of characters in argument',
      'CHAR' => 'Return the character for each integer passed',
      'CHARACTER_LENGTH' => 'Synonym for CHAR_LENGTH()',
      'CHARSET' => 'Return the character set of the argument',
      'COALESCE' => 'Return the first non-NULL argument',
      'COERCIBILITY' => 'Return the collation coercibility value of the string argument',
      'COLLATION' => 'Return the collation of the string argument',
      'COMPRESS' => 'Return result as a binary string',
      'CONCAT_WS' => 'Return concatenate with separator',
      'CONCAT' => 'Return concatenated string',
      'CONNECTION_ID' => 'Return the connection ID (thread ID) for the connection',
      'Contains' => 'Whether one geometry contains another',
      'CONV' => 'Convert numbers between different number bases',
      'CONVERT_TZ' => 'Convert from one timezone to another',
      'CONVERT' => 'Cast a value as a certain type',
      'COS' => 'Return the cosine',
      'COT' => 'Return the cotangent',
      'COUNT(DISTINCT) Return the count of a number of different values',
      'COUNT' => 'Return a count of the number of rows returned',
      'CRC32' => 'Compute a cyclic redundancy check value',
      'Crosses' => 'Whether one geometry crosses another',
      'CURDATE' => 'Return the current date',
      'CURRENT_DATE' => 'CURRENT_DATE Synonyms for CURDATE()',
      'CURRENT_TIME' => 'CURRENT_TIME Synonyms for CURTIME()',
      'CURRENT_TIMESTAMP' => 'CURRENT_TIMESTAMP Synonyms for NOW()',
      'CURRENT_USER' => 'CURRENT_USER The authenticated user name and host name',
      'CURTIME' => 'Return the current time',
      'DATABASE' => 'Return the default (current) database name',
      'DATE_ADD' => 'Add time values (intervals) to a date value',
      'DATE_FORMAT' => 'Format date as specified',
      'DATE_SUB' => 'Subtract a time value (interval) from a date',
      'DATE' => 'Extract the date part of a date or datetime expression',
      'DATEDIFF' => 'Subtract two dates',
      'DAY' => 'Synonym for DAYOFMONTH()',
      'DAYNAME' => 'Return the name of the weekday',
      'DAYOFMONTH' => 'Return the day of the month (0-31)',
      'DAYOFWEEK' => 'Return the weekday index of the argument',
      'DAYOFYEAR' => 'Return the day of the year (1-366)',
      'DECODE' => 'Decodes a string encrypted using ENCODE()',
      'DEFAULT' => 'Return the default value for a table column',
      'DEGREES' => 'Convert radians to degrees',
      'DES_DECRYPT' => 'Decrypt a string',
      'DES_ENCRYPT' => 'Encrypt a string',
      'Dimension' => 'Dimension of geometry',
      'Disjoint' => 'Whether one geometry is disjoint from another',
      'ELT' => 'Return string at index number',
      'ENCODE' => 'Encode a string',
      'ENCRYPT' => 'Encrypt a string',
      'EndPoint' => 'End Point of LineString',
      'Envelope' => 'Return MBR of geometry',
      'Equals' => 'Whether one geometry is equal to another',
      'EXP' => 'Raise to the power of',
      'EXPORT_SET' => 'Return a string such that for every bit set in the value bits, you get an on string and for every unset bit, you get an off string',
      'ExteriorRing' => 'Return exterior ring of Polygon',
      'EXTRACT' => 'Extract part of a date',
      'FIELD' => 'Return the index (position) of the first argument in the subsequent arguments',
      'FIND_IN_SET' => 'Return the index position of the first argument within the second argument',
      'FLOOR' => 'Return the largest integer value not greater than the argument',
      'FORMAT' => 'Return a number formatted to specified number of decimal places',
      'FOUND_ROWS' => 'For a SELECT with a LIMIT clause, the number of rows that would be returned were there no LIMIT clause',
      'FROM_DAYS' => 'Convert a day number to a date',
      'FROM_UNIXTIME' => 'Format UNIX timestamp as a date',
      'GeomCollFromText' => 'Return geometry collection from WKT',
      'GeometryCollectionFromText' => 'Return geometry collection from WKT',
      'GeomCollFromWKB' => 'Return geometry collection from WKB',
      'GeometryCollectionFromWKB' => 'Return geometry collection from WKB',
      'GeometryCollection' => 'Construct geometry collection from geometries',
      'GeometryN' => 'Return N-th geometry from geometry collection',
      'GeometryType' => 'Return name of geometry type',
      'GeomFromText' => 'Return geometry from WKT',
      'GeometryFromText' => 'Return geometry from WKT',
      'GeomFromWKB' => 'Return geometry from WKB',
      'GET_FORMAT' => 'Return a date format string',
      'GET_LOCK' => 'Get a named lock',
      'GLength' => 'Return length of LineString',
      'GREATEST' => 'Return the largest argument',
      'GROUP_CONCAT' => 'Return a concatenated string',
      'HEX' => 'Return a hexadecimal representation of a decimal or string value',
      'HOUR' => 'Extract the hour',
      'IF' => 'If/else construct',
      'IFNULL' => 'Null if/else construct',
      'IN' => 'Check whether a value is within a set of values',
      'INET_ATON' => 'Return the numeric value of an IP address',
      'INET_NTOA' => 'Return the IP address from a numeric value',
      'INSERT' => 'Insert a substring at the specified position up to the specified number of characters',
      'INSTR' => 'Return the index of the first occurrence of substring',
      'InteriorRingN' => 'Return N-th interior ring of Polygon',
      'Intersects' => 'Whether one geometry intersects another',
      'INTERVAL' => 'Return the index of the argument that is less than the first argument',
      'IS_FREE_LOCK' => 'Checks whether the named lock is free',
      'IS_USED_LOCK' => 'Checks whether the named lock is in use. Return connection identifier if true.',
      'IsClosed' => 'Whether a geometry is closed and simple',
      'IsEmpty' => 'Placeholder function',
      'ISNULL' => 'Test whether the argument is NULL',
      'IsSimple' => 'Whether a geometry is simple',
      'LAST_DAY Return the last day of the month for the argument',
      'LAST_INSERT_ID' => 'Value of the AUTOINCREMENT column for the last INSERT',
      'LCASE' => 'Synonym for LOWER()',
      'LEAST' => 'Return the smallest argument',
      'LEFT' => 'Return the leftmost number of characters as specified',
      'LENGTH' => 'Return the length of a string in bytes',
      'LIKE Simple pattern matching',
      'LineFromText' => 'Construct LineString from WKT',
      'LineFromWKB' => 'Construct LineString from WKB',
      'LineStringFromWKB' => 'Construct LineString from WKB',
      'LineString' => 'Construct LineString from Point values',
      'LN' => 'Return the natural logarithm of the argument',
      'LOAD_FILE' => 'Load the named file',
      'LOCALTIME' => 'LOCALTIME Synonym for NOW()',
      'LOCALTIMESTAMP Synonym for NOW()',
      'LOCALTIMESTAMP' => 'Synonym for NOW()',
      'LOCATE' => 'Return the position of the first occurrence of substring',
      'LOG10' => 'Return the base-10 logarithm of the argument',
      'LOG2' => 'Return the base-2 logarithm of the argument',
      'LOG' => 'Return the natural logarithm of the first argument',
      'LOWER' => 'Return the argument in lowercase',
      'LPAD' => 'Return the string argument, left-padded with the specified string',
      'LTRIM' => 'Remove leading spaces',
      'MAKE_SET' => 'Return a set of comma-separated strings that have the corresponding bit in bits set',
      'MAKEDATE' => 'Create a date from the year and day of year',
      'MAKETIME' => 'Create time from hour, minute, second',
      'MASTER_POS_WAIT' => 'Block until the slave has read and applied all updates up to the specified position',
      'MATCH Perform full-text search',
      'MAX' => 'Return the maximum value',
      'MBRContains' => 'Whether MBR of one geometry contains MBR of another',
      'MBRDisjoint' => 'Whether MBRs of two geometries are disjoint',
      'MBREqual' => 'Whether MBRs of two geometries are equal',
      'MBRIntersects' => 'Whether MBRs of two geometries intersect',
      'MBROverlaps' => 'Whether MBRs of two geometries overlap',
      'MBRTouches' => 'Whether MBRs of two geometries touch',
      'MBRWithin' => 'Whether MBR of one geometry is within MBR of another',
      'MD5' => 'Calculate MD5 checksum',
      'MICROSECOND' => 'Return the microseconds from argument',
      'MID' => 'Return a substring starting from the specified position',
      'MIN' => 'Return the minimum value',
      'MINUTE' => 'Return the minute from the argument',
      'MLineFromText' => 'Construct MultiLineString from WKT',
      'MultiLineStringFromText' => 'Construct MultiLineString from WKT',
      'MLineFromWKB' => 'Construct MultiLineString from WKB',
      'MultiLineStringFromWKB' => 'Construct MultiLineString from WKB',
      'MOD' => 'Return the remainder',
      'MONTH' => 'Return the month from the date passed',
      'MONTHNAME' => 'Return the name of the month',
      'MPointFromText' => 'Construct MultiPoint from WKT',
      'MultiPointFromText' => 'Construct MultiPoint from WKT',
      'MPointFromWKB' => 'Construct MultiPoint from WKB',
      'MultiPointFromWKB' => 'Construct MultiPoint from WKB',
      'MPolyFromText' => 'Construct MultiPolygon from WKT',
      'MultiPolygonFromText' => 'Construct MultiPolygon from WKT',
      'MPolyFromWKB' => 'Construct MultiPolygon from WKB',
      'MultiPolygonFromWKB' => 'Construct MultiPolygon from WKB',
      'MultiLineString' => 'Contruct MultiLineString from LineString values',
      'MultiPoint' => 'Construct MultiPoint from Point values',
      'MultiPolygon' => 'Construct MultiPolygon from Polygon values',
      'NAME_CONST' => 'Causes the column to have the given name',
      'NOT_IN' => 'Check whether a value is not within a set of values',
      'NOW' => 'Return the current date and time',
      'NULLIF' => 'Return NULL if expr1 = expr2',
      'NumGeometries' => 'Return number of geometries in geometry collection',
      'NumInteriorRings' => 'Return number of interior rings in Polygon',
      'NumPoints' => 'Return number of points in LineString',
      'OCT' => 'Return a string containing octal representation of a number',
      'OCTET_LENGTH' => 'Synonym for LENGTH()',
      'OLD_PASSWORD' => '(deprecated 5.6.5) Return the value of the pre-4.1 implementation of PASSWORD',
      'ORD' => 'Return character code for leftmost character of the argument',
      'Overlaps' => 'Whether one geometry overlaps another',
      'PASSWORD' => 'Calculate and return a password string',
      'PERIOD_ADD' => 'Add a period to a year-month',
      'PERIOD_DIFF' => 'Return the number of months between periods',
      'PI' => 'Return the value of pi',
      'Point' => 'Construct Point from coordinates',
      'PointFromText' => 'Construct Point from WKT',
      'PointFromWKB' => 'Construct Point from WKB',
      'PointN' => 'Return N-th point from LineString',
      'PolyFromText' => 'Construct Polygon from WKT',
      'PolygonFromText' => 'Construct Polygon from WKT',
      'PolyFromWKB' => 'Construct Polygon from WKB',
      'PolygonFromWKB' => 'Construct Polygon from WKB',
      'Polygon' => 'Construct Polygon from LineString arguments',
      'POSITION' => 'Synonym for LOCATE()',
      'POW' => 'Return the argument raised to the specified power',
      'POWER' => 'Return the argument raised to the specified power',
      'PROCEDURE ANALYSE' => 'Analyze the results of a query',
      'QUARTER' => 'Return the quarter from a date argument',
      'QUOTE' => 'Escape the argument for use in an SQL statement',
      'RADIANS' => 'Return argument converted to radians',
      'RAND' => 'Return a random floating-point value',
      'REGEXP Pattern matching using regular expressions',
      'RELEASE_LOCK' => 'Releases the named lock',
      'REPEAT' => 'Repeat a string the specified number of times',
      'REPLACE' => 'Replace occurrences of a specified string',
      'REVERSE' => 'Reverse the characters in a string',
      'RIGHT' => 'Return the specified rightmost number of characters',
      'RLIKE Synonym for REGEXP',
      'ROUND' => 'Round the argument',
      'ROW_COUNT' => 'The number of rows updated',
      'RPAD' => 'Append string the specified number of times',
      'RTRIM' => 'Remove trailing spaces',
      'SCHEMA' => 'Synonym for DATABASE()',
      'SEC_TO_TIME' => 'Converts seconds to \'HH:MM:SS\' format',
      'SECOND' => 'Return the second (0-59)',
      'SESSION_USER' => 'Synonym for USER()',
      'SHA1' => 'Calculate an SHA-1 160-bit checksum',
      'SHA' => 'Calculate an SHA-1 160-bit checksum',
      'SIGN' => 'Return the sign of the argument',
      'SIN' => 'Return the sine of the argument',
      'SLEEP' => 'Sleep for a number of seconds',
      'SOUNDEX' => 'Return a soundex string',
      'SOUNDS LIKE Compare sounds',
      'SPACE' => 'Return a string of the specified number of spaces',
      'SQRT' => 'Return the square root of the argument',
      'SRID' => 'Return spatial reference system ID for geometry',
      'StartPoint' => 'Start Point of LineString',
      'STD' => 'Return the population standard deviation',
      'STDDEV_POP' => 'Return the population standard deviation',
      'STDDEV_SAMP' => 'Return the sample standard deviation',
      'STDDEV' => 'Return the population standard deviation',
      'STR_TO_DATE' => 'Convert a string to a date',
      'STRCMP' => 'Compare two strings',
      'SUBDATE' => 'Synonym for DATE_SUB() when invoked with three arguments',
      'SUBSTR' => 'Return the substring as specified',
      'SUBSTRING_INDEX' => 'Return a substring from a string before the specified number of occurrences of the delimiter',
      'SUBSTRING' => 'Return the substring as specified',
      'SUBTIME' => 'Subtract times',
      'SUM' => 'Return the sum',
      'SYSDATE' => 'Return the time at which the function executes',
      'SYSTEM_USER' => 'Synonym for USER()',
      'TAN' => 'Return the tangent of the argument',
      'TIME_FORMAT' => 'Format as time',
      'TIME_TO_SEC' => 'Return the argument converted to seconds',
      'TIME' => 'Extract the time portion of the expression passed',
      'TIMEDIFF' => 'Subtract time',
      'TIMESTAMP' => 'With a single argument, this function returns the date or datetime expression; with two arguments, the sum of the arguments',
      'TIMESTAMPADD' => 'Add an interval to a datetime expression',
      'TIMESTAMPDIFF' => 'Subtract an interval from a datetime expression',
      'TO_DAYS' => 'Return the date argument converted to days',
      'Touches' => 'Whether one geometry touches another',
      'TRIM' => 'Remove leading and trailing spaces',
      'TRUNCATE' => 'Truncate to specified number of decimal places',
      'UCASE' => 'Synonym for UPPER()',
      'UNCOMPRESS' => 'Uncompress a string compressed',
      'UNCOMPRESSED_LENGTH' => 'Return the length of a string before compression',
      'UNHEX' => 'Return a string containing hex representation of a number',
      'UNIX_TIMESTAMP' => 'Return a UNIX timestamp',
      'UPPER' => 'Convert to uppercase',
      'USER' => 'The user name and host name provided by the client',
      'UTC_DATE' => 'Return the current UTC date',
      'UTC_TIME' => 'Return the current UTC time',
      'UTC_TIMESTAMP' => 'Return the current UTC date and time',
      'UUID' => 'Return a Universal Unique Identifier (UUID)',
      'VALUES' => 'Defines the values to be used during an INSERT',
      'VAR_POP' => 'Return the population standard variance',
      'VAR_SAMP' => 'Return the sample variance',
      'VARIANCE' => 'Return the population standard variance',
      'VERSION' => 'Return a string that indicates the MySQL server version',
      'WEEK' => 'Return the week number',
      'WEEKDAY' => 'Return the weekday index',
      'WEEKOFYEAR' => 'Return the calendar week of the date (0-53)',
      'Within' => 'Whether one geometry is within another',
      'X' => 'Return X coordinate of Point',
      'Y' => 'Return Y coordinate of Point',
      'YEAR' => 'Return the year',
      'YEARWEEK' => 'Return the year and week'
	);

    /**
     * @var string - Mysql username
     */
	private $user = '';

    /**
     * @var string - Mysql password
     */
	private $pass = '';

    /**
     * @var string - Mysql database
     */
	private $db = '';

    /**
     * @var string - Mysql host
     */
	private $host = '';

    /**
     * @var string - Mysql port
     */
	private $port = 0;

    /**
     * @var bool - Connected status flag
     */
	private $connected = false;

    /**
     * @var object|null - Current result handler
     */
	private $result = null;

    /**
     * @var string|null - Current query being processed
     */
	private $last_query = null;

    /**
     * @var int - Number of rows found from a SELECT query
     */
	private $num_rows = 0;

    /**
     * LhpMysql - Returns mysqli object
     *
	 * @param string $user
	 * @param string $pass
	 * @param string $db
	 * @param string $host
     */
	public function __construct($user,$pass,$db,$host,$port) {
	  $this->user = $user;
	  $this->pass = $pass;
	  $this->db = $db;
	  $this->host = $host;
	  $this->port = $port;
	}

    /**
     * connect - Connects to mysql server
     *
	 * @throws exception
     */
	public function connect() {
	  if(!$this->connected) {
	    parent::init();
        if(!parent::real_connect($this->host, $this->user, $this->pass, $this->db, $this->port)) {
          throw new Exception($this->connect_error);
        }
	    else {
	      $this->connected = true;
	    }
      }
	  return $this;
	}

    /**
     * disconnect - Connects to mysql server
     *
	 * @throws exception
     */
	public function disconnect() {
	  if($this->connected) {
	    $this->close();
	    $this->connected = false;
      }
	  return $this;
	}

    /**
     * query - Execute mysql query
     *
     * @param string $query
     *
     * @return object|bool
     *
	 * @throws exception
     */
	public function query($query,$ignore=false) {
	  $this->connect();
	  if($this->last_query !== $query) {
	    if($this->result === null) {
		  $this->last_query = $query;
		  $res = parent::query($query);
          if(!$res && !$ignore) {
	        throw new Exception($this->error . "\n" . "sql: " . $query);
	      }
		  else if(preg_match('/^select /i', $query)) {
		    $this->result = $res;
		    $this->num_rows = $this->result->num_rows;
		  }
		  else if(preg_match('/^(?:insert|update|replace|delete) /i', $query)) {
		    //$this->num_rows = $this->affected_rows;
			return $res;
		  }
		}
		else {
		  throw new Exception("You are trying to fetch another query before freeing result of last query.");
		}
	  }
	  return $this;
	}

    /**
     * fetch - Executes $query if supplied and fetchs associative array
     *
     * @param string $query
     *
	 * @return array|null
	 *
	 & @throws exception
     */
	public function fetch($query=null,$ignore=false) {
	  if($query !== null) {
		$this->query($query,$ignore);
	  }
	  if($this->result !== null) {
	    $row = $this->result->fetch_assoc();
        // if($row === null || preg_match('/ limit 1$/i', $query)) {
	    if($row === null) {
		  $this->free();
		}
		return $row;
	  }
	  else {
	    if($this->last_query === $query) {
	      throw new Exception("You are trying to run the same query twice in a row: $query");
		}
		else {
		  throw new Exception("You are trying to fetch rows from an invalid mysqli::result object (Query might be empty).");
		}
	  }
	}

    /**
     * free - Free up used memory associated with mysqli::result object
     *
	 * @return object
     */
	public function free() {
	  if($this->result !== null) {
	    $this->result->free();
	  }
	  $this->result = null;
      return $this;
	}

    /**
     * getNumRows - Returns the number of rows returned from a SELECT statement
	 *  or the number of rows affected by an INSERT, UPDATE, REPLACE or DELETE statement
     *
	 * @return int
     */
	public function getNumRows() {
      return $this->num_rows;
	}

    /**
     * getFoundRows - Returns number of total rows matching query result when SQL_CALC_FOUND_ROWS is used
     *
	 * @return int
     */
	public function getFoundRows() {
	  $row = $this->fetch("SELECT FOUND_ROWS() as FOUND_ROWS");
	  $this->free();
      return $row['FOUND_ROWS'];
	}

    /**
     * getLastId - Returns the id of the last insert statement with an auto_increment column
     *
	 * @return int
     */
	public function getLastId() {
      return $this->insert_id;
	}

    /**
     * real_escape - Escapes user input to safely update / add data to the database
     *
	 * @return int
     */
	public function real_escape($str) {
	  $this->connect();
	  $str = parent::real_escape_string($str);
      return $str;
	}

    /**
     * insert - Insert $row as is into given $table, if $row_escaped is given
	 *  all values will be escaped with mysqli::real_escape_string
     *
	 * @param string $table
	 * @param array $row
	 * @param array $row_escape
	 *
	 * @return bool
     */
	public function insert($table,$row,$row_escape=array(),$ignore=false) {
	  $fields = array();
	  $values = array();
	  foreach($row as $key=>$val) {
	    $fields[] = $key;
		$values[] = $val;
	  }
	  foreach($row_escape as $key=>$val) {
	    $fields[] = $key;
		$values[] = "'" . $this->real_escape($val) . "'";
	  }
	  $fields = '`' . implode('`,`',$fields) . '`';
	  $values = implode(",", $values);
	  return $this->query("INSERT INTO `$table` ($fields) VALUES($values)",$ignore);
	}

    /**
     * update - Update $row as is into given $table based on $where condition, if $row_escaped is given
	 *  all values will be escaped with mysqli::real_escape_string
     *
	 * @param string $table
	 * @param string $where
	 * @param array $row
	 * @param array $row_escape
	 *
	 * @return bool
     */
	public function update($table,$where,$row,$row_escape=array(),$ignore=false) {
	  $field_values = array();
	  foreach($row as $key=>$val) {
	    $field_values[] = "`$key`=$val";
	  }
	  foreach($row_escape as $key=>$val) {
	    $field_values[] = "`$key`='" . $this->real_escape($val) . "'";
	  }
	  return $this->query("UPDATE `$table` SET " . implode(',', $field_values) . " WHERE $where",$ignore);
	}

    /**
     * delete - Delete row(s) from specified $table under $where condition
     *
	 * @param string $table
	 * @param string $where
	 *
	 * @return bool
     */
	public function delete($table,$where,$ignore=false) {
	  return $this->query("DELETE FROM $table WHERE $where",$ignore);
	}

  }
?>
