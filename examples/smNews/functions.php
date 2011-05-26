<?php
// Startup the output buffer, and set our custom error handler
ob_start();
set_error_handler('errorHandler');

// Define the function to handle our errors
function errorHandler ( $errno, $errstr, $errfile, $errline )
{
	switch ( $errno )
	{
		case E_USER_ERROR:   ob_end_clean();
				     echo "<b>Fatal Error occurred</b>- $errstr<br />\n";
				     exit(1);

		case E_USER_WARNING: echo "<b>ERROR</b> $errstr<br />\n";
				     break;

		case E_USER_NOTICE:  echo "<b>WARNING</b> $errstr<br />\n";
				     break;
	}
	return TRUE;
}

// Shortcut for defining errors
function showError ( $errstr, $errtype, $explanation = '' )
{
	trigger_error(sprintf($errstr, $explanation), $errtype);
	return TRUE;
}

// Execute a txtSQL function and return the results
function execute ( $action, $arg = array() )
{
	global $sql, $LANG;
	if ( ( $results = $sql->execute($action, $arg) ) === FALSE )
	{
		showError($LANG['txtsql']['queryError'], E_USER_ERROR, $sql->get_last_error());
	}
	return $results;
}
?>