<?php

if ( !$sql->grant_permissions('add', $_POST['new_username'], $_POST['new_password']) )
{
	error('Error while trying to add a new user; txtSQL said: '.$sql->get_last_error(), E_USER_ERROR);
}

header('Location: index.php?msg=002&redir=users');
?>