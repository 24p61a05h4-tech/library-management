<?php
require 'db.php'; require 'auth.php';
$error=''; $kind=$_GET['type']??'admin'; if(!in_array($kind,['admin','student'],true)) $kind='admin';
if($_SERVER['REQUEST_METHOD']==='POST') {
  check_csrf(); $kind=$_POST['account_type']??'student'; $username=trim($_POST['username']??''); $password=$_POST['password']??''; $confirm=$_POST['confirm_password']??'';
  $invite=trim($_POST['admin_invite_code']??'');
  if(!in_array($kind,['admin','student'],true)) $error='Choose a valid account type.';
  elseif(!preg_match('/^[A-Za-z0-9_.-]{3,80}$/',$username)) $error='Username must be 3–80 characters using letters, numbers, dots, dashes or underscores.';
  elseif(strlen($password)<10) $error='Use a password with at least 10 characters.';
  elseif($password!==$confirm) $error='Passwords do not match.';
  else {
    try {
      if($kind==='admin') {
        if($invite==='') throw new RuntimeException('Enter the one-time invitation code supplied by an existing administrator.');
        $pdo->beginTransaction();
        $q=$pdo->prepare('SELECT * FROM admin_invites WHERE code_hash=? AND used_at IS NULL AND expires_at>NOW() FOR UPDATE');
        $q->execute([hash('sha256',$invite)]); $inv=$q->fetch();
        if(!$inv) throw new RuntimeException('That invitation code is invalid, expired, or already used. Ask an existing administrator to generate a new one.');
        $s=$pdo->prepare('INSERT INTO admin_users(username,password_hash) VALUES(?,?)');
        $s->execute([$username,password_hash($password,PASSWORD_DEFAULT)]); $newId=(int)$pdo->lastInsertId();
        $u=$pdo->prepare('UPDATE admin_invites SET used_at=NOW(), used_by=? WHERE id=? AND used_at IS NULL'); $u->execute([$newId,$inv['id']]);
        if($u->rowCount()!==1) throw new RuntimeException('This invitation was already used. Ask for a new one.');
        $pdo->commit();
      } else {
        $s=$pdo->prepare('INSERT INTO student_users(username,password_hash) VALUES(?,?)'); $s->execute([$username,password_hash($password,PASSWORD_DEFAULT)]);
      }
      header('Location: login.php?registered='.rawurlencode($kind)); exit;
    } catch(RuntimeException $e) { if($pdo->inTransaction())$pdo->rollBack(); $error=$e->getMessage(); }
      catch(PDOException $e) { if($pdo->inTransaction())$pdo->rollBack(); $error='Could not create account. That username may already be in use, or the invitations table may need importing from database.sql.'; }
  }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Signup · LibraryDesk</title><link rel="stylesheet" href="style.css"></head><body class="auth-bg"><main class="auth-card"><div class="auth-logo">📚</div><p class="eyebrow">LIBRARYDESK · ACCOUNT SETUP</p><h1>Create an account</h1><p class="muted">Faculty admins need a single-use invitation from an existing admin. Students can register normally.</p>
<?php if($error):?><div class="error"><?=h($error)?></div><?php endif;?>
<form method="post" class="stack"><input type="hidden" name="csrf_token" value="<?=h(csrf_token())?>"><label>Account type<select name="account_type" onchange="location.href='signup.php?type='+this.value"><option value="admin" <?=$kind==='admin'?'selected':''?>>Faculty administrator</option><option value="student" <?=$kind==='student'?'selected':''?>>Student</option></select></label>
<?php if($kind==='admin'):?><label>One-time admin invitation code<input type="password" name="admin_invite_code" required autocomplete="off" placeholder="Code from an existing admin"></label><?php endif;?>
<label>Username<input name="username" required minlength="3" maxlength="80" autocomplete="username" placeholder="Choose a username"></label><label>Password<input type="password" name="password" required minlength="10" autocomplete="new-password" placeholder="At least 10 characters"></label><label>Confirm password<input type="password" name="confirm_password" required minlength="10" autocomplete="new-password"></label><button class="btn full"><?=$kind==='admin'?'Create faculty admin account':'Create student account'?></button></form><p class="muted center">Already have an account? <a href="login.php">Sign in</a></p></main></body></html>
