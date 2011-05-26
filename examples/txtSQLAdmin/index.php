<?php
// Load up our configuration settings and start up the txtSQL service
include('./config.php');

$_GET['action'] = isset($_GET['action']) ? $_GET['action'] : '';

// Check if the user is logged in or not
if ( isset($_SESSION['txtsqladmin']['login']) )
{
	if ( strtolower($_GET['action']) == 'logout' )
	{
		@session_unregister('txtSQLAdmin');
		@session_unset();
	}
	else
	{
		// Connect to txtSQL
		$sql->connect($_SESSION['txtsqladmin']['username'], $_SESSION['txtsqladmin']['password']) or error($sql->get_last_error(), E_USER_ERROR);

		// Include the proper section of txtSQLAdmin
		$validPages = array(
			'main',
			'php_info',

			'view_databases',
			'view_database',
			'create_database',
			'lock_database',
			'unlock_database',
			'rename_db',
			'drop_db',

			'table_prop',
			'table_edit',
			'create_table',

			'search',
			'search_results',
			'export_database',

			'change_pass',
			'users'
		);

		setDefault('$_GET["page"]', 'main');
		setDefault('$_GET["db"]');
		setDefault('$_GET["table"]');

		// See if the page specified is valid
		if ( !in_array(strtolower($_GET['page']), $validPages) )
		{
			error('Invalid page specified, try again with a valid page or <a href="index.php">Go Home</a>', E_USER_ERROR);
		}

		// Delete any search results because they can slow down the script
		if ( isset($_SESSION['txtsqladmin']['search']) && !strpos(' '.$_GET['page'], 'search') )
		{
			unset($_SESSION['txtsqladmin']['search']);
		}

		// Go ahead and include the proper page
		require('./includes/'.strtolower($_GET['page']).'.php');
		exit;
	}
}
elseif ( strtolower($_GET['action']) == 'login' )
{
	// Attempt a connect to the txtSQL service
	if ( !$sql->connect($_POST['username'], $_POST['password']) )
	{
		$error  = '<font color="red"><b>Could not connect to the txtSQL Service, txtSQL said:</b></font><br />';
		$error .= $sql->get_last_error();
	}
	else
	{
		// log the user in here
		session_register('txtSQLAdmin');
		$_SESSION['txtsqladmin']['login']    = TRUE;
		$_SESSION['txtsqladmin']['username'] = $_POST['username'];
		$_SESSION['txtsqladmin']['password'] = $_POST['password'];
		header('Location: index.php');
		exit;
	}
}

require('./includes/header.php');
?>

<body onload="document.form.username.focus()">
<form method="post" action="index.php?action=login" name="form">

<br/>
<br/>
<br/>
<table width="350" cellspacing="0" cellpadding="0" border="1" bordercolor="AFB0B2" align="center">
	<tr>
		<td>
			<table width="100%" cellspacing="2" cellpadding="0" border="0">
				<tr>
					<td background="images/header_bg.gif" height="26" align="center">
					<div class="header"><B>Login to txtSQLAdmin v2.0</B></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="EFEFEF" height="125" valign="middle" align="center">
						<?php echo $error; ?>
						<br />
						<table cellspacing="0" cellpadding="3" align="center">
							<tr>
								<td align="right">
									<font size="2">Username:</td>
								<td>
									<input type="text" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
								</td>
							</tr>
							<tr>
								<td align="right">
									<font size="2">Password:</td>
								<td>
									<input type="password" name="password">
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<input type="submit" value="Login" class="btn"> <input type="reset" value="Reset Form" class="btn"><br/><br/>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php
require('./includes/footer.php');
?>