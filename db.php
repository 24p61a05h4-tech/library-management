<?php
$host='localhost'; $db='library_db'; $user='root'; $pass='';
try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES=>false
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  exit('Database connection failed. Check db.php and import database.sql into library_db.');
}
