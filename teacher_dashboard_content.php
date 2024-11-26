<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

require_once "db_connect.php";

$teacherId = $_SESSION['user']['id'];
$currentDate = date('Y-m-d H:i:s');

// Query for today's classes
$query_today = "SELECT * FROM class WHERE teacherid = ? AND DATE(starttime) = CURDATE() ORDER BY starttime";
$stmt_today = $conn->prepare($query_today);
$stmt_today->bind_param("i", $teacherId);
$stmt_today->execute();
$result_today = $stmt_today->get_result();
$today_classes = $result_today->fetch_all(MYSQLI_ASSOC);

// Query for past classes
$query_past = "SELECT * FROM class WHERE teacherid = ? AND endtime < ? ORDER BY starttime DESC";
$stmt_past = $conn->prepare($query_past);
$stmt_past->bind_param("is", $teacherId, $currentDate);
$stmt_past->execute();
$result_past = $stmt_past->get_result();
$past_classes = $result_past->fetch_all(MYSQLI_ASSOC);

// Query for future classes
$query_future = "SELECT * FROM class WHERE teacherid = ? AND starttime > ? ORDER BY starttime";
$stmt_future = $conn->prepare($query_future);
$stmt_future->bind_param("is", $teacherId, $currentDate);
$stmt_future->execute();
$result_future = $stmt_future->get_result();
$future_classes = $result_future->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .class-list {
            margin: 20px 0;
        }

        .class-item {
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <main>
        <h1>Teacher Dashboard</h1>

        <section>
            <h2>Today's Classes</h2>
            <?php if (!empty($today_classes)): ?>
                <ul class="class-list">
                    <?php foreach ($today_classes as $class): ?>
                        <li class="class-item">
                            <a href="mark_attendance.php?view=mark&classid=<?= $class['id'] ?>">
                                Class <?= $class['id'] ?> | Starts: <?= date('Y-m-d H:i', strtotime($class['starttime'])) ?> | Ends: <?= date('Y-m-d H:i', strtotime($class['endtime'])) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No classes scheduled for today.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Past Classes</h2>
            <button onclick="toggleSection('pastClasses')">Show/Hide Past Classes</button>
            <div id="pastClasses" style="display: none;">
                <?php if (!empty($past_classes)): ?>
                    <ul class="class-list">
                        <?php foreach ($past_classes as $class): ?>
                            <li class="class-item">
                                <a href="mark_attendance.php?view=mark&classid=<?= $class['id'] ?>">
                                    Class <?= $class['id'] ?> | Date: <?= date('Y-m-d', strtotime($class['starttime'])) ?> | Time: <?= date('H:i', strtotime($class['starttime'])) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No past classes available.</p>
                <?php endif; ?>
            </div>
        </section>

        <section>
            <h2>Future Classes</h2>
            <button onclick="toggleSection('futureClasses')">Show/Hide Future Classes</button>
            <div id="futureClasses" style="display: none;">
                <?php if (!empty($future_classes)): ?>
                    <ul class="class-list">
                        <?php foreach ($future_classes as $class): ?>
                            <li class="class-item">
                                <a href="mark_attendance.php?view=mark&classid=<?= $class['id'] ?>">
                                    Class <?= $class['id'] ?> | Date: <?= date('Y-m-d', strtotime($class['starttime'])) ?> | Time: <?= date('H:i', strtotime($class['starttime'])) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No future classes available.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section.style.display === 'none') {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        }
    </script>
</body>

</html>