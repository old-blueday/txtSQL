<?php

$_GET['db'] = isset($_GET['db']) ? $_GET['db'] : '';

if ( !empty($_GET['db']) )
{
	if ( !$sql->unlockDB($_GET['db']) )
	{
		error('Could not lock database, txtSQL said: '.$sql->get_last_error(), E_USER_ERROR);
	}
}

header('Location: '.$_SERVER['HTTP_REFERER']);

?>