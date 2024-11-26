<?php
session_start();
require_once "db_connect.php"; // Ensure this is correct and exists

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $class = trim($_POST["class"]); // Corrected case
    $role = $_POST["role"];

    // Validate form inputs
    if (empty($fullname) || empty($email) || empty($class) || empty($role)) {
        $message = "All fields are required.";
    } else {
        // Check if email is already in use
        $checkEmailQuery = "SELECT * FROM user WHERE email = ?";
        $stmt = $conn->prepare($checkEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "This email is already registered.";
        } else {
            // Insert new user into the database
            $insertQuery = "INSERT INTO user (fullname, email, class, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssss", $fullname, $email, $class, $role);

            if ($stmt->execute()) {
                $_SESSION['signup_success'] = "Registration successful! You can now log in.";
                header("Location: login.php");
                exit;
            } else {
                $message = "Error: " . $conn->error;
            }
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
    <title>Sign Up</title>
</head>
<body>
    <div class="login-form">
        <h2>Sign Up</h2>
        <?php if ($message): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="signup.php" method="post">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="class" placeholder="Class" required> <!-- Fixed 'class' casing -->
            <select name="role" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
            <button type="submit">Sign Up</button>
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </form>
    </div>
</body>
</html>
