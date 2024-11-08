<?php

require_once __DIR__ . '/config.php';

$DB_HOST = DB_HOST;
$db_username = DB_USERNAME;
$db_password = DB_PASSWORD;
$database = DB_NAME;
$port = DB_PORT;

// Create a connection
$conn = mysqli_connect($DB_HOST, $db_username, $db_password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
