<?php
include 'connection.php'; // Include database connection

$i = $_GET['ISBN_NO']; // Get books ISBN_NO from the URL

// Fetch books details for the given ISBN_NO
$sql = "SELECT * FROM books WHERE ISBN_NO = '$i'";
$result = $conn->query($sql);
$books = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs
    $name = $_POST['title'];
    $disease = $_POST['author'];

    // Update query
    $sql = "UPDATE books  SET title = '$name', author = '$disease' WHERE patient_no = '$ISBN_NO'";

    if ($conn->query($sql) === TRUE) {
        header('Location: index.php'); // Redirect to the books list after success
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Books</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcISBN_NOslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <center>
    <h2>Edit Books</h2>
    <form method="post">
        <label for="title">pName:</label><br>
        <input type="text" ISBN_NO="title" name="title" value="<?php echo $books ['title']; ?>" required><br><br>

        <label for="author">disease:</label><br>
        <input type="text" ISBN_NO="author" name="author" value="<?php echo $books ['author']; ?>" required><br><br>

        <input class="btn btn-success" type="submit" value="Update books ">
    </form>
    <br>
    <button class="btn btn-primary"> <a class="bs" href="index.php">Back to Patient List</a></button>
    </center>   
</div>    
</body>
</html>
