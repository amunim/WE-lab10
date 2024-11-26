<?php
include 'connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs
    $i = $_POST['ISBN_NO'];
    $t = $_POST['title'];
    $a = $_POST['author'];

    // Prepare an insert statement
    $stmt = $conn->prepare("INSERT INTO books (ISBN_NO, title, author) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $i, $t, $a);

    if ($stmt->execute()) {
        header('Location: index.php'); // Redirect to Books list after success
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close(); // Close the statement
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Books</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcislK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <center>
            <h2>Add New Books</h2>
            <form method="post">
                <label for="ISBN_NO">Book ISBN_NO:</label><br>
                <input type="text" name="ISBN_NO" id="ISBN_NO" required><br><br>

                <label for="title">Books Title:</label><br>
                <input type="text" name="title" id="title" required><br><br>

                <label for="author">Author:</label><br>
                <input type="text" name="author" id="author" required><br><br>

                <input class="btn btn-success" type="submit" value="Add Books">
            </form>

            <br>
            <button class="btn btn-primary"><a class="bs" href="index.php" style="color: white; text-decoration: none;">Back to Books List</a></button>
        </center>
    </div>
</body>
</html>
