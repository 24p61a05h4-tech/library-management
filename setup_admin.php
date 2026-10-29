<?php
require 'db.php';
$message = ''; $error = '';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(80) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    $count = (int)$pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
        $stmt->execute(['admin', password_hash('Admin@123', PASSWORD_DEFAULT)]);
        $message = 'Admin account created. Username: admin · Password: Admin@123';
    } else { $message = 'An admin account already exists. No changes were made. If you know your credentials, go to login.'; }
} catch (Throwable $e) { $error = 'Setup failed. Import database.sql and check your database connection.'; }
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Setup · LibraryDesk</title><link rel="stylesheet" href="style.css"></head><body class="login-page"><main class="panel" style="width:min(520px,100%);padding:30px"><div class="brand" style="padding:0;margin-bottom:25px;color:var(--ink)"><span class="brand-mark">📚</span>LibraryDesk setup</div><?php if($message): ?><div class="notice"><?=h($message)?></div><p style="font-size:13px;line-height:1.7">Next, sign in with the credentials above. For safety, delete <code>setup_admin.php</code> from this folder after setup, and change the demo password before public deployment.</p><?php else: ?><div class="error"><?=h($error)?></div><?php endif; ?><a class="btn" style="display:inline-block;margin-top:12px" href="login.php">Go to admin login →</a></main></body></html>
