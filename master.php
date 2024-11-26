<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$user = $_SESSION['user'];

// Extract first name and handle cases for long names
$firstName = strtok($user['fullname'], ' ');
if (strlen($firstName) > 10) {
    $displayName = "Hello";
} else {
    $displayName = "Hello " . htmlspecialchars($firstName);
}

// Determine the page content based on user role
if ($user['role'] === 'teacher') {
    $page_content = 'teacher_dashboard_content.php';  // For teacher
} else {
    $page_content = 'student_dashboard_content.php';  // For student
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        header {
            background-color: #2196F3; /* Blue header for dashboard */
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header-content {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .header-content h1 {
            margin: 0;
            font-size: 24px;
            color: white;
        }
        .header-content div {
            font-size: 18px;
            color: white;  /* Text color for display name */
        }
        .header-buttons a {
            margin-left: 10px;
            text-decoration: none;
            color: white;  /* Button text color */
            font-size: 16px;
        }
        .header-buttons a:hover {
            text-decoration: underline;
        }
        /* Additional styles for your page */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: center; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div><?= $displayName ?></div> <!-- Display user name -->
            <h1><?= ($user['role'] === 'teacher') ? 'Teacher Dashboard' : 'Student Dashboard' ?></h1> <!-- Title changes based on role -->
        </div>
        <div class="header-buttons">
            <a href="signup.php">Logout</a> <!-- Logout link -->
            <?php if ($user['role'] === 'teacher'): ?>
                <a href="add_class.php">Add Class</a> <!-- Teacher-specific link -->
            <?php endif; ?>
        </div>
    </header>

    <!-- Main content section: Dynamically include the correct content -->
    <main>
        <?php include $page_content; ?>
    </main>
</body>
</html>
