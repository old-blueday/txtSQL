<?php

execute('drop database', array('db' => $_GET['db']));
header('Location: index.php?page=view_databases');

?>