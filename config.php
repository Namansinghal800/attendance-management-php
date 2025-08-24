<?php
// Update these to match your MySQL setup
define('DB_HOST', 'localhost');
define('DB_NAME', 'attendance_db'); // <- create this DB in phpMyAdmin
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default is empty password

// App base URL (no trailing slash). Adjust if you use a subfolder/domain.
define('BASE_URL', '/attendance-management-php');

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
