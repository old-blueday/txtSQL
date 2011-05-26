<?php

if ( !$sql->grant_permissions('edit', $_GET['user'], $_POST['pass'], $_POST['newpass']) )
{
	error('Error while trying to edit user; txtSQL said: '.$sql->get_last_error(), E_USER_ERROR);
}

header('Location: index.php?msg=004&redir=users');
?>