<?php
session_start();
require_once __DIR__.'/../core/db.php';
$msg = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $st = $db->prepare('SELECT id, password_hash FROM users WHERE email=? LIMIT 1');
    $st->execute([$email]);
    $user = $st->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password_hash'])){
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
        exit;
    } else {
        $msg = "Email veya şifre hatalı.";
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Giriş Yap - Ev Kontrol</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#f0f0f0; font-family:Poppins,sans-serif; display:flex; justify-content:center; align-items:center; height:100vh; }
.card { width:400px; padding:30px; border-radius:20px; box-shadow:0 0 20px rgba(0,0,0,0.1); }
.btn-custom { background:#e83e8c; color:#fff; border:none; }
.btn-custom:hover { background:#c82368; }
.text-link { color:#28a745; text-decoration:none; }
.text-link:hover { text-decoration:underline; }
</style>
</head>
<body>
  <div class="card">
    <h3 class="text-center mb-3">Giriş Yap</h3>
    <?php if($msg) echo "<div class='alert alert-danger'>$msg</div>"; ?>

    <form method="POST">
      <div class="mb-3">
        <input type="email" class="form-control" name="email" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" name="password" placeholder="Şifre" required>
      </div>
      <button type="submit" class="btn btn-custom w-100">Giriş Yap</button>
    </form>

    <!-- Kayıt yönlendirme -->
    <p class="mt-3 text-center">
      Hesabınız yok mu? <a href="register.php" class="text-link">Kayıt Ol</a>
    </p>
  </div>
</body>
</html>
