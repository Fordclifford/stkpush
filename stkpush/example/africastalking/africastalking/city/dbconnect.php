<?php

define('DB_HOST','192.168.1.243:3306');
define('DB_HOST_NAME','root');
define('DB_HOST_PASS','mysql');
define('DB_NAME','mifostenant-default');

$conn =mysqli_connect(DB_HOST,DB_HOST_NAME,DB_HOST_PASS,DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
//echo "Connected successfully";