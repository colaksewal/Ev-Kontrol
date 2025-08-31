<?php
session_start();
require_once __DIR__.'/../core/db.php';
$msg = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Mevcut kullanıcı kontrolü
    $st = $db->prepare('SELECT id FROM users WHERE email=?');
    $st->execute([$email]);
    if($st->fetch()){
        $msg = "Bu email zaten kayıtlı.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $st = $db->prepare('INSERT INTO users (name, email, password_hash) VALUES (?,?,?)');
        $st->execute([$name, $email, $hash]);
        $_SESSION['user_id'] = $db->lastInsertId();
        header('Location: index.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kayıt Ol - Ev Kontrol</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#f0f0f0; font-family:Poppins,sans-serif; display:flex; justify-content:center; align-items:center; height:100vh; }
.card { width:400px; padding:30px; border-radius:20px; box-shadow:0 0 20px rgba(0,0,0,0.1); }
.btn-custom { background:#e83e8c; color:#fff; border:none; }
.btn-custom:hover { background:#c82368; }
</style>
</head>
<body>
  <div class="card">
    <h3 class="text-center mb-3">Kayıt Ol</h3>
    <?php if($msg) echo "<div class='alert alert-danger'>$msg</div>"; ?>
    <form method="POST">
      <div class="mb-3">
        <input type="text" class="form-control" name="name" placeholder="Ad Soyad" required>
      </div>
      <div class="mb-3">
        <input type="email" class="form-control" name="email" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" name="password" placeholder="Şifre" required>
      </div>
      <button type="submit" class="btn btn-custom w-100">Kayıt Ol</button>
    </form>
  </div>
</body>
</html>
