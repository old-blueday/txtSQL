<html>
<head>
<title>Install smNews</title>
<style>
td, body,
input    { font-family: Tahoma; font-size: 11px; color: black }
a        { color: darkblue; font-weight: bold; text-decoration: none; }
a:hover  { text-decoration: underline; }
</style>
</head>
<body bgcolor="DADADA">

<?php
if ( empty($_GET['step']) || !is_numeric($_GET['step']) )
{
	echo "<form method=post action=\"install.php?step=2\">\n";
	echo "<table width=\"500\" cellspacing=\"1\" bgcolor=\"#FAFAFA\" cellpadding=\"3\" align=\"center\">\n";
	echo "<tr><td colspan=\"2\" align=\"center\" bgcolor=\"#DADADA\"><b>Installing smNews</b></td></tr>\n";
	echo "<tr><td colspan=\"2\" height=\"1\" bgcolor=\"#FFFFFF\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td><font color=\"red\">Path to folder with txtSQL scripts (no trailing slash)</font></td><td><input type=\"text\" name=\"txtsql_path\" value=\"../..\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td><font color=\"red\">Path to txtSQL data folder (no trailing slash)</font></td><td><input type=\"text\" name=\"data_path\" value=\"../../data\"></td></tr>\n";	
	echo "<tr bgcolor=\"#FFFFFF\"><td><font color=\"red\">txtSQL Username </font></td><td><input type=\"text\" name=\"username\" value=\"root\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td><font color=\"red\">txtSQL Password </font></td><td><input type=\"password\" name=\"password\" value=\"\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td><font color=\"red\">txtSQL Database smNews uses </font></td><td><input type=\"text\" name=\"dbname\" value=\"smnews\"></td></tr>\n";
	echo "<tr><td colspan=\"2\" align=\"center\" bgcolor=\"#FFFFFF\"><input type=\"submit\" value=\"Step 2\"></td></tr>\n";
	echo "</table></form>";
}
elseif ( $_GET['step'] == 2 )
{
	if ( empty($_POST['txtsql_path']) || !is_file($_POST['txtsql_path'].'/txtSQL.class.php') || 
	     empty($_POST['data_path'])   || !is_dir($_POST['data_path']) )
	{
		$word = ( empty($_POST['data_path']) || !is_dir($_POST['data_path']) ) ? 'data' : '';
		echo "<center><b><font color=\"red\">Invalid txtSQL ".$word." folder specified</font><br>\n";
		echo "<a href=\"javascript:history.back()\">Go Back</a></b></center>";
		exit;
	}
	define('TXTSQL_CORE_PATH', $_POST['txtsql_path']);
	require(TXTSQL_CORE_PATH.'/txtSQL.class.php');

	$sql = new txtSQL($_POST['data_path']);
	$sql->strict(0);
	if ( $sql->connect($_POST['username'], $_POST['password']) === FALSE )
	{
		echo "<center><b><font color=\"red\">Invalid username/password specified, txtSQL said: </font><pre>";
		echo $sql->get_last_error();
		echo "</pre>\n<a href=\"javascript:history.back()\">Go Back</a></b></center>";
		exit;
	}
	elseif ( empty($_POST['dbname']) || $sql->selectdb($_POST['dbname']) === FALSE )
	{
		echo "<center><b><font color=\"red\">Invalid database specified</font><br>\n";
		echo "<a href=\"javascript:history.back()\">Go Back</a></b></center>";
		exit;		
	}

	echo "<form method=\"post\" action=\"install.php?step=3&txtsql_path={$_POST['txtsql_path']}&data_path={$_POST['data_path']}&dbname={$_POST['dbname']}&username={$_POST['username']}&password={$_POST['password']}\">\n";
	echo "<table width=\"500\" cellspacing=\"1\" bgcolor=\"#FAFAFA\" cellpadding=\"3\" align=\"center\">\n";
	echo "<tr><td colspan=\"2\" align=\"center\" bgcolor=\"#DADADA\"><b>Installing smNews</b></td></tr>\n";
	echo "<tr><td colspan=\"2\" height=\"1\" bgcolor=\"#FFFFFF\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td>Name of template you want to use (without '.php')</td><td> <input type=\"text\" name=\"tpl\" value=\"default\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td>Name of langiage you want to use (without '.php')</td><td> <input type=\"text\" name=\"lang\" value=\"en\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td>Maximum number of articles to show </td><td> <input type=\"text\" name=\"maxShow\" value=\"10\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td>Maximum number of headlines to show </td><td> <input type=\"text\" name=\"maxShowH\" value=\"10\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td>Show which articles on top </td><td> <select name=\"postsOrder\"><option value=\"1\">Newest<option value=\"0\">Oldest</select></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td>The format of the date displayed on articles </td><td> <input type=\"text\" name=\"dateFormat\" value=\"F d, Y g:i\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td><font color=\"red\">Administrator username</font></td><td> <input type=\"text\" name=\"adminName\" value=\"Admin\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td><font color=\"red\">Administrator password</font></td><td> <input type=\"password\" name=\"adminPass\" value=\"admin\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td><font color=\"red\">Administrator email</font></td><td> <input type=\"text\" name=\"adminEmail\" value=\"foo@bar.com\"></td></tr>\n";
	echo "<tr bgcolor=\"#FFFFFF\"><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Finish Installation\"></td></tr>\n";
	echo "</table></form>";
}
elseif ( $_GET['step'] == 3 )
{
	if ( empty($_GET['txtsql_path']) || !is_file($_GET['txtsql_path'].'/txtSQL.class.php') || 
	     empty($_GET['data_path'])   || !is_dir($_GET['data_path']) )
	{
		$word = ( empty($_GET['data_path']) || !is_dir($_GET['data_path']) ) ? 'data' : '';
		echo "<center><b><font color=\"red\">Invalid txtSQL ".$word." folder specified</font><br>\n";
		echo "<a href=\"javascript:history.back()\">Go Back</a></b></center>";
		exit;
	}
	define('TXTSQL_CORE_PATH', $_GET['txtsql_path']);
	require(TXTSQL_CORE_PATH.'/txtSQL.class.php');

	$sql = new txtSQL($_GET['data_path']);
	$sql->strict(0);
	if ( $sql->connect($_GET['username'], $_GET['password']) === FALSE )
	{
		echo "<center><b><font color=\"red\">Invalid username/password specified, txtSQL said: </font><pre>";
		echo $sql->get_last_error();
		echo "</pre>\n<a href=\"javascript:history.back()\">Go Back</a></b></center>";
		exit;
	}
	elseif ( empty($_GET['dbname']) || $sql->selectdb($_GET['dbname']) === FALSE )
	{
		echo "<center><b><font color=\"red\">Invalid database specified</font><br>\n";
		echo "<a href=\"javascript:history.back()\">Go Back</a></b></center>";
		exit;		
	}
	elseif ( empty($_GET['dbname']) || $sql->selectdb($_GET['dbname']) === FALSE )
	{
		echo "<center><b><font color=\"red\">Invalid database specified</font><br>\n";
		echo "<a href=\"javascript:history.back()\">Go Back</a></b></center>";
		exit;		
	}

	$config = <<<EOF
<?php
\$sqlPath    = '$_GET[txtsql_path]';
\$sqlData    = '$_GET[data_path]';
\$sqlUser    = '$_GET[username]';
\$sqlPass    = '$_GET[password]';
\$sqlDB      = '$_GET[dbname]';
define('TXTSQL_CORE_PATH', \$sqlPath);

\$tpl        = '$_POST[tpl]';
\$lang       = '$_POST[lang]';
\$maxShow    = $_POST[maxShow];
\$maxShowH   = $_POST[maxShowH];
\$postsOrder = $_POST[postsOrder];
\$dateFormat = '$_POST[dateFormat]';

// END OF CONFIGURATION
// ===================================================================
require_once('./functions.php');
require_once('./languages/'.\$lang.'.php');
require_once('./templates/'.\$tpl.'.php');
require_once(TXTSQL_CORE_PATH.'/txtSQL.class.php');

\$sql = new txtSQL(\$sqlData);
\$sql->strict(0);
\$sql->connect(\$sqlUser, \$sqlPass) or showError(\$LANG['txtsql']['connectError'], E_USER_ERROR, \$sql->get_last_error());
\$sql->selectdb(\$sqlDB) or showError(\$LANG['txtsql']['dbNotFound'], E_USER_ERROR, \$sql->get_last_error());
EOF;
$config .= "?>";

	$fp = fopen('./config.php', 'w');
	      fwrite($fp, $config);
	      fclose($fp);

	// Create table articles
	$sql->strict(0);
	$sql->execute('create table',
		array('table'   => 'articles',
		      'columns' => array('id'       => array('type' => 'int',    'auto_increment' => 1, 'permanent' => 1, 'primary' => 1),
		                         'posterid' => array('type' => 'int',    'permanent'      => 1),
		                         'date'     => array('type' => 'date'),
		                         'subject'  => array('type' => 'string'),
		                         'article'  => array('type' => 'text')))) or die($sql->get_last_error());

	// Create table 'users'
	$sql->execute('create table',
		array('table'   => 'users',
		      'columns' => array('id'       => array('type' => 'int',    'auto_increment' => 1,  'permanent' => 1, 'primary' => 1),
		                         'username' => array('type' => 'string', 'max'            => 25, 'permanent' => 1),
		                         'password' => array('type' => 'string', 'max'            => 32),
		                         'email'    => array('type' => 'string', 'max'            => 50, 'default'   => '[no-email]')))) or die($sql->get_last_error());

	// Create administrator account
	$sql->execute('insert',
		array('table'  => 'users',
		      'values' => array('username' => $_POST['adminName'],
		                        'password' => md5($_POST['adminPass']),
		                        'email'    => $_POST['adminEmail']))) or die($sql->get_last_error());

	// Create table 'comments'
	$sql->execute('create table',
		array('table'   => 'comments',
		      'columns' => array('id'       => array('type' => 'int', 'auto_increment' => 1, 'permanent' => 1, 'primary' => 1),
		                         'newsid'   => array('type' => 'int', 'permanent' => 1),
		                         'posterid' => array('type' => 'int', 'permanent' => 1),
		                         'date'     => array('type' => 'date'),
		                         'comment'  => array('type' => 'text')))) or die($sql->get_last_error());

	// Insert a test post
	$sql->execute('insert', array('table' => 'articles', 'values' => array('posterid' => 1, 'subject'  => 'Test Post', 'article' => 'This is just a test post'))) or die($sql->get_last_error());
	$sql->execute('insert', array('table' => 'comments', 'values' => array('newsid'   => 1, 'posterid' => 1,           'comment' => 'Test comment')))             or die($sql->get_last_error());

	echo "<center><font color=\"red\"><b>Done installing!</b></font><br>\n";
	echo "Make sure to delete this install.php file, or hide it from the public to clear the security hole<br><br>\n";
}

?>

</body>
</html>