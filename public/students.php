<?php
require_once __DIR__ . '/../auth/require_login.php';
require_once __DIR__ . '/../db.php';
$pdo = get_db();
$error = null;
$success = null;

// Create / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $roll_no = trim($_POST['roll_no'] ?? '');
        $class = trim($_POST['class'] ?? '');
        if ($name === '' || $roll_no === '' || $class === '') {
            $error = 'All fields are required.';
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO students (name, roll_no, class) VALUES (?, ?, ?)');
                $stmt->execute([$name, $roll_no, $class]);
                $success = 'Student added.';
            } catch (PDOException $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $roll_no = trim($_POST['roll_no'] ?? '');
        $class = trim($_POST['class'] ?? '');
        if ($id <= 0 || $name === '' || $roll_no === '' || $class === '') {
            $error = 'All fields are required.';
        } else {
            try {
                $stmt = $pdo->prepare('UPDATE students SET name=?, roll_no=?, class=? WHERE id=?');
                $stmt->execute([$name, $roll_no, $class, $id]);
                $success = 'Student updated.';
            } catch (PDOException $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    }
}

// Delete
if (($_GET['action'] ?? '') === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM students WHERE id=?');
        $stmt->execute([$id]);
        $success = 'Student deleted.';
    }
}

$students = $pdo->query('SELECT * FROM students ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Students | Attendance App</title>
<link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
  <h1>Students</h1>
  <?php if ($error): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

  <div class="grid two">
    <div class="card">
      <h2>Add Student</h2>
      <form method="post">
        <input type="hidden" name="action" value="create">
        <label>Name
          <input type="text" name="name" required>
        </label>
        <label>Roll No
          <input type="text" name="roll_no" required>
        </label>
        <label>Class
          <input type="text" name="class" required placeholder="e.g. CS-A, CS-3rd Sem">
        </label>
        <button type="submit">Add</button>
      </form>
    </div>

    <div class="card">
      <h2>All Students</h2>
      <table>
        <thead>
          <tr><th>ID</th><th>Name</th><th>Roll</th><th>Class</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($students as $s): ?>
            <tr>
              <td><?php echo $s['id']; ?></td>
              <td><?php echo htmlspecialchars($s['name']); ?></td>
              <td><?php echo htmlspecialchars($s['roll_no']); ?></td>
              <td><?php echo htmlspecialchars($s['class']); ?></td>
              <td>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                  <input type="text" name="name" value="<?php echo htmlspecialchars($s['name']); ?>" required>
                  <input type="text" name="roll_no" value="<?php echo htmlspecialchars($s['roll_no']); ?>" required>
                  <input type="text" name="class" value="<?php echo htmlspecialchars($s['class']); ?>" required>
                  <button type="submit" class="btn secondary">Save</button>
                </form>
                <a class="btn danger" href="?action=delete&id=<?php echo $s['id']; ?>" onclick="return confirm('Delete this student?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
