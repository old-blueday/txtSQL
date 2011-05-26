<?php
setDefault('$_GET["column"]');

execute('alter table', array(
	'action' => 'dropkey',
	'db'     => $_GET['db'],
	'table'  => $_GET['table'],
	'name'   => $_GET['column']
));

header('Location: index.php?page=table_prop&db='.$_GET['db'].'&table='.$_GET['table']);
?>