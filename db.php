<?php
// Local XAMPP defaults are retained. On Render, set DB_HOST, DB_NAME, DB_USER, and DB_PASSWORD
// as environment variables in the Web Service dashboard.
$host = getenv('DB_HOST') ?: 'localhost';
$db = getenv('DB_NAME') ?: 'library_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  error_log('Database connection failed: ' . $e->getMessage());
  exit('Database connection failed. Check the hosting database environment variables.');
}
