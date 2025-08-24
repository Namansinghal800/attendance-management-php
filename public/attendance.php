<?php
require_once __DIR__ . '/../auth/require_login.php';
require_once __DIR__ . '/../db.php';
$pdo = get_db();

$date = $_GET['date'] ?? date('Y-m-d');
$error = null;
$success = null;

// Load students
$students = $pdo->query('SELECT * FROM students ORDER BY name ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? date('Y-m-d');
    $statuses = $_POST['status'] ?? []; // status[student_id] = Present/Absent

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO attendance (student_id, date, status)
                               VALUES (?, ?, ?)
                               ON DUPLICATE KEY UPDATE status = VALUES(status)');
        foreach ($statuses as $sid => $st) {
            $sid = (int)$sid;
            $st = $st === 'Present' ? 'Present' : 'Absent';
            $stmt->execute([$sid, $date, $st]);
        }
        $pdo->commit();
        $success = 'Attendance saved for ' . htmlspecialchars($date);
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = 'Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mark Attendance | Attendance App</title>
<link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
  <h1>Mark Attendance</h1>
  <?php if ($error): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

  <div class="card">
    <form method="get" style="display:flex; gap:12px; align-items:center;">
      <label style="margin:0;">Date
        <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
      </label>
      <button class="btn secondary" type="submit">Load</button>
    </form>
  </div>

  <form method="post" class="card" style="margin-top:16px;">
    <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
    <table>
      <thead>
        <tr><th>Student</th><th>Roll</th><th>Class</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($students as $s): 
          $stmt = $pdo->prepare('SELECT status FROM attendance WHERE student_id=? AND date=?');
          $stmt->execute([$s['id'], $date]);
          $row = $stmt->fetch();
          $current = $row['status'] ?? 'Present';
        ?>
          <tr>
            <td><?php echo htmlspecialchars($s['name']); ?></td>
            <td><?php echo htmlspecialchars($s['roll_no']); ?></td>
            <td><?php echo htmlspecialchars($s['class']); ?></td>
            <td>
              <select name="status[<?php echo $s['id']; ?>]">
                <option value="Present" <?php echo $current === 'Present' ? 'selected' : ''; ?>>Present</option>
                <option value="Absent" <?php echo $current === 'Absent' ? 'selected' : ''; ?>>Absent</option>
              </select>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div style="margin-top:12px;">
      <button type="submit">Save Attendance</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
