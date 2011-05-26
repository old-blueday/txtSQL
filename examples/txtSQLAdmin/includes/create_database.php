<?php

if ( empty($_GET['database']) )
{
	error('Invalid/No database name; can only contain letters, numbers, and underscores.', E_USER_ERROR);
}
elseif ( !execute('create database', array('db' => $_GET['database'])) )
{
	error('Error creating database, txtSQL said: <pre>'.$sql->get_last_error().'</pre>', E_USER_ERROR);
}

header('Location: index.php?page=view_database&db='.$_GET['database']);
exit;
?>