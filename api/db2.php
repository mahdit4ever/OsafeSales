<?php
$servername = "localhost";
$username = "apemalay_osafe";
$password = "osafe01234";
$dbname = "apemalay_osafe";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
?>
