<?php
define('DB_HOST',   'localhost');
define('DB_USER',   'root');
define('DB_PASS',   '');
define('DB_NAME',   'simple_banking');
define('BASE_URL',  '/Simple-Banking-Application');
define('APP_NAME',  'Simple Bank');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<!DOCTYPE html><html><head><title>DB Error</title>
    <style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;
    min-height:100vh;background:#f1f5f9;margin:0}.card{background:#fff;padding:40px;
    border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1);max-width:480px;text-align:center}
    h2{color:#dc2626}code{background:#f1f5f9;padding:2px 6px;border-radius:4px}</style>
    </head><body><div class="card">
    <h2>&#9888; Database Connection Failed</h2>
    <p>Make sure MySQL is running and the <code>simple_banking</code> database exists.</p>
    <p>Import <code>database/schema.sql</code> via phpMyAdmin to get started.</p>
    </div></body></html>');
}
