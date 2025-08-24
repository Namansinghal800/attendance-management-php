<?php
require_once __DIR__ . '/../auth/require_login.php';
require_once __DIR__ . '/../db.php';
$pdo = get_db();

$filter_student = (int)($_GET['student_id'] ?? 0);
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-t');

// Load students for filter dropdown
$students = $pdo->query('SELECT id, name FROM students ORDER BY name ASC')->fetchAll();

$params = [];
$sql = "SELECT a.date, s.name, s.roll_no, s.class, a.status
        FROM attendance a
        JOIN students s ON s.id = a.student_id
        WHERE 1=1";
if ($filter_student > 0) { $sql .= " AND s.id = ?"; $params[] = $filter_student; }
if ($from) { $sql .= " AND a.date >= ?"; $params[] = $from; }
if ($to) { $sql .= " AND a.date <= ?"; $params[] = $to; }
$sql .= " ORDER BY a.date DESC, s.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Simple stats
$total = count($rows);
$present = 0; $absent = 0;
foreach ($rows as $r) {
    if ($r['status'] === 'Present') $present++; else $absent++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Reports | Attendance App</title>
<link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
  <h1>Attendance Reports</h1>

  <div class="grid three" style="margin-bottom:16px;">
    <div class="kpi"><div><div class="muted">Records</div><div class="big"><?php echo $total; ?></div></div><div>📄</div></div>
    <div class="kpi"><div><div class="muted">Present</div><div class="big"><?php echo $present; ?></div></div><div>✅</div></div>
    <div class="kpi"><div><div class="muted">Absent</div><div class="big"><?php echo $absent; ?></div></div><div>❌</div></div>
  </div>

  <div class="card">
    <form method="get" class="grid two">
      <label>Student
        <select name="student_id">
          <option value="0">All</option>
          <?php foreach ($students as $s): ?>
            <option value="<?php echo $s['id']; ?>" <?php echo $filter_student === (int)$s['id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>From
        <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>">
      </label>
      <label>To
        <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>">
      </label>
      <div style="align-self:end;">
        <button type="submit" class="btn secondary">Apply</button>
        <a class="btn" href="export_csv.php?student_id=<?php echo $filter_student; ?>&from=<?php echo urlencode($from); ?>&to=<?php echo urlencode($to); ?>">Export CSV</a>
      </div>
    </form>
  </div>

  <div class="card" style="margin-top:16px;">
    <table>
      <thead>
        <tr><th>Date</th><th>Student</th><th>Roll</th><th>Class</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['date']); ?></td>
            <td><?php echo htmlspecialchars($r['name']); ?></td>
            <td><?php echo htmlspecialchars($r['roll_no']); ?></td>
            <td><?php echo htmlspecialchars($r['class']); ?></td>
            <td><?php echo $r['status'] === 'Present' ? '✅ Present' : '❌ Absent'; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
