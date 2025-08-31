<?php
session_start();
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ev Kontrol - Landing Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      margin: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #f0f0f0ff;
    }
    .container { display: flex; width: 100%; max-width: 1200px; height: 90vh; gap: 30px; }
    .left-side { flex: 1; display: flex; flex-direction: column; justify-content: space-between; height: 100%; }
    .left-side h1 { font-size: 2.5rem; font-weight: bold; }
    .highlight { color: #e83e8c; }
    .custom-btn { background-color: #e83e8c; color: #fff; border: none; padding: 14px 24px; border-radius: 10px; font-size: 1.2rem; margin-bottom: 15px; width: 100%; }
    .custom-btn:hover { background-color: #c82368; }
    .right-side { flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; height: 100%; }
    .left-col { display: grid; grid-template-rows: 1.5fr 0.5fr; gap: 20px; height: 100%; }
    .right-col { display: grid; grid-template-rows: 1fr 1fr; gap: 20px; height: 100%; }
    .device-box { border-radius: 20px; display: flex; justify-content: center; align-items: center; overflow: hidden; width: 100%; height: 100%; }
    .device-box img { width: 70%; height: 70%; object-fit: contain; transform: scale(1); }
    @media (max-width: 768px) { .container { flex-direction: column; height: auto; } .right-side { grid-template-columns: 1fr 1fr; grid-template-rows: auto auto; } .left-side h1 { font-size: 2rem; } }
  </style>
</head>
<body>

<div class="container">
  <!-- Sol Taraf -->
  <div class="left-side">
    <div>
      <div class="d-flex align-items-center mb-4">
        <img src="../assets/logo.png" alt="Logo" style="height:40px; margin-right:10px;">
        <span class="logo" style="font-weight:bold; font-size:1.4rem; color:#e83e8c;">Ev Kontrol</span>
      </div>
      <h1 class="mt-5">Evinizi <span class="highlight">Avucunuzun</span><br><span class="highlight">İçine</span> Taşıyın</h1>
      <p class="mt-5" style="font-weight: bold;">
      Akıllı cihazlarınızı tek bir yerden yönetin, otomasyonlar oluşturun ve ev işlerinizi kolayca planlayın.
      <br><br>
      <span style="color:#e83e8c;">⚠ Bu proje demo sürümüdür. Kullanıcı kayıtları ve işlemler gerçek değildir.</span>
      </p>
    </div>
    
    <div>
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="controlDevice.php" class="btn custom-btn">Cihaz Yönetimi</a>
        <a href="controlTasks.php" class="btn custom-btn">Yapılacaklar Listesi</a>
        <a href="logout.php" class="btn custom-btn" style="background:#6c757d;">Çıkış Yap</a>
      <?php else: ?>
        <a href="login.php" class="btn custom-btn">Giriş Yap</a>
        <a href="register.php" class="btn custom-btn" style="background:#28a745;">Kayıt Ol</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Sağ Taraf -->
  <div class="right-side">
    <div class="left-col">
      <div class="device-box" style="background:#e83e8c;">
        <img src="../assets/kamera.png" alt="Cihaz 1">
      </div>
      <div class="device-box" style="background:#ffc107;">
        <img src="../assets/supurge.png" alt="Cihaz 2">
      </div>
    </div>
    <div class="right-col">
      <div class="device-box" style="background:#28a745;">
        <img src="../assets/cihaz.png" alt="Cihaz 3">
      </div>
      <div class="device-box" style="background:#17a2b8;">
        <img src="../assets/hoparlor.png" alt="Cihaz 4">
      </div>
    </div>
  </div>
</div>

</body>
</html>
