<?php


function connectToDatabase() {
    $mysqli_conn = new mysqli('localhost', 'root', '', 'connection_db', 6453);
    
    if ($mysqli_conn->connect_error) {
        die("Connection failed: " . $mysqli_conn->connect_error);
    }
    return $mysqli_conn;
}


function insertBook($isbn_no, $title, $author) {
    $mysqli_conn = connectToDatabase();
    $stmt_insert = $mysqli_conn->prepare("INSERT INTO books (ISBN_NO, title, author) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("sss", $isbn_no, $title, $author);
    $stmt_insert->execute();
    $stmt_insert->close();
    $mysqli_conn->close();
}


function fetchBooks() {
    $mysqli_conn = connectToDatabase();
    $res_gen_books = $mysqli_conn->query("SELECT * FROM books");
    $mysqli_conn->close();
    return $res_gen_books;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn_no_unique_varchar = $_POST['isbn_no'];
    $title_varchar = $_POST['title'];
    $author_varchar = $_POST['author'];
    insertBook($isbn_no_unique_varchar, $title_varchar, $author_varchar);
}

$res_gen_books = fetchBooks();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #2c3e50;
            color: #ecf0f1;
        }
        .form-container {
            display: flex;
            justify-content: center;
            padding: 20px;
            margin: 40px auto;
            max-width: 400px;
            background-color: #34495e;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        form {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        label {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="submit"] {
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
        }
        input[type="text"] {
            background-color: #ecf0f1;
            color: #2c3e50;
            font-size: 1rem;
        }
        input[type="submit"] {
            background-color: #e74c3c;
            color: #fff;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #c0392b;
        }
        .table-container {
            margin: 40px auto;
            max-width: 90%;
            background-color: #34495e;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid #7f8c8d;
        }
        th {
            background-color: #e67e22;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #7f8c8d;
        }
        tr:hover {
            background-color: #95a5a6;
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>

<div class="form-container">
    <form action="" method="POST">
        <label for="isbn_no">ISBN Number:</label>
        <input type="text" name="isbn_no" id="isbn_no" required>

        <label for="title">Book Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="author">Author Name:</label>
        <input type="text" name="author" id="author" required>

        <input type="submit" value="Submit">
    </form>
</div>

<div class="table-container">
    <table>
        <tr>
            <th>ISBN No</th>
            <th>Title</th>
            <th>Author</th>
        </tr>
        <?php while ($row = $res_gen_books->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['ISBN_NO']); ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['author']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

<?php
$res_gen_books->free();
?>
