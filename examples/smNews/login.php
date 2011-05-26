<?php
require_once('./config.php');

if ( !empty($_GET['action']) && $_GET['action'] == 'logout' )
{
	setcookie('userid', '', (time()+60*60*24*30));
	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit();
}

if ( !empty($_GET['action']) && $_GET['action'] == 'login' )
{
	if ( empty($_POST['username']) )
	{
		$error = $LANG['forgotUsername'];
	}
	elseif ( empty($_POST['password']) )
	{
		$error = $LANG['forgotPassword'];
	}
	else
	{
		$results = execute('select',
			     array('table' => 'users',
				   'where' => array('username =~ ^'.$_POST['username'].'$', 'and', 'password = '.md5($_POST['password'])),
				   'limit' => array(0,0)));

		if ( count($results) == 0 )
		{
			$error = $LANG['wrongLogin'];
		}
	}

	if ( !isset($error) )
	{
		setcookie('userid', $results[0]['id'], (time()+60*60*24*30));
		printf($LANG['loggedin'], '<a href="index.php?smA=">'.$LANG['gohome'].'</a>');
		exit();
	}
	$_GET['action'] = '';
}

if ( empty($_GET['action']) )
{
	$template = str_replace(array('{{ERROR}}', '{{USERNAME}}'),
				array($error, $_POST['username']),
				$TPL['login']['form']);
	exit($template);
}
?>