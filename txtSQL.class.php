<?php
/************************************************************************
* txtSQL                                                 ver. 2.2 Final *
*************************************************************************
* A php class of functions which simulats, and acts almost like a mySQL *
* service                                                               *
*-----------------------------------------------------------------------*
* This program is free software; you can redistribute it and/or         *
* modify it under the terms of the GNU General Public License           *
* as published by the Free Software Foundation; either version 2        *
* of the License, or (at your option) any later version.                *
*                                                                       *
* This program is distributed in the hope that it will be useful,       *
* but WITHOUT ANY WARRANTY; without even the implied warranty of        *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
* GNU General Public License for more details.                          *
*                                                                       *
* You should have received a copy of the GNU General Public License     *
* along with this program; if not, write to the Free Software           *
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307 *
* USA.                                                                  *
*-----------------------------------------------------------------------*
*  NOTE- Tab size in this file: 8 spaces/tab                            *
*-----------------------------------------------------------------------*
*  ©2003 Faraz Ali, ChibiGuy Production [http://txtsql.sourceforge.net] *
*  File: txtsql.core.php                                                *
************************************************************************/
if ( !defined("TXTSQL_CORE_PATH") )
{
	define('TXTSQL_CORE_PATH', './');
}
require_once(TXTSQL_CORE_PATH.'/txtSQL.core.php');

/**
 * Extracts data from a flatfile database via a limited SQL
 *
 * @package txtSQL
 * @author Faraz Ali <Faraz87@comcast.net>
 * @version 2.2 Final
 * @access public
 */
class txtSQL
{
	/**
	 * If set to true, prints all errors and warnings
	 * @var bool
	 * @access public
	 * @see strict()
	 */
	var $_STRICT        = TRUE;

	/**
	 * Holds the path of the txtSQL data directory
	 * @var string
	 * @access private
	 */
	var $_LIBPATH       = NULL;

	/**
	 * Holds the name of the currently logged in user
	 * @var string
	 * @access private
	 * @see _isconnected()
	 */
	var $_USER          = NULL;

	/**
	 * Holds the md5() hash of the password of the currently logged in user
	 * @var string
	 * @access private
	 * @see _isconnected()
	 * @see disconnect()
	 */
	var $_PASS          = NULL;

	/**
	 * Contains a cache of any files that have been read to increase execution time
	 * @var array
	 * @access private
	 * @see readFile()
	 */
	var $_CACHE         = array();

	/**
	 * Holds the name of the currently selected database
	 * @var string
	 * @access private
	 * @see selectdb()
	 */
	var $_SELECTEDDB    = NULL;

	/**
	 * Holds the number of queries sent to txtSQL
	 * @var int
	 * @access private
	 * @see query_count()
	 */
	var $_QUERYCOUNT    = 0;

	/**
	 * The constructor of the txtSQL class
	 * @param string $path The path to which the databases are located
	 * @return void
	 * @access public
	 */
	function txtSQL ($path='./data')
	{
		$this->_LIBPATH = $path;
		return TRUE;
	}

	/**
	 * Connects a user to the txtSQL service
	 * @param string $user The username of the user
	 * @param string $pass The corressponding password of the user
	 * @return void
	 * @access public
	 */
	function connect ($user, $pass)
	{
		/* Check to see if our data exists */
		if ( !is_dir($this->_LIBPATH) )
		{
			$this->_error(E_USER_ERROR, 'Invalid data directory specified');
		}

		/* Instantiate parser and core class */
		$this->_query            = new txtSQLCore;
		$this->_query->_LIBPATH  = $this->_LIBPATH;

		/* Read in the user/pass information */
		if ( ($DATA = $this->_readFile("$this->_LIBPATH/txtsql/txtsql.MYI")) === FALSE )
		{
			$this->_error(E_USER_WARNING, 'Database file is corrupted!');
			return FALSE;
		}
		$this->_data = $DATA;

		/* Check to see if the username exists, and for a matching password */
		if ( !isset($DATA[strtolower($user)]) || $DATA[strtolower($user)] != md5($pass) )
		{
			$this->_error(E_USER_NOTICE, 'Access denied for user \''.$user.'\' (using password: '.(!empty($pass)?'yes':'no').')');
			return FALSE;
		}

		$this->_USER = $user;
		$this->_PASS = $pass;
		return TRUE;
	}

	/**
	 * Disconnects a user from the txtSQL Service
	 * @return void
	 * @access public
	 */
	function disconnect ()
	{
		/* Check to see that we are already connected */
		if( !$this->_isconnected() )
		{
			$this->_error(E_USER_NOTICE, 'Can only disconnect when connected!');
			return FALSE;
		}

		/* Unset user, pass variables
		 * Then remove the core executer object and the parser object
		 * And finally return */
		unset($this->_USER, $this->_PASS, $this->_query);
		return TRUE;
	}

	/**
	 * Selects rows of information from a selected database and a table
	 * that fits the given 'where' clause
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'select', 'where', 'limit'
	 *                         and 'orderby'
	 * @return mixed $results An array that txtSQL returns that matches the given criteria
	 * @access public
	 */
	function select ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->select($arguments);
	}

	/**
	 * Inserts a new row into a table with the given information
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'values'
	 * @return int $inserted The number of rows inserted into the table
	 * @access public
	 */
	function insert ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->insert($arguments);
	}

	/**
	 * Updates a row that matches a 'where' clause, with new information
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'where', 'limit',
	 *                         and 'values'
	 * @return int $inserted The number of rows updated
	 * @access public
	 */
	function update ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->update($arguments);
	}

	/**
	 * Deletes a row from a table that matches a 'where' clause
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'where', 'limit'
	 * @return int $inserted The number of rows deleted
	 * @access public
	 */
	function delete ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->delete($arguments);
	}

	/**
	 * Returns a list containing the current valid txtSQL databases
	 * @return mixed $databases A list containing the databases
	 * @access public
	 */
	function showdbs ()
	{
		/* Check for a connection, and valid arguments */
		$this->_validate(array());
		$this->_QUERYCOUNT++;

		return $this->_query->showdatabases();
	}

	/**
	 * Creates a new database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db'
	 * @return void
	 * @access public
	 */
	function createdb ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->createdatabase($arguments);
	}

	/**
	 * Drops a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db'
	 * @return void
	 * @access public
	 */
	function dropdb ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->dropdatabase($arguments);
	}

	/**
	 * Renames a database
	 * @param mixed $arguments The arguments in form of "[old db name], [new db name]"
	 * @return void
	 * @access public
	 */
	function renamedb ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->renamedatabase($arguments);
	}

	/**
	 * Returns an array containing a list of tables inside of a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db'
	 * @return mixed $tables   An array with a list of tables
	 * @access public
	 */
	function showtables ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->showtables($arguments);
	}

	/**
	 * Creates a new table with the given criteria inside a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'columns'
	 * @return int $deleted The number of rows deleted
	 * @access public
	 */
	function createtable ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->createtable($arguments);
	}

	/**
	 * Drops a table from a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table'
	 * @return void
	 * @access public
	 */
	function droptable ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->droptable($arguments);
	}

	/**
	 * Alters a database by working with its columns
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'action',
	 *                         'name', and 'values'
	 * @return void
	 * @access public
	 */
	function altertable ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->altertable($arguments);
	}

	/**
	 * Returns a description of a table using an array
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table'
	 * @return int $columns An array with the description of a table
	 * @access public
	 */
	function describe ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->describe($arguments);
	}

	/**
	 * Checks for a connection, and valid arguments
	 * @param mixed $arguments The arguments to validify
	 * @return void
	 * @access private
	 */
	function _validate ($arguments)
	{
		/* Check to see user is connected */
		if ( !$this->_isconnected() )
		{
			$this->_error(E_USER_NOTICE, 'Can only perform queries when connected!');
			return FALSE;
		}

		/* Arguments have to be inside of an array */
		if ( !empty($arguments) && !is_array($arguments) )
		{
			$this->_error(E_USER_ERROR, 'txtSQL can only accept arguments in an array');
		}

		return TRUE;
	}

	/**
	 * Evaluates a query with manually inputted arguments.
	 * The $action can be either 'show databases', 'create databases', 'drop database', 'rename database'
	 * 'show tables', 'create table', 'drop table', 'alter table', 'describe', 'select', 'insert', 'delete',
	 * and 'insert'. See the readme for more information.
	 *
	 * @param string $action The command txtSQL is to perform
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 * @return mixed $results The results that txtSQL returned
	 * @access public
	 */
	function execute ($action, $arguments = NULL)
	{
		/* Check to see user is connected */
		if ( !$this->_isconnected() )
		{
			$this->_error(E_USER_NOTICE, 'Can only perform queries when connected!');
			return FALSE;
		}

		/* If there is no action */
		if ( empty($action) )
		{
			$this->_error(E_USER_NOTICE, 'You have an error in your txtSQL query');
			return FALSE;
		}

		/* Arguments have to be inside of an array */
		if ( !empty($arguments) && !is_array($arguments) )
		{
			$this->_error(E_USER_ERROR, 'txtSQL Can only accept arguments in an array');
		}

		/* Depending on what type of action it is, then perform right query */
		switch ( strtolower($action) )
		{
			/* ----- Database Related ----- */
			case 'show databases':
				$results = $this->_query->showdatabases();
				break;
			case 'create database':
				$results = $this->_query->createdatabase($arguments);
				break;
			case 'drop database':
				$results = $this->_query->dropdatabase($arguments);
				break;
			case 'rename database':
				$results = $this->_query->renamedatabase($arguments);
				break;

			/* ----- Table Related ----- */
			case 'show tables':
				$results = $this->_query->showtables($arguments);
				break;
			case 'create table':
				$results = $this->_query->createtable($arguments);
				break;
			case 'drop table':
				$results = $this->_query->droptable($arguments);
				break;
			case 'alter table':
				$results = $this->_query->altertable($arguments);
				break;
			case 'describe':
				$results = $this->_query->describe($arguments);
				break;

			/* ----- Main functions ----- */
			case 'select':
				$results = $this->_query->select($arguments);
				break;
			case 'insert':
				$results = $this->_query->insert($arguments);
				break;
			case 'update':
				$results = $this->_query->update($arguments);
				break;
			case 'delete':
				$results = $this->_query->delete($arguments);
				break;

			default:
				$this->_error(E_USER_NOTICE, 'Unknown action: '.$action);
				return FALSE;
		}

		/* Return whatever results we got back */
		$this->_QUERYCOUNT++;
		return isset($results) ? $results : '';
	}

	/**
	 * Turns strict property of txtSQL off/on
	 * @param bool $strict The value of the strict property
	 * @return void
	 * @access public
	 */
	function strict ($strict = FALSE)
	{
		$strict        = (bool) $strict;
		$this->_STRICT = $strict;

		if ( $this->_isconnected() )
		{
			$this->_query->strict($strict);
		}
		return TRUE;
	}

	/**
	 * To set username and/or passwords, or create/delete users
	 * @param string $action The action to perform (add, drop, edit)
	 * @param string $user The username to be added/modified
	 * @param string $pass The password of the username
	 * @param string $pass1 The new password of the username (optional if editing)
	 * @return void
	 * @access public
	 */
	function grant_permissions($action, $user, $pass = NULL, $pass1 = NULL)
	{
		/* Are we connected? */
		if ( !$this->_isconnected() )
		{
			$this->_error(E_USER_NOTICE, 'Not connected');
			return FALSE;
		}

		/* Can only work with strings */
		if ( !is_string($action) || !is_string($user) || (!empty($pass) && !is_string($pass)) || (!empty($pass1) && !is_string($pass1)) )
		{
			$this->_error(E_USER_NOTICE, 'The arguments must be a string');
			return FALSE;
		}

		/* Read in user database */
		if ( ($DATA = $this->_readFile("$this->_LIBPATH/txtsql/txtsql.MYI")) === FALSE )
		{
			$this->_error(E_USER_WARNING, 'Database file is corrupted!');
			return FALSE;
		}

		/* Need a username */
		if ( empty($user) )
		{
			$this->_error(E_USER_NOTICE, 'Forgot to input username');
			return FALSE;
		}

		/* Perform the correct operation */
		switch ( strtolower($action) )
		{
			case 'add':
				if ( isset($DATA[strtolower($user)]) )
				{
					$this->_error(E_USER_NOTICE, 'User already exists');
					return FALSE;
				}
				$DATA[strtolower($user)] = md5($pass);
				break;
			case 'drop':
				if ( strtolower($user) == strtolower($this->_USER) )
				{
					$this->_error(E_USER_NOTICE, 'Can\'t drop yourself');
					return FALSE;
				}
				elseif ( strtolower($user) == 'root' )
				{
					$this->_error(E_USER_NOTICE, 'Can\'t drop user root');
					return FALSE;
				}
				elseif ( !isset($DATA[strtolower($user)]) )
				{
					$this->_error(E_USER_NOTICE, 'User doesn\'t exist');
					return FALSE;
				}
				elseif ( md5($pass) != $DATA[strtolower($user)] )
				{
					$this->_error(E_USER_NOTICE, 'Incorrect password');
					return FALSE;
				}
				unset($DATA[strtolower($user)]);
				break;
			case 'edit':
				if ( !isset($DATA[strtolower($user)]) )
				{
					$this->_error(E_USER_NOTICE, 'User doesn\'t exist');
					return FALSE;
				}
				if ( md5($pass) != $DATA[strtolower($user)] )
				{
					$this->_error(E_USER_NOTICE, 'Incorrect password');
					return FALSE;
				}
				$DATA[strtolower($user)] = md5($pass1);
				break;
			default: $this->_error(E_USER_NOTICE, 'Invalid action specified');
			         return FALSE;
		}

		/* Save the new information */
		$fp = @fopen("$this->_LIBPATH/txtsql/txtsql.MYI", 'w') or $this->_error(E_USER_FATAL,  "Couldn't open $this->_LIBPATH/txtsql/txtsql.MYI for writing");
		      @flock($fp, LOCK_EX);
		      @fwrite($fp, serialize($DATA))                   or $this->_error(E_USER_FATAL,  "Couldn't write to $this->_LIBPATH/txtsql/txtsql.MYI");
		      @flock($fp, LOCK_UN);
		      @fclose($fp)                                     or $this->_error(E_USER_NOTICE, "Error closing $this->_LIBPATH/txtsql/txtsql.MYI");

		/* Save it in the cache */
		$this->_CACHE["$this->_LIBPATH/txtsql/txtsql.MYI"] = $DATA;
		return TRUE;
	}

	/**
	 * Returns an array filled with a list of current txtSQL users
	 * @return mixed $users
	 * @access public
	 */
	function getUsers ()
	{
		/* Are we connected? */
		if ( !$this->_isconnected() )
		{
			$this->_error(E_USER_NOTICE, 'Not connected');
			return FALSE;
		}

		/* Read in user database */
		if ( ($DATA = $this->_readFile("$this->_LIBPATH/txtsql/txtsql.MYI")) === FALSE )
		{
			$this->_error(E_USER_WARNING, 'Database file is corrupted!');
			return FALSE;
		}

		$users = array();
		foreach ( $DATA as $key => $value )
		{
			$users[] = $key;
		}
		return $users;
	}

	/**
	 * Check whether a database is locked or not
	 * @param string $db The database to check
	 * @return bool $locked Whether it is locked or not
	 * @access public
	 */
	function isLocked ($db)
	{
		if ( !$this->_dbexist($db) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$db.' doesn\'t exist');
			return FALSE;
		}
		return is_file("$this->_LIBPATH/$db/txtsql.lock") ? TRUE : FALSE;
	}

	/**
	 * To put a file lock on the database
	 * @param string $db The database to have a file lock placed on
	 * @return void
	 * @access public
	 */
	function lockdb ($db)
	{
		/* Make sure that the user is connected */
		if ( !$this->_isConnected() )
		{
			$this->_error(E_USER_NOTICE, 'You must be connected');
			return FALSE;
		}
		elseif ( $this->isLocked($db) )
		{
			$this->_error(E_USER_NOTICE, 'Lock for database '.$db.' already exists');
			return FALSE;
		}

		$fp = fopen("$this->_LIBPATH/$db/txtsql.lock", 'a') or $this->_error(E_USER_ERROR, 'Err1or creating a lock for database '.$db);
		      fclose($fp) or $this->_error(E_USER_ERROR, 'Error creating a lock for database '.$db);

		return TRUE;
	}

	/**
	 * To remove a file lock from the database
	 * @param string $db The database to have a file lock removed from
	 * @return void
	 * @access public
	 */
	function unlockdb ($db)
	{
		/* Make sure that the user is connected */
		if ( !$this->_isConnected() )
		{
			$this->_error(E_USER_NOTICE, 'You must be connected');
			return FALSE;
		}
		elseif ( !$this->isLocked($db) )
		{
			$this->_error(E_USER_NOTICE, 'Lock for database '.$db.' doesn\'t exist');
			return FALSE;
		}

		if ( !@unlink("$this->_LIBPATH/$db/txtsql.lock") )
		{
			$this->_error(E_USER_ERROR, 'Error removing lock for database '.$db);
		}
		return TRUE;
	}

	/**
	 * To select a database for txtsql to use as a default
	 * @param string $db The name of the database that is to be selected
	 * @return void
	 * @access public
	 */
	function selectdb ($db)
	{
		/* Valid db name? */
		if ( empty($db) )
		{
			$this->_error(E_USER_NOTICE, 'Cannot select database '.$db);
			return FALSE;
		}

		/* Does it exist? */
		if ( !$this->_dbexist($db) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$db.' doesn\'t exist');
			return FALSE;
		}

		/* Select the database */
		$this->_SELECTEDDB = $db;
		$this->_query->_SELECTEDDB = $db;
		return TRUE;
	}

	/**
	 * An alias (but public) of the private function _tableexist()
	 * @param $table Table to be checked for existence
	 * @param $db The database the table is in
	 * @return bool Whether it exists or not
	 */
	function table_exists ($table, $db)
	{
		return $this->_tableexist($table, $db);
	}

	/**
	 * An alias (public) of the private function _dbexist()
	 * @param $table DB to be checked for existence
	 * @return bool Whether it exists or not
	 */
	function db_exists ($db)
	{
		return $this->_dbexist($db);
	}

	/**
	 * To retrieve the number of records inside of a table
	 * @param string $table The name of the table
	 * @param string $database The database the table is inside of (optional)
	 * @return int $count The number of records in the table
	 * @access public
	 */
	function table_count ($table, $database=NULL)
	{
		/* Inside of another database? */
		if ( !empty($database) )
		{
			if ( !$this->selectdb($database) )
			{
				return FALSE;
			}
		}

		/* No database or no table specified means that we stop here */
		if ( empty($this->_SELECTEDDB) || empty($table) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}

		/* Does table exist? */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table";
		if ( !is_file($filename.'.MYD') || !is_file($filename.'.FRM') )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$table.' doesn\'t exist');
			return FALSE;
		}

		/* Read in the table's records */
		if ( ($rows = @file($filename.'.MYD')) === FALSE )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$table.' doesn\'t exist');
			return FALSE;
		}
		$count = substr($rows[0], 2, strpos($rows[0], '{') - 3);

		/* Return the count */
		return $count;
	}

	/**
	 * To retrieve the last ID generated by an auto_increment field in a table
	 * @param string $table The name of the table
	 * @param string $db The database the table is inside of (optional)
	 * @return string $column Get the last ID generated by this column instead of the priamry key (optional)
	 * @access public
	 */
	function last_insert_id( $table, $db = '', $column = '' )
	{
		/* Select a database if one is given */
		if ( !empty($db) )
		{
			if ( !$this->selectdb($db) )
			{
				return FALSE;
			}
		}

		/* Check for a selected database */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}

		/* Read in the column definitions */
		if ( ( $cols = $this->_readFile("$this->_LIBPATH/$this->_SELECTEDDB/$table.FRM") ) === FALSE )
		{
			$this->_error(E_USER_NOTICE, 'Table "'.$table.'" doesn\'t exist');
			return FALSE;
		}

		/* Check for a valid column that is auto_increment */
		if ( !empty($column) )
		{
			if ( $this->_getColPos($column, $cols) === FALSE )
			{
				$this->_error(E_USER_NOTICE, 'Column '.$column.' doesn\'t exist');
				return FALSE;
			}
			elseif ( $cols[$column]['auto_increment'] != 1 )
			{
				$this->_error(E_USER_NOTICE, 'Column '.$column.' is not an auto_increment field');
				return FALSE;
			}

			$cols['primary'] = $column;
		}

		/* If we are using the primary key, make sure it exists */
		elseif ( empty($cols['primary']) && empty($column) )
		{
			$this->_error(E_USER_NOTICE, 'There is no primary key defined for table "'.$table.'"');
			return FALSE;
		}

		return $cols[$cols['primary']]['autocount'];
	}

	/**
	 * To return the number of queries sent to txtSQL
	 * @return int $_QUERYCOUNT
	 * @access public
	 */
	function query_count()
	{
		return $this->_QUERYCOUNT;
	}

	/**
	 * To print the last error that occurred
	 * @return void
	 * @access public
	 */
	function last_error()
	{
		if ( !empty($this->_query->_ERRORS) )
		{
			print '<pre>'.$this->_query->_ERRORSPLAIN[count($this->_query->_ERRORS)-1].'</pre>';
		}
		elseif ( !empty($this->_ERRORS) )
		{
			print '<pre>'.$this->_ERRORSPLAIN[count($this->_ERRORS)-1].'</pre>';
		}
	}

	/**
	 * To return the last error that occurred
	 * @return string $error The last error
	 * @access public
	 */
	function get_last_error()
	{
		if ( !empty($this->_query->_ERRORS) )
		{
			return $this->_query->_ERRORSPLAIN[count($this->_query->_ERRORS)-1];
		}
		elseif ( !empty($this->_ERRORS) )
		{
			return $this->_ERRORSPLAIN[count($this->_ERRORS)-1];
		}		
	}

	/**
	 * To print any errors that occurred during script execution so far
	 * @return void
	 * @access public
	 */
	function errordump()
	{
		/* No errors? */
		if ( empty($this->_ERRORS) && empty($this->_query->_ERRORS) )
		{
			echo 'No errors occurred during script execution';
			return TRUE;
		}

		/* Errors during this part of script */
		if ( !empty($this->_ERRORS) )
		{
			foreach ( $this->_ERRORS as $key => $value )
			{
				echo 'ERROR #['.$key.'] '.$value;
			}
		}

		/* Errors during query execution portion */
		elseif ( !empty($this->_query->_ERRORS) )
		{
			foreach ( $this->_query->_ERRORS as $key => $value )
			{
				echo 'ERROR #['.$key.'] '.$value;
			}
		}

		return TRUE;
	}

	/**
	 * Removes any cache that is being stored
	 * @return void
	 * @access public
	 */
	function emptyCache()
	{
		$this->_CACHE = array();
		return TRUE;
	}

	// PRIVATE FUNCTIONS //////////////////////////////////////////////////////////////////////////////////////
	/**
	 * To retrieve the number of records inside of a table
	 * @param int $errno The error type (number form)
	 * @param string $errstr The error message that will be shown
	 * @param string $errtype Prints this string before the message
	 * @return void
	 * @access private
	 */
	function _error ($errno, $errstr, $errtype=NULL)
	{
		/* If this error is not an internal error, then generate a backtrace
		 * to the line that originally caused the error */
		$backtrace = array_reverse(@debug_backtrace());
		$errfile   = $backtrace[0]['file'];
		$errline   = $backtrace[0]['line'];

		/* Determine what kind of error this is, so we can display it. */
		switch ($errno)
		{
			case E_USER_ERROR:
				$type = 'Fatal Error';
				break;
			case E_USER_NOTICE:
				$type = "Warning";
				break;
			default:
				$type = "Error";
				break;
		}
		$type = isset($errtype) ? $errtype : $type;

		/* Print the message to the screen, if strict is on */
		$this->_ERRORSPLAIN[] = $errstr;
		$errormsg = "<BR />\n<B>txtSQL $type:</B> $errstr in <B>$errfile</B> on line <B>$errline</B>\n<BR /></DIV>";
		$this->_ERRORS[] = $errormsg;
		if ( $this->_STRICT === TRUE )
		{
			echo $errormsg;
		}

		/* If this is a fatal error, then we are forced to exit and stop execution */
		if ( $errno == E_USER_ERROR )
		{
			exit;
		}
		return TRUE;
	}

	/**
	 * To Read a file into a string and return it
	 * @param string $filename The path to the file needed to be opened
	 * @param bool $useCache Whether to save/retrieve this file from a cache
	 * @param bool $unserialize Whether to unserialize the string or not
	 * @return string $contents The file's contents
	 * @access private
	 */
	function _readFile ( $filename, $useCache = TRUE, $unserialize = TRUE )
	{
		if ( is_file($filename) )
		{
			if ( $useCache === TRUE )
			{
				if ( isset($this->_CACHE[$filename]) )
				{
					return $this->_CACHE[$filename];
				}
			}

			if ( ( $contents = @implode('', @file($filename)) ) !== FALSE )
			{
				if ( $unserialize === TRUE )
				{
					if ( ( $contents = @unserialize($contents) ) === FALSE )
					{
						return FALSE;
					}
				}

				if ( $useCache === TRUE )
				{
					$this->_CACHE[$filename] = $contents;
				}
				return $contents;
			}
		}
		return FALSE;
	}

	/**
	 * Check to see whether a user is connected or not
	 * @return bool $connected Whether the user is connected or not
	 * @access private
	 */
	function _isconnected ()
	{
		/* If either one of the user or pass vars are empty, then return false; */
		if ( empty($this->_USER) )
		{
			return FALSE;
		}

		/* Are we authenticated? */
		if ( $this->_data[strtolower($this->_USER)] != md5($this->_PASS) )
		{
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * To check whether a database exists or not
	 * @param string $db The name of the database
	 * @return bool Whether the db exists or not
	 * @access private
	 */
	function _dbexist ($db)
	{
		return is_dir("$this->_LIBPATH/$db") ? TRUE : FALSE;
	}

	/**
	 * To check whether a table exists or not
	 * @param string $table The name of the table
	 * @param string $db The name of the database the table is in
	 * @return bool Whether the db exists or not
	 * @access private
	 */
	function _tableexist ($table, $db)
	{
		/* Check to see if the database exists */
		if ( !empty($db) )
		{
			if ( !$this->selectdb($db) )
			{
				$this->_error(E_USER_NOTICE, 'Database, \''.$db.'\', doesn\'t exist');
				return FALSE;
			}
		}

		/* Check to see if the table exists */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table";

		if ( is_file($filename.'.MYD') && is_file($filename.'.FRM') )
		{
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * To build an if-statement which can be used to see if a row
	 * fits the given credentials
	 * @param mixed $where The array containing the where clause
	 * @param mixed $cols The array containing the column definitions
	 * @return string $query The string which contains the php-equivelent to the where clause
	 * @access private
	 */
	function _buildIf ($where, $cols)
	{
		/* We can only work with a string containing where */
		if ( !is_array($where) || empty($where) )
		{
			$this->_error(E_USER_NOTICE, 'Where clause must be an array');
			return FALSE;
		}
		$query = '';

		/* Start creating the query */
		foreach ( $where as $key => $value )
		{
			/* Are we on an 'and|or'? */
			if ( $key % 2 == 1 )
			{
				/* Check for a valid "and|or" */
				$and = strtolower($value) == 'and';
				$or  = strtolower($value) == 'or';
				$xor = strtolower($value) == 'xor';
				if ( $and === FALSE && $or === FALSE && $xor === FALSE )
				{
					$this->_error(E_USER_NOTICE, 'Only boolean seperators AND, and OR are allowed');
					return FALSE;
				}
				$query .= ( $and === TRUE ) ? ' && ' : ( ( $xor === TRUE ) ? ' XOR ' : ' || ' );
				continue;
			}

			/* Find out which operator we are going to use to create the if
			 * NOTE: I'm pretty sure the order in which these operators are checked
			 *       are correct. If anyone notices a bug in the order, let me know*/
			$f1 = '(';
			$f2 = ') ';
			switch ( TRUE )
			{
				case strpos($value, '!='): $type = 1; $op = '!='; break;
				case strpos($value, '!~'): $type = 3; $op = '!~'; break;
				case strpos($value, '=~'): $type = 3; $op = '=~'; break;
				case strpos($value, '<='): $type = 2; $op = '<='; break;
				case strpos($value, '>='): $type = 2; $op = '>='; break;
				case strpos($value, '=' ): $type = 1; $op = '=';  break;
				case strpos($value, '<>'): $type = 1; $op = '<>'; break;
				case strpos($value, '<' ): $type = 2; $op = '<';  break;
				case strpos($value, '>' ): $type = 2; $op = '>';  break;
				case strpos($value, '!?'): $type = 5; $op = '!?'; break;
				case strpos($value, '?' ): $type = 5; $op = '?';  break;
				default:
					/* Check for a valid function that requires no operator */
					$val = 'TRUE';
					if ( substr(trim($value), 0, 1) == '!' )
					{
						$val   = 'FALSE';
						$value = substr($value, strpos($value, '!')+1);
					}

					$function = substr($value, 0, strpos($value, '('));
					$col      = substr($value, strlen($function) + 1, strlen($value) - strlen($function) - 2 );

					if ( $function !== FALSE )
					{
						$type = 4;
						$op   = '===';
						switch ( strtolower($function) )
						{
							case 'isnumeric':  $f1 = 'is_numeric('; break 2;
							case 'isstring':   $f1 = 'is_string('; break 2;
							case 'isfile':     $f1 = 'is_file('; break 2;
							case 'isdir':      $f1 = 'is_dir('; break 2;
							case 'iswritable': $f1 = 'is_writable(';  break 2;
						}
					}

					/* There is an error in your where clause */
					$this->_error(E_USER_NOTICE, 'You have an error in your where clause, (operators allowed: =, !=, <>, =~, !~, <, >, <=, >=)'); return FALSE;
			}

			/* Split string by the proper operator, as long as there is an operator */
			if ( !isset($function) )
			{
				list ( $col, $val ) = explode($op, $value, 2);
			}

			/* Check to see if we are utilizing a function */
			if ( substr_count($col, '(') == 1 && substr_count($col, ')') == 1 )
			{
				$function = substr($col, 0, strpos($col, '('));

				if ( $val != '' && $col{strlen($col)-1}.$val{0} == "  " )
				{
					$col  = substr($col, strlen($function) + 1, strlen($col) - strlen($function) - ( ($col{strlen($col)-1} != " " ) ? 2 : 3 ) )." ";
					$val  = $val;
				}
				else
				{
					$col = substr($col, strlen($function) + 1, strlen($col) - strlen($function) - ( ($col{strlen($col)-1} != " " ) ? 2 : 3 ) );
				}

				/* Check for a valid function call */
				switch ( strtolower($function) )
				{
					case 'strlower':   $f1 = 'strtolower(';         break;
					case 'strupper':   $f1 = 'strtoupper(';         break;
					case 'chop':
					case 'rtrim':      $f1 = 'rtrim(';              break;
					case 'ltrim':      $f1 = 'ltrim(';              break;
					case 'trim':       $f1 = 'trim(';               break;
					case 'md5':        $f1 = 'md5(';                break;
					case 'stripslash': $f1 = 'stripslashes(';       break;
					case 'strlength':  $f1 = 'strlen(';             break;
					case 'strreverse': $f1 = 'strrev(';             break;
					case 'ucfirst':    $f1 = 'ucfirst(';            break;
					case 'ucwords':    $f1 = 'ucwords(';            break;
					case 'bin2hex':    $f1 = 'bin2hex(';            break;
					case 'entdecode':  $f1 = 'html_entity_decode('; break;
					case 'entencode':  $f1 = 'htmlentities(';       break;
					case 'soundex':    $f1 = 'soundex(';            break;
					case 'ceil':       $f1 = 'ceil(';               break;
					case 'floor':      $f1 = 'floor(';              break;
					case 'round':      $f1 = 'round(';              break;

					/* These are functions that should NOT have an operator */
					case 'isnumeric':
					case 'isstring':
					case 'isfile':
					case 'isdir':
						$this->_error(E_USER_NOTICE, 'Function, '.$function.', requires that NO operator be present in the clause');
						return FALSE;

					default:
						$this->_error(E_USER_NOTICE, 'Function, '.$function.', hasn\'t been implemented');
						return FALSE;
				}
			}

			/* What if the column name is primary? */
			if ( strtolower(trim($col)) == 'primary' )
			{
				/* Make sure there is a primary key */
				if ( empty($cols['primary']) )
				{
					$this->_error(E_USER_NOTICE, 'No primary key has been assigned to this table');
					return FALSE;
				}
				$col = $cols['primary'];
			}

			/* Does the specified column exist? */
			if ( ( $position = $this->_getColPos(rtrim($col), $cols) ) === FALSE )
			{
				$this->_error(E_USER_NOTICE, 'Column \''.rtrim($col).'\' doesn\'t exist');
				return FALSE;
			}

			/* Create/Add-To the queries */
			$val = str_replace("\'", "'", addslashes($val));
			$val = ( $col{strlen($col)-1}.$val{0} == "  " ) ? substr($val, 1) : $val;

			if ( empty($val) && ( $type == '5' || $f1 != '(' ) )
			{
				$this->_error(E_USER_NOTICE, 'Forgot to specify a value to match in your where clause');
				return FALSE;
			}

			switch ( $type )
			{
				/* Test for equality */
				case 1:
				case 2: $quotes = ( !is_numeric($val) || $cols[rtrim($col)]['type'] != 'int' ) ? '"' : '';
					$query .= ' ( '.$f1.'$value['.$position.']'.$f2.' '.( $op == '=' ? '==' : $op ).' '.$quotes.$val.$quotes.' ) ';
					break;

				/* Test using regex, with[out] a function */
				case 3:	$val    = str_replace(array('(',   ')',  '{',  '}', '.',  '$',  '/',       '\%',  '*',     '%', '$$PERC$$'),
					                      array('\(', '\)', '\{', '\}', '\.', '\$', '\/', '$$PERC$$', '\*', '(.+)?',       '%'), $val);
					$query .= ' ( '.($op == '!~' ? '!' : '').'preg_match("/^'.$val.'$/iU", '.$f1.'$value['.$position.']'.$f2.') ) ';
					break;

				/* Test involving a function */
				case 4: $query .= ' ( '.$f1.'$value['.$position.']'.$f2.' === '.$val.' ) ';
				        break;

				/* Test involving a strpos with[out] function */
				case 5: $query .= ' ( strpos('.$f1.'\' \'.$value['.$position.']), \''.$val.'\') '.(($op == '!?') ? '=' : '!' ).'== FALSE ) ';
			}
			unset($function, $f1, $f2, $quotes, $position, $val, $col, $op);
		}

		/* Make sure that we have a valid query ending */
		$andor = substr($query, -3, -1);
		if ( $andor == '&&' || $andor == '||' || $andor == 'OR' )
		{
			$this->_error(E_USER_NOTICE, 'You have an error in your where clause, cannot end statement with an AND, OR, or XOR');
			return FALSE;
		}
		return $query;
	}

	/**
	 * To retrieve the index of the column from the columns' array
	 * @param string $colname The name of the column to be searched for
	 * @param mixed $cols The column definitions array
	 * @return int $position The index of the column in the array
	 * @access private
	 */
	function _getColPos ($colname, $cols)
	{
		/* Make sure array is not empty, and the parameter is an array */
		if ( empty($cols) || !is_array($cols) || !array_key_exists($colname, $cols) )
		{
			return FALSE;
		}
		unset($cols['primary']);

		/* Get the index for the column */
		if ( ( $position = array_search($colname, array_keys($cols)) ) === FALSE )
		{
			return FALSE;
		}
		return $position;
	}

	/**
	 * To sort a multi-dimensional array by a key
	 * @author fmmarzoa@gmx.net <fmmarzoa@gmx.net>
	 * @param mixed $array The array to be sorted
	 * @param string $num The name of the key to sort the array by
	 * @return string $order Either a 'ASC' or 'DESC' for sorting order
	 * @access private
	 */
	function _qsort($array, $num = 0, $order = "ASC", $left = 0, $right = -1)
	{
		if ( count($array) >= 1 )
		{
			if ( $right == -1 )
			{
				$right = count($array) - 1;
			}

			$links  = $left;
			$rechts = $right;
			$mitte  = $array[($left + $right) / 2][$num];
			if ( $rechts > $links )
			{

				do {
					if ( strtolower($order) == 'asc' )
					{
						while ( $array[$links][$num] < $mitte )
						{
							$links++;
						}
						while ( $array[$rechts][$num] > $mitte )
						{
							$rechts--;
						}
					}
					else
					{
						while ( $array[$links][$num] > $mitte )
						{
							$links++;
						}
						while ( $array[$rechts][$num] < $mitte)
						{
							$rechts--;
						}
					}

					if ( $links <= $rechts )
					{
						$tmp              = $array[$links];
						$array[$links++]  = $array[$rechts];
						$array[$rechts--] = $tmp;
					}

				}
				while ( $links <= $rechts );

				if ( $left < $rechts )
				{
					$array = $this->_qsort($array,$num,$order,$left, $rechts);
				}
				if ( $links < $right )
				{
					$array = $this->_qsort($array,$num,$order,$links,$right);
				}
			}
			return $array;
		}
		return FALSE;
	}

	/**
	 * Does what unique_array() does but with multidimensional arrays
	 * @param mixed $array The array that will be filtered
	 * @param string $sub_key The $key that will be examined for duplicates
	 */
	function unique_multi_array ( $array, $sub_key )
	{
		$target                  = array();
		$existing_sub_key_values = array();

		foreach ( $array as $key => $sub_array )
		{
			if ( !in_array($sub_array[$sub_key], $existing_sub_key_values) )
			{
				$existing_sub_key_values[] = $sub_array[$sub_key];
				$target[$key]              = $sub_array;
			}
		}
		return $target;
	}

	/**
	 * Returns the current txtSQL version
	 * @return string $version The current version of txtSQL
	 * @access public
	 */
	function version()
	{
		return '2.2 Final';
	}
}
?>