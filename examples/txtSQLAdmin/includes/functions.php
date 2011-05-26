<?php
// Define the function to handle our errors
function errorHandler ( $errno, $errstr, $errfile, $errline )
{
	switch ( $errno )
	{
		case E_USER_ERROR:   ob_end_clean();
				     include('./includes/header.php');
				     echo "<b>Fatal Error occurred</b><br /> $errstr<br />\n";
				     echo "<a href=\"javascript:history.back()\">Go Back</a>\n";
				     exit(1);

		case E_USER_WARNING: echo "<br/><br /><b>ERROR</b><br /> $errstr<br />\n";
				     break;

		case E_USER_NOTICE:  echo "<br/><br /><b>WARNING</b><br />$errstr<br />\n";
				     break;
	}
	return TRUE;
}

// Shortcut for defining errors
function error ( $errstr, $errtype, $explanation = '' )
{
	trigger_error(sprintf($errstr, $explanation), $errtype);
	return TRUE;
}

// Execute a txtSQL function and return the results
function execute ( $action, $arg = array() )
{
	global $sql, $CFG;

	if ( ( $results = $sql->execute($action, $arg) ) === FALSE )
	{
		error('Error occurred while attempting to access txtSQL: <pre>%s</pre>', E_USER_ERROR, $sql->get_last_error());
	}

	return $results;
}

// Sets a default value to a variable
function setDefault ( $var, $default = '' )
{
	eval('
		$var = !empty('.$var.') ? '.$var.' : $default;
		'.$var.' = $var;
		return '.$var.';
	');
}

// Removes all slashes from an arary
function removeSlashes ( &$array )
{
	foreach ( $array as $key => $value )
	{
		if ( is_array($value) )
		{
			removeSlashes($value);
		}
		elseif ( is_string($value) )
		{
			$array[$key] = stripslashes(stripslashes($value));
		}
	}

	return $array;
}

// Cuts a string off at $maxlength
function cutString ( $string, $maxlength )
{
	$copy = $string;

	if ( strlen($string) > $maxlength )
	{
		$string = substr($string, 0, $maxlength);

		if ( strpos($copy, ' ') )
		{
			$string = substr($string, 0, strrpos($string, ' '));
		}
		$string .= '...';
	}

	return $string;
}

// Returns the microtime
function getTime ()
{
	$microtime = explode(' ', microtime());
	return $microtime[1] . substr($microtime[0], 1);
}

// Constructs the javascript array for holding the serialized 'WHERE' clause
function constructWhere ( $rows, $cols, $indent = 6)
{
	for ( $i = 1; $i <= $indent; $i++ )
	{
		$tabs .= "\t";
	}

	$script  = "{$tabs}<script>\n";
	$script .= "{$tabs}var whereClauses = new Array(".( count($rows) ).");\n";

	foreach ( $rows as $key => $value )
	{
		if ( !empty($cols['primary']) )
		{
			$script .= "{$tabs}whereClauses[$key] = \"";
			$script .= str_replace('"', '\"', serialize(array("{$cols['primary']} = {$value[$cols['primary']]}")));
			$script .= "\";\n";
		}
		else
		{
			$whereClause = array();
			foreach ( $value as $key1 => $value1 )
			{
				$whereClause[] = "$key1 = $value1";
				$whereClause[] = 'AND';
			}
			$whereClause = array_splice($whereClause, 0, count($whereClause) - 1);

			$script .= "{$tabs}whereClauses[$key] = \"";
			$script .= str_replace(array('\\', '"', "\r", "\n"), array('\\\\', '\"', '', '\n'), serialize($whereClause));
			$script .= "\";\n";
		}
	}

	$script .= "$tabs</script>\n";

	return $script;
}
?>