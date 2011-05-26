<?php

execute('delete', array(
	'db'    => $_GET['db'],
	'table' => $_GET['table']
));

header('Location: index.php?page=table_prop&db='.$_GET['db'].'&table='.$_GET['table']);
?>