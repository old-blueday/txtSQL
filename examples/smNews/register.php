<?php
require_once('./config.php');

// Register this user with the information given as long as it's valid
if ( isset($_GET['action']) && $_GET['action'] == 'register' )
{
	// Check for any errors in the information entered
	if ( empty($_POST['username']) )
	{
		$error = $LANG['forgotUsername'];
	}
	elseif ( !preg_match('/^[a-zA-z0-9_]{4,25}$/', $_POST['username']) )
	{
		$error = $LANG['wrongUsername'];
	}
	elseif ( userExists($_POST['username']) )
	{
		$error = $LANG['userExists'];
	}

	if ( empty($_POST['password']) )
	{
		$error = $LANG['forgotPassword'];
	}
	elseif ( !preg_match('/^[a-zA-Z0-9_]{4,25}$/', $_POST['password']) )
	{
		$error = $LANG['wrongPassword'];
	}

	// Add the user to the database and give a confirmation message
	if ( !isset($error) )
	{
		execute('insert',
		  array('table' => 'users',
		        'values' => array('username' => $_POST['username'],
		                          'password' => md5($_POST['password']),
		                          'email'    => $_POST['email'])));
		echo sprintf($LANG['registered'], '<a href="login.php">'.$LANG['login'].'</a>');
		exit;
	}
	$_GET['action'] = '';
}

// Show the registration form
if ( empty($_GET['action']) )
{
	$template = str_replace(array('{{USERNAME}}', '{{PASSWORD}}', '{{EMAIL}}', '{{ERROR}}'),
				array($_POST['username'], $_POST['password'], $_POST['email'], $error),
				$TPL['register']['form']);	
	echo $template;
}

function userExists ( $user )
{
	$exists = execute('select',
		    array('table' => 'users',
			  'where' => array('username =~ '.$user),
			  'limit' => array(0,0)));

	return ( count($exists) == 1 ) ? TRUE : FALSE;
}
?>