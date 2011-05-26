<?php

execute('drop table', array(
	'db' => $_GET['db'],
	'table' => $_GET['table']
));

header('Location: index.php?page=view_database&db='.$_GET['db']);
?>