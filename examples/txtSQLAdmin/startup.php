<?php
// Load up the txtSQL service
define ('TXTSQL_CORE_PATH', $CFG['txtsql']['core_path']);
require('./includes/functions.php');
require($CFG['txtsql']['class']);

// Startup the output buffer, and set our custom error handler
@ob_start();
@set_error_handler('errorHandler');
@session_start();

// Strip slashes from the $_GET and $_POST variables
removeSlashes($_POST);
removeSlashes($_GET);

// Instantiate the txtSQL service and surpress any error messages/warnings
$sql = new txtSQL($CFG['txtsql']['data_path']);
$sql->strict(0);
?>