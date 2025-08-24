Attendance Management System (PHP + MySQL)
=========================================

Quick Start (XAMPP/WAMP)
------------------------
1) Copy the `attendance-management-php` folder to your web root:
   - Windows (XAMPP): C:\xampp\htdocs\attendance-management-php
   - macOS (MAMP): /Applications/MAMP/htdocs/attendance-management-php
   - Linux (LAMP): /var/www/html/attendance-management-php

2) Create a MySQL database, e.g. `attendance_db`.

3) Open `config.php` and set your DB credentials (DB_HOST, DB_NAME, DB_USER, DB_PASS).

4) Visit `http://localhost/attendance-management-php/setup.php` in your browser:
   - This runs the database migration (creates tables).
   - It lets you create the first admin user (username & password of your choice).
   - After successful setup, delete or rename `setup.php` for security.

5) Login at `http://localhost/attendance-management-php/public/index.php` with the admin you created.

Default Tech
------------
- Frontend: HTML, CSS, minimal JavaScript
- Backend: PHP (PDO, prepared statements)
- DB: MySQL

Main Features
-------------
- Secure login (password_hash/password_verify)
- Manage students (add/edit/delete)
- Mark attendance by date
- View attendance by student or by date
- Export attendance to CSV

Folder Structure
----------------
attendance-management-php/
├── assets/
│   ├── styles.css
│   └── script.js
├── auth/
│   ├── login.php
│   ├── logout.php
│   └── require_login.php
├── includes/
│   ├── header.php
│   └── footer.php
├── public/
│   ├── index.php          (redirects to dashboard or login)
│   ├── dashboard.php
│   ├── students.php
│   ├── attendance.php
│   ├── attendance_view.php
│   └── export_csv.php
├── config.php              (DB credentials)
├── db.php                  (PDO connection helper)
└── setup.php               (one-time DB migration + admin bootstrap)

Security Notes
--------------
- Always delete/rename `setup.php` after first run.
- Change DB credentials and never commit secrets publicly.
- This is a starter app—extend validation and authorization as needed.
