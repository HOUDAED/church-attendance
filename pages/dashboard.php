<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

// Get username
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$username = $stmt->fetchColumn();

// Fetch dashboard statistics
$stats = [
    'total_members' => $pdo->query("SELECT COUNT(*) FROM members WHERE status = 'active'")->fetchColumn(),
    'today_attendance' => $pdo->query("SELECT COUNT(*) FROM attendances WHERE attendance_date = CURRENT_DATE")->fetchColumn(),
    'total_events' => $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURRENT_DATE")->fetchColumn(),
    'total_churches' => $pdo->query("SELECT COUNT(*) FROM churches")->fetchColumn()
];

// Fetch upcoming events
$stmt = $pdo->query("SELECT * FROM events WHERE event_date >= CURRENT_DATE ORDER BY event_date LIMIT 5");
$upcoming_events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Church Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?= h($username) ?></span>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </nav>
    <div class="dashboard">
        <h1>Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3><?= h($stats['total_members']) ?></h3>
                <p>Active Members</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3><?= h($stats['today_attendance']) ?></h3>
                <p>Today's Attendance</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar"></i>
                <h3><?= h($stats['total_events']) ?></h3>
                <p>Upcoming Events</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-church"></i>
                <h3><?= h($stats['total_churches']) ?></h3>
                <p>Churches</p>
            </div>
        </div>

        <div class="content-grid">
            <div class="events-list">
                <h2>Upcoming Events</h2>
                <?php foreach ($upcoming_events as $event): ?>
                    <div class="event-item">
                        <h3><?= h($event['title']) ?></h3>
                        <p><i class="fas fa-calendar"></i> <?= h(date('m/d/Y', strtotime($event['event_date']))) ?></p>
                        <p><i class="fas fa-clock"></i> <?= h($event['event_time']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="quick-actions-grid">
                    <button onclick="location.href='members.php'" class="action-button member-action">
                        <div class="action-icon"><i class="fas fa-user-plus"></i></div>
                        <div class="action-content">
                            <span class="action-title">New Member</span>
                            <span class="action-desc">Add a new church member</span>
                        </div>
                    </button>
                    
                    <button onclick="location.href='churches.php'" class="action-button church-action">
                        <div class="action-icon"><i class="fas fa-church"></i></div>
                        <div class="action-content">
                            <span class="action-title">Churches</span>
                            <span class="action-desc">Manage church branches</span>
                        </div>
                    </button>

                    <button onclick="location.href='events.php'" class="action-button event-action">
                        <div class="action-icon"><i class="fas fa-calendar-plus"></i></div>
                        <div class="action-content">
                            <span class="action-title">New Event</span>
                            <span class="action-desc">Schedule an event</span>
                        </div>
                    </button>

                    <button onclick="location.href='schedules.php'" class="action-button schedule-action">
                        <div class="action-icon"><i class="fas fa-clock"></i></div>
                        <div class="action-content">
                            <span class="action-title">Schedules</span>
                            <span class="action-desc">Manage weekly schedules</span>
                        </div>
                    </button>

                    <button onclick="location.href='attendance.php'" class="action-button attendance-action">
                        <div class="action-icon"><i class="fas fa-check-square"></i></div>
                        <div class="action-content">
                            <span class="action-title">Mark Attendance</span>
                            <span class="action-desc">Record daily attendance</span>
                        </div>
                    </button>

                    <button onclick="location.href='roles.php'" class="action-button role-action">
                        <div class="action-icon"><i class="fas fa-user-tag"></i></div>
                        <div class="action-content">
                            <span class="action-title">Manage Roles</span>
                            <span class="action-desc">Define member roles</span>
                        </div>
                    </button>

                    <button onclick="location.href='reports.php'" class="action-button report-action">
                        <div class="action-icon"><i class="fas fa-chart-bar"></i></div>
                        <div class="action-content">
                            <span class="action-title">Reports</span>
                            <span class="action-desc">View analytics</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>