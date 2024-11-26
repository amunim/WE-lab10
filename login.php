<?php
session_start();
require_once "db_connect.php"; // Ensure the database connection works

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $class = trim($_POST["class"]);
    $role = $_POST["role"];

    // Validate form inputs
    if (empty($email) || empty($class) || empty($role)) {
        $message = "All fields are required.";
    } else {
        // Query the database to authenticate user
        $query = "SELECT * FROM user WHERE email = ? AND class = ? AND role = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $email, $class, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Login successful
            $user = $result->fetch_assoc();
            $_SESSION['user'] = $user;

            // Redirect based on the role
            if ($role === 'teacher') {
                header("Location: teacher_dashboard.php");
            } else if ($role === 'student') {
                header("Location: student_dashboard.php");
            }
            exit;
        } else {
            // Login failed
            $message = "Invalid credentials. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <div class="login-form">
        <h2>Login</h2>
        <?php if ($message): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="class" placeholder="Class" required>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
            <button type="submit">Log In</button>
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </form>
    </div>
</body>
</html>
