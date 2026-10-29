<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  session_set_cookie_params(['httponly'=>true, 'secure'=>$secure, 'samesite'=>'Lax']);
  session_start();
}
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function require_login() {
  if (empty($_SESSION['user_id']) || empty($_SESSION['user_role'])) { header('Location: login.php'); exit; }
}
function require_admin() {
  require_login();
  if ($_SESSION['user_role'] !== 'admin') { http_response_code(403); exit('Access denied: administrator permission required.'); }
}
function require_student() {
  require_login();
  if ($_SESSION['user_role'] !== 'student') { http_response_code(403); exit('Access denied: student account required.'); }
}
function csrf_token() {
  if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32));
  return $_SESSION['csrf_token'];
}
function check_csrf() {
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    $sent=$_POST['csrf_token']??'';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf_token']??'', $sent)) {
      http_response_code(403); exit('Invalid form token. Go back and try again.');
    }
  }
}
