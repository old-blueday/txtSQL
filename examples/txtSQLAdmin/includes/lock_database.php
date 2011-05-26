<?php

setDefault('$_GET["db"]');

if ( !empty($_GET['db']) )
{
	if ( !$sql->lockDB($_GET['db']) )
	{
		error('Could not lock database, txtSQL said: '.$sql->get_last_error(), E_USER_ERROR);
	}
}

header('Location: '.$_SERVER['HTTP_REFERER']);
?>