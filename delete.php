<?php
include 'connection.php'; // Include database connection

$i = $_GET['ISBN_NO']; // Get student i from the URL

// Delete query
$sql = "DELETE FROM books WHERE ISBN_NO = 'ISBN_NO'";

if ($conn->query($sql) === TRUE) {
    header('Location: index.php'); // Redirect to the student list after deletion
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
