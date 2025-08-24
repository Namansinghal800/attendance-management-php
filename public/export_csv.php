<?php
require_once __DIR__ . '/../auth/require_login.php';
require_once __DIR__ . '/../db.php';
$pdo = get_db();

$filter_student = (int)($_GET['student_id'] ?? 0);
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-t');

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

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendance_export.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Date', 'Name', 'Roll No', 'Class', 'Status']);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($out, [$row['date'], $row['name'], $row['roll_no'], $row['class'], $row['status']]);
}
fclose($out);
exit;
?>
