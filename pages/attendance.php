<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$error = '';
$success = '';

// Fetch events ordered descending by date
$events = $pdo->query("SELECT id, title, event_date FROM events ORDER BY event_date DESC")->fetchAll();

// Get selected event ID and date from GET, fallback to first event or null
$selectedEventId = $_GET['event_id'] ?? ($events[0]['id'] ?? null);
$selectedDate = $_GET['attendance_date'] ?? date('Y-m-d');

// Fetch members ordered by last and first name
$members = $pdo->query("SELECT id, first_name, last_name FROM members ORDER BY last_name, first_name")->fetchAll();

$attendanceRecords = [];
if ($selectedEventId) {
    $stmt = $pdo->prepare("SELECT * FROM attendances WHERE event_id = ? AND attendance_date = ?");
    $stmt->execute([$selectedEventId, $selectedDate]);
    $attendanceRecords = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$selectedEventId) {
        $error = "No event selected to mark attendance.";
    } else {
        foreach ($_POST['attendance'] as $memberId => $status) {
            $memberId = (int)$memberId;
            $status = in_array($status, ['present', 'absent', 'excused']) ? $status : 'present';

            if (isset($attendanceRecords[$memberId])) {
                $stmt = $pdo->prepare("UPDATE attendances SET status = ? WHERE id = ?");
                $stmt->execute([$status, $attendanceRecords[$memberId]['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO attendances (member_id, event_id, attendance_date, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$memberId, $selectedEventId, $selectedDate, $status]);
            }
        }
        $success = "Attendance updated successfully.";

        // Reload attendance after update
        $stmt = $pdo->prepare("SELECT * FROM attendances WHERE event_id = ? AND attendance_date = ?");
        $stmt->execute([$selectedEventId, $selectedDate]);
        $attendanceRecords = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Member Attendance</title>
    <link rel="stylesheet" href="../css/dashboard.css" />
    <link rel="stylesheet" href="../css/attendance.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        /* Include the above CSS here or link externally */
    </style>
    <script>
        function updateSelectColor(select) {
            select.className = 'status-select ' + select.value;
        }
        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.status-select').forEach(s => updateSelectColor(s));
            document.querySelectorAll('.status-select').forEach(s => {
                s.addEventListener('change', () => updateSelectColor(s));
            });
        });
    </script>
</head>
<body>
     <nav class="navbar">
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>


<div class="container">
    <h1>Member Attendance</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= h($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= h($success) ?></div>
    <?php endif; ?>

<?php if (!$events): ?>
    <div class="no-events-container">
        <i class="fas fa-calendar-times"></i>
        <h2>No Events Available</h2>
        <p>There are currently no events scheduled. Please create an event to start marking attendance.</p>
    </div>
<?php else: ?>

        <form method="GET" class="filter-bar" aria-label="Filter attendance by event and date">
            <label for="event_id">Event:</label>
            <select name="event_id" id="event_id" onchange="this.form.submit()" aria-required="true">
                <?php foreach ($events as $event): ?>
                    <option value="<?= $event['id'] ?>" <?= ($selectedEventId == $event['id']) ? 'selected' : '' ?>>
                        <?= h($event['title']) ?> (<?= h($event['event_date']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="attendance_date">Date:</label>
            <input type="date" id="attendance_date" name="attendance_date" value="<?= h($selectedDate) ?>" onchange="this.form.submit()" />
        </form>

        <form method="POST" aria-label="Attendance form">
            <table class="attendance-table" role="grid">
                <thead>
                    <tr>
                        <th scope="col">Member Name</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): 
                        $attStatus = $attendanceRecords[$member['id']]['status'] ?? 'present';
                    ?>
                    <tr>
                        <td><?= h($member['last_name']) . ' ' . h($member['first_name']) ?></td>
                        <td>
                            <select name="attendance[<?= $member['id'] ?>]" class="status-select <?= $attStatus ?>">
                                <option value="present" <?= $attStatus === 'present' ? 'selected' : '' ?>>Present</option>
                                <option value="absent" <?= $attStatus === 'absent' ? 'selected' : '' ?>>Absent</option>
                                <option value="excused" <?= $attStatus === 'excused' ? 'selected' : '' ?>>Excused</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn-submit" aria-label="Save attendance">
                <i class="fas fa-save"></i> Save Attendance
            </button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
