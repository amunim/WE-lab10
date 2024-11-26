<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

require_once "db_connect.php";

// Fetch teacher IDs from user table
$queryTeachers = "SELECT id, fullname FROM user WHERE role = 'teacher'";
$stmtTeachers = $conn->prepare($queryTeachers);
$stmtTeachers->execute();
$resultTeachers = $stmtTeachers->get_result();

$error = "";
$success = "";

// Handle form submission if method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacherid = $_POST["teacherid"];
    $date = $_POST["date"];
    $starttime = $_POST["starttime"];
    $endtime = $_POST["endtime"];
    $credit_hours = $_POST["credit_hours"];

    // Combine date with times to form full datetime strings
    $start_datetime = $date . " " . $starttime;
    $end_datetime = $date . " " . $endtime;

    // Check for overlapping classes for the same teacher
    $query_overlap = "SELECT * FROM class 
                      WHERE teacherid = ? 
                      AND (
                          (starttime < ? AND endtime > ?) OR
                          (starttime < ? AND endtime > ?)
                      )";
    $stmt_overlap = $conn->prepare($query_overlap);
    $stmt_overlap->bind_param("issss", $teacherid, $end_datetime, $end_datetime, $start_datetime, $start_datetime);
    $stmt_overlap->execute();
    $result_overlap = $stmt_overlap->get_result();

    if ($result_overlap->num_rows > 0) {
        $error = "The selected time slot overlaps with an existing class.";
    } else {
        // Insert class into the database
        $query = "INSERT INTO class (teacherid, starttime, endtime, credit_hours) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issi", $teacherid, $start_datetime, $end_datetime, $credit_hours);

        if ($stmt->execute()) {
            $success = "Class added successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Class</title>
    <link rel="stylesheet" href="style.css">
    <style>
        form {
            margin-bottom: 20px;
        }

        button[type="submit"] {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .btn {
            display: inline-block;
            background-color: #007BFF;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            margin-top: 20px;
            font-size: 16px;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .success {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <h2>Add Class</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="teacherid">Select Teacher</label>
        <select name="teacherid" required>
            <?php while ($teacher = $resultTeachers->fetch_assoc()): ?>
                <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['fullname']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="date">Date</label>
        <input type="date" name="date" required>

        <label for="starttime">Start Time</label>
        <input type="time" name="starttime" required>

        <label for="endtime">End Time</label>
        <input type="time" name="endtime" required>

        <label for="credit_hours">Credit Hours</label>
        <input type="number" name="credit_hours" required>

        <button type="submit">Add Class</button>
    </form>

    <a href="teacher_dashboard.php" class="btn">Go to Dashboard</a>
</body>

</html>