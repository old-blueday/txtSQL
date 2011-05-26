<?php
setDefault('$_GET["column"]');

$columns =
execute('describe', array(
	'db'    => $_GET['db'],
	'table' => $_GET['table']
));

if ( !empty($columns['primary']) )
{
	execute('alter table', array(
		'action' => 'dropkey',
		'db'     => $_GET['db'],
		'table'  => $_GET['table']
	));
}

$results =
$sql->altertable(array(
	'action' => 'addkey',
	'db'     => $_GET['db'],
	'table'  => $_GET['table'],
	'values' => array(
		'name'   => $_GET['column']
	)
));

if ( !$results && !empty($columns['primary']) )
{
	execute('alter table', array(
		'action' => 'addkey',
		'db'     => $_GET['db'],
		'table'  => $_GET['table'],
		'values' => array(
			'name' => $columns['primary']
		)
	));
}
if ( !$results )
{
	error('Error while trying to add a primary key, txtSQL said: '.$sql->get_last_error(), E_USER_ERROR);
}

header('Location: index.php?page=table_prop&db='.$_GET['db'].'&table='.$_GET['table']);
?>