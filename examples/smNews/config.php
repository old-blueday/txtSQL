<?php
$sqlPath    = '../..';
$sqlData    = '../../data';
$sqlUser    = 'root';
$sqlPass    = '';
$sqlDB      = 'smnews';
define('TXTSQL_CORE_PATH', $sqlPath);

$tpl        = 'default';
$lang       = 'en';
$maxShow    = 10;
$maxShowH   = 10;
$postsOrder = 1;
$dateFormat = 'F d, Y g:i';

// END OF CONFIGURATION
// ===================================================================
require_once('./functions.php');
require_once('./languages/'.$lang.'.php');
require_once('./templates/'.$tpl.'.php');
require_once(TXTSQL_CORE_PATH.'/txtSQL.class.php');

$sql = new txtSQL($sqlData);
$sql->strict(0);
$sql->connect($sqlUser, $sqlPass) or showError($LANG['txtsql']['connectError'], E_USER_ERROR, $sql->get_last_error());
$sql->selectdb($sqlDB) or showError($LANG['txtsql']['dbNotFound'], E_USER_ERROR, $sql->get_last_error());?>