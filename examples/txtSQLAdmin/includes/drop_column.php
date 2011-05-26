<?php
setDefault('$_GET["column"]');

execute('alter table', array(
	'db'    => $_GET['db'],
	'table' => $_GET['table'],
	'name'  => $_GET['column'],
	'action' => 'drop'
));

header('Location: index.php?page=table_prop&db='.$_GET['db'].'&table='.$_GET['table']);
?>