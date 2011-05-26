<?php

if ( empty($_POST['whereClause']) )
{
	error('Missing the where clause', E_USER_ERROR);
}
else
{
	execute('delete', array(
		'db'    => $_GET['db'],
		'table' => $_GET['table'],
		'where' => unserialize($_POST['whereClause'])
	));

	header('Location: index.php?page=table_edit&action=browse_table&db='.$_GET['db'].'&table='.$_GET['table'].'&s='.$_POST['s'].'&sdir='.$_POST['sdir'].'&scolumn='.$_POST['scolumn']);
}

?>