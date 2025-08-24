<?php
require_once __DIR__ . '/db.php';

// If tables already exist, you can comment out or guard the migration.
$migrations = [
    // users
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    // students
    "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        roll_no VARCHAR(50) NOT NULL UNIQUE,
        class VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    // attendance
    "CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        date DATE NOT NULL,
        status ENUM('Present','Absent') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_student_date (student_id, date),
        CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

$pdo = get_db();
$error = null;
$success = false;

// Run migrations
try {
    foreach ($migrations as $sql) {
        $pdo->exec($sql);
    }
} catch (PDOException $e) {
    $error = $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                $ins->execute([$username, $hash]);
                $success = true;
            }
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup | Attendance App</title>
<link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="container small">
    <h1>Attendance App — Setup</h1>
    <p>This will create required tables and the first admin user.</p>
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert success">Admin user created successfully. You can now <a href="<?php echo BASE_URL; ?>/public/index.php">login</a>.
        <br>For security, delete or rename <code>setup.php</code>.</div>
    <?php endif; ?>

    <form method="post" class="card">
        <label>Admin Username
            <input type="text" name="username" required>
        </label>
        <label>Admin Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Create Admin</button>
    </form>
</div>
</body>
</html>
