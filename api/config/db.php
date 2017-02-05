<?php

define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','');
define('DBNAME','toucantech');

$connection = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
if ($connection->connect_error) die($connection->connect_error);
