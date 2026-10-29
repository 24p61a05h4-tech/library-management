<?php
require 'db.php'; require 'auth.php'; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  check_csrf(); $username=trim($_POST['username']??''); $password=$_POST['password']??''; $role=$_POST['account_type']??'student';
  if (!in_array($role,['admin','student'],true)) $error='Choose a valid account type.';
  else {
    $table=$role==='admin'?'admin_users':'student_users';
    $s=$pdo->prepare("SELECT * FROM {$table} WHERE username=? LIMIT 1"); $s->execute([$username]); $u=$s->fetch();
    if ($u && password_verify($password,$u['password_hash'])) {
      session_regenerate_id(true); $_SESSION['user_role']=$role; $_SESSION['user_id']=(int)$u['id']; $_SESSION['admin_username']=$u['username'];
      if($role==='student') {
        if('library_management'==='student_management') $_SESSION['student_id']=(int)$u['student_id'];
      }
      header('Location: index.php'); exit;
    } else $error='Incorrect username/password or account type.';
  }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login · LibraryDesk</title><link rel="stylesheet" href="style.css"></head><body class="auth-bg"><main class="auth-card"><div class="auth-logo">📚</div><p class="eyebrow">LibraryDesk · SECURE SIGN IN</p><h1>Welcome back</h1><p class="muted">Sign in to manage or browse the library.</p><?php if(isset($_GET['registered'])):?><div class="notice"><?=h(ucfirst($_GET['registered']))?> account created. Please sign in.</div><?php endif;?><?php if($error):?><div class="error"><?=h($error)?></div><?php endif;?><form method="post" class="stack"><input type="hidden" name="csrf_token" value="<?=h(csrf_token())?>"><label>Account type<select name="account_type"><option value="admin">Administrator</option><option value="student">Student</option></select></label><label>Username<input name="username" required autocomplete="username"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button class="btn full">Sign in</button></form><p class="muted center"><a href="signup.php?type=student">Create student account</a></p><p class="muted center"><a href="signup.php?type=admin">First-time admin setup</a></p></main></body></html>
