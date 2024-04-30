<?php
// Database configuration settings
$server = 'anysql.itcollege.ee'; // Database server
$user = 'ICS0008_WT_17'; // Database username
$database = 'ICS0008_17'; // Database name
$password = '7a900f227b86'; // Database password

// Create a connection
$link = new mysqli($server, $user, $password, $database);

// Check the connection
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}