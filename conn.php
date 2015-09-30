<?php
//Database information
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

//Connect to MySQL
$mysqli = new MeekroDB(DB_SERVER, DB_USER, DB_PASS);
?>