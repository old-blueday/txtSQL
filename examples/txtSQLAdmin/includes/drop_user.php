<?php

if ( !$sql->grant_permissions('drop', $_GET['user'], $_POST['pass']) )
{
	error('Error while trying to drop user '.$_GET['user'].'; txtSQL said: '.$sql->get_last_error(), E_USER_ERROR);
}

header('Location: index.php?msg=003&redir=users');
?>