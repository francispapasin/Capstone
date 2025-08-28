<?php

// database connection
$host = 'localhost';
$database = 'db_alumniconnect';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password, $database);

if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}   

?>