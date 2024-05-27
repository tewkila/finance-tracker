<?php
// Database configuration settings
$server = 'anysql.itcollege.ee'; // Database server
$user = ''; // Database username
$database = ''; // Database name
$password = ''; // Database password

// Create a connection
$link = new mysqli($server, $user, $password, $database);

// Check the connection
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}
