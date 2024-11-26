<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php'; 

// Check if user is logged in and if 'user' session is set (which holds user data)
if (!isset($_SESSION['user'])) {
    echo "User is not logged in. Please log in again.";
    exit;
}

// Get the logged-in student's user ID from the 'user' session
$userId = $_SESSION['user']['id'];  // Assuming 'id' is stored under 'user' session

// Get selected class id from GET request (if set)
$classId = isset($_GET['classid']) ? $_GET['classid'] : '';

// Query for attendance records based on class ID if provided
$query = "SELECT * FROM attendance WHERE studentid = ?";
if ($classId) {
    $query .= " AND classid = ?";
}
$stmt = $conn->prepare($query);
if ($classId) {
    $stmt->bind_param("ii", $userId, $classId);
} else {
    $stmt->bind_param("i", $userId);
}
$stmt->execute();
$result = $stmt->get_result();

// Check if any records were returned
if ($result->num_rows === 0) {
    $attendanceMessage = "No attendance records found for this student.";
} else {
    $attendanceMessage = null;  // Set to null if records are found
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: center; color: black; }  /* Default text color black */
    </style>
</head>
<body>

    <!-- Class Filter Dropdown -->
    <form method="GET" action="student_dashboard.php">
        <label for="classFilter">Select Class:</label>
        <select name="classid" id="classFilter" onchange="this.form.submit();">
            <option value="">Select Class</option>
            <?php
                $classesQuery = "SELECT id FROM class";
                $classesResult = $conn->query($classesQuery);
                while ($classRow = $classesResult->fetch_assoc()):
            ?>
                <option value="<?= $classRow['id'] ?>" <?= (isset($_GET['classid']) && $_GET['classid'] == $classRow['id']) ? 'selected' : '' ?>>Class <?= $classRow['id'] ?></option>
            <?php endwhile; ?>
        </select>
    </form>

    <!-- Attendance Message (if no records) -->
    <?php if ($attendanceMessage): ?>
        <p><?= $attendanceMessage ?></p>
    <?php endif; ?>

    <h2>Attendance Records</h2>
    <table>
        <thead>
            <tr>
                <th>Class ID</th>
                <th>Attendance</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr style="color: 
                    <?php
                    if ($row['isPresent'] < 75) {
                        echo 'red';
                    } elseif ($row['isPresent'] < 85) {
                        echo 'yellow';
                    } else {
                        echo 'green';
                    }
                    ?>">
                    <td><?php echo $row['classid']; ?></td>
                    <td><?php echo $row['isPresent'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo $row['comments']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Total Attendance Percentage -->
    <h3>Total Attendance: 
        <?php
        $attendanceQuery = "SELECT SUM(isPresent) / COUNT(*) * 100 AS total_percentage FROM attendance WHERE studentid = ?";
        if ($classId) {
            $attendanceQuery .= " AND classid = ?";
        }
        $stmt = $conn->prepare($attendanceQuery);
        if ($classId) {
            $stmt->bind_param("ii", $userId, $classId);
        } else {
            $stmt->bind_param("i", $userId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $percentage = round($row['total_percentage'], 2);

        if ($percentage < 75) {
            echo "<span style='color:red;'>$percentage%</span>";
        } elseif ($percentage < 85) {
            echo "<span style='color:yellow;'>$percentage%</span>";
        } else {
            echo "<span style='color:green;'>$percentage%</span>";
        }
        ?>
    </h3>

</body>
</html>
