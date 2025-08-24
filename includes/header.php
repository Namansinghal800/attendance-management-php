<?php require_once __DIR__ . '/../config.php'; ?>
<div class="navbar">
  <div class="inner">
    <div class="brand"><a href="<?php echo BASE_URL; ?>/public/dashboard.php">Attendance App</a></div>
    <div class="navlinks">
      <a class="btn secondary" href="<?php echo BASE_URL; ?>/public/students.php">Students</a>
      <a class="btn secondary" href="<?php echo BASE_URL; ?>/public/attendance.php">Mark Attendance</a>
      <a class="btn secondary" href="<?php echo BASE_URL; ?>/public/attendance_view.php">Reports</a>
      <a class="btn danger" href="<?php echo BASE_URL; ?>/auth/logout.php">Logout</a>
    </div>
  </div>
</div>
