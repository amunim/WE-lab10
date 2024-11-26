<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

require_once "db_connect.php";

// Fetch Classes for Dropdown (Only class IDs)
$query_classes = "SELECT id FROM class";
$result_classes = $conn->query($query_classes);

// Fetch Students and Attendance for the selected class
$classFilter = isset($_POST['classid']) ? $_POST['classid'] : (isset($_GET['classid']) ? $_GET['classid'] : null);
$studentsData = [];
$attendanceData = [];

if ($classFilter) {
    // Fetch all students in the system
    $stmt_students = $conn->prepare("SELECT id, fullname FROM user WHERE role = 'student'");
    $stmt_students->execute();
    $result_students = $stmt_students->get_result();
    $studentsData = $result_students->fetch_all(MYSQLI_ASSOC);

    // Fetch attendance records for the selected class
    $stmt_attendance = $conn->prepare("
        SELECT a.studentid, a.isPresent, a.marked_at 
        FROM attendance a 
        WHERE a.classid = ?
    ");
    $stmt_attendance->bind_param("i", $classFilter);
    $stmt_attendance->execute();
    $result_attendance = $stmt_attendance->get_result();
    $attendanceRecords = $result_attendance->fetch_all(MYSQLI_ASSOC);

    // Reformat attendance data for quick lookup
    foreach ($attendanceRecords as $record) {
        $attendanceData[$record['studentid']] = [
            'isPresent' => $record['isPresent'],
            'marked_at' => $record['marked_at'],
        ];
    }

    // Merge student data with attendance data
    foreach ($studentsData as &$student) {
        $studentId = $student['id'];
        $student['isPresent'] = $attendanceData[$studentId]['isPresent'] ?? 0; // Default to not present
        $student['marked_at'] = $attendanceData[$studentId]['marked_at'] ?? null; // Default to no marking
    }

    $attendanceData = $studentsData;
}

// Handle Mark Attendance Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance']) && $classFilter) {
    $attendanceUpdates = $_POST['attendance']; // Array with student IDs as keys and "1" for present
    $currentTime = date('Y-m-d H:i:s');

    foreach ($studentsData as $student) {
        $studentId = $student['id'];
        $isPresent = $attendanceUpdates[$studentId] ? 1 : 0;

        // Check if an attendance record exists for this student and class
        $stmt_check = $conn->prepare("SELECT studentid FROM attendance WHERE classid = ? AND studentid = ?");
        $stmt_check->bind_param("ii", $classFilter, $studentId);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Update existing record
            $stmt_update = $conn->prepare("
                UPDATE attendance 
                SET isPresent = ?, marked_at = ? 
                WHERE classid = ? AND studentid = ?
            ");
            $stmt_update->bind_param("isii", $isPresent, $currentTime, $classFilter, $studentId);
            $stmt_update->execute();
        } else {
            // Insert new record
            $stmt_insert = $conn->prepare("
                INSERT INTO attendance (classid, studentid, isPresent, marked_at)
                VALUES (?, ?, ?, ?)
            ");
            $stmt_insert->bind_param("iiis", $classFilter, $studentId, $isPresent, $currentTime);
            $stmt_insert->execute();
        }
    }

    echo "<script>alert('Attendance updated successfully!'); window.location.href = '?classid=$classFilter';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Attendance</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .low-attendance {
            color: red;
        }

        .mid-attendance {
            color: yellow;
        }

        .high-attendance {
            color: green;
        }
    </style>
</head>

<body>
    <header>
        <h1>Check Attendance</h1>
        <nav>
            <a href="mark_attendance.php" class="btn active">Mark</a>
            <a href="teacher_dashboard.php" class="btn">Dashboard</a>
        </nav>
    </header>
    <main>
        <!-- Select Class -->
        <form method="GET" id="classFilterForm">
            <label for="classFilter">Select Class</label>
            <select name="classid" id="classFilter" onchange="this.form.submit()" required>
                <option value="">Select Class</option>
                <?php while ($row = $result_classes->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= ($classFilter == $row['id']) ? 'selected' : '' ?>>Class <?= $row['id'] ?></option>
                <?php endwhile; ?>
            </select>
        </form>

        <?php if ($classFilter): ?>
            <p>Attendance Records for Class <?= $classFilter ?>:</p>
            <form method="POST">
                <input type="hidden" name="classid" value="<?= $classFilter ?>">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Present?</th>
                            <th>Marked At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studentsData as $student): ?>
                            <tr>
                                <td><?= $student['id'] ?></td>
                                <td><?= htmlspecialchars($student['fullname']) ?></td>
                                <td>
                                    <!-- Hidden input to send '0' if checkbox is not checked -->
                                    <input type="hidden" name="attendance[<?= $student['id'] ?>]" value="0">
                                    <input type="checkbox" name="attendance[<?= $student['id'] ?>]" value="1" <?= $student['isPresent'] ? 'checked' : '' ?>>
                                </td>
                                <td><?= $student['marked_at'] ? htmlspecialchars($student['marked_at']) : '' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">Save Attendance</button>
            </form>
        <?php endif; ?>
    </main>
</body>

</html>