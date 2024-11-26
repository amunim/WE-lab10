<?php
$host = 'localhost';
$username = 'root';
$password = ''; // Add your password if needed
$dbname = 'con';

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname, port: 5643);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
