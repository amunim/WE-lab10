<?php
session_start();
require_once "db_connect.php";

// Check if the user is logged in
if (isset($_SESSION['user'])) {
    // Redirect based on user role
    if ($_SESSION['user']['role'] == 'teacher') {
        header("Location: teacher_dashboard.php");
    } elseif ($_SESSION['user']['role'] == 'student') {
        header("Location: student_dashboard.php");
    }
    exit;
}

// Redirect if the user has successfully signed up but not logged in yet
if (isset($_SESSION['signup_success'])) {
    // Show the login page with the success message
    $success_message = $_SESSION['signup_success'];
    unset($_SESSION['signup_success']); // Remove the message after displaying
} else {
    header("Location: signup.php");
    exit;
}

$error = "";

// Login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE email=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            if ($user['role'] == 'teacher') {
                header("Location: teacher_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Attendance System Login</title>
</head>
<body>
    <div class="login-form">
        <h2>Login</h2>
        <?php if (!empty($success_message)): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
</body>
</html>
