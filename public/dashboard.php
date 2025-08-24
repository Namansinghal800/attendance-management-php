<?php
require_once __DIR__ . '/../auth/require_login.php';
require_once __DIR__ . '/../db.php';
$pdo = get_db();

$totalStudents = (int)$pdo->query('SELECT COUNT(*) AS c FROM students')->fetch()['c'];
$today = date('Y-m-d');
$presentToday = (int)$pdo->prepare('SELECT COUNT(*) AS c FROM attendance WHERE date = ? AND status = "Present"');
$presentToday_stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM attendance WHERE date = ? AND status = "Present"');
$presentToday_stmt->execute([$today]);
$presentToday = (int)$presentToday_stmt->fetch()['c'];

$absentToday_stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM attendance WHERE date = ? AND status = "Absent"');
$absentToday_stmt->execute([$today]);
$absentToday = (int)$absentToday_stmt->fetch()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Attendance App</title>
<link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
  <div class="grid three">
    <div class="kpi">
      <div>
        <div class="muted">Total Students</div>
        <div class="big"><?php echo $totalStudents; ?></div>
      </div>
      <div>👩‍🎓</div>
    </div>
    <div class="kpi">
      <div>
        <div class="muted">Present Today (<?php echo $today; ?>)</div>
        <div class="big"><?php echo $presentToday; ?></div>
      </div>
      <div>✅</div>
    </div>
    <div class="kpi">
      <div>
        <div class="muted">Absent Today (<?php echo $today; ?>)</div>
        <div class="big"><?php echo $absentToday; ?></div>
      </div>
      <div>❌</div>
    </div>
  </div>

  <div class="card" style="margin-top:16px;">
    <h2>Quick Actions</h2>
    <div class="grid two">
      <a class="btn" href="students.php">Manage Students</a>
      <a class="btn" href="attendance.php">Mark Today's Attendance</a>
      <a class="btn secondary" href="attendance_view.php">View Reports</a>
      <a class="btn secondary" href="export_csv.php">Export CSV</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
