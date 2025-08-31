<?php
session_start();
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = 1;
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ev Kontrol - Yapılacaklar Listesi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: #f4ecec;
      margin: 0;
      min-height: 100vh;
      padding-top: 70px;
    }
    .container {
      max-width: 1200px;
      width: 100%;
      border-radius: 20px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }

    h1 {
      color: #e83e8c;
      margin-bottom: 30px;
      text-align: center;
    }

    /* Navbar */
    .navbar { padding: 0.5rem 1rem; background-color: #e83e8c; }
    .navbar-brand img { width: 35px; height: 35px; }
    .nav-link { color: #fff !important; padding: 0.5rem 1rem; }
    .nav-link:hover { color: #f0f0f0 !important; }

    /* Görev Kartları */
    .task-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 15px;
      border-radius: 10px;
      margin-bottom: 10px;
      background: #f0f0f0;
      transition: background 0.2s;
    }
    .task-item.done { text-decoration: line-through; background: #d4edda; }
    .task-actions button { margin-left: 5px; }
    .custom-input {
      display: grid;
      grid-template-columns: 1fr 150px 200px 1fr auto;
      gap: 10px;
      margin-bottom: 20px;
    }
    .custom-input input, .custom-input select, .custom-input textarea {
      padding: 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
    }

    /* Buton renkleri */
    .btn-add { background: #ffc107; color: #fff; border: none; }
    .btn-add:hover { background: #e0a800; }
    .btn-status { background: #28a745; color: #fff; border: none; }
    .btn-status:hover { background: #218838; }
    .btn-delete { background: #17a2b8; color: #fff; border: none; }
    .btn-delete:hover { background: #117a8b; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
    <img src="../assets/logo2.png" alt="Logo">
    <span class="ms-2 fw-bold text-white">Ev Kontrol</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Ana Sayfa</a></li>
        <li class="nav-item"><a class="nav-link" href="controlDevice.php">Cihaz Yönetimi</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Ana İçerik -->
<div class="container">
  <div class="back">
      <h1>Yapılacaklar Listesi</h1>

  <!-- Yeni Görev Ekleme -->
  <div class="custom-input">
    <input type="text" id="task-title" placeholder="Görev başlığı">
    <select id="task-category">
      <option value="diğer">Diğer</option>
      <option value="iş">İş</option>
      <option value="ev">Ev</option>
      <option value="alışveriş">Alışveriş</option>
      <option value="sağlık">Sağlık</option>
      <option value="eğitim">Eğitim</option>
      <option value="finans">Finans</option>
    </select>
    <input type="datetime-local" id="task-due">
    <textarea id="task-note" placeholder="Not..." rows="1"></textarea>
    <button class="btn-add" onclick="addTask()">Ekle</button>
  </div>

  <!-- Görev Listesi -->
  <div id="task-list"></div>
  </div>

</div>

<script>
const apiUrl = '../api/tasks.php';

function fetchTasks() {
  fetch(`${apiUrl}?action=list`)
    .then(res => res.json())
    .then(tasks => {
      const list = document.getElementById('task-list');
      list.innerHTML = '';
      tasks.forEach(task => {
        const div = document.createElement('div');
        div.className = `task-item ${task.status}`;
        div.innerHTML = `
          <div>
            <strong>${task.title}</strong> <small>(${task.category})</small>
            ${task.due_at ? `<br><small>Hatırlatma: ${task.due_at}</small>` : ''}
            ${task.note ? `<br><em>${task.note}</em>` : ''}
          </div>
          <div class="task-actions">
            <button class="btn-status btn btn-sm" onclick="toggleStatus(${task.id})">Durum</button>
            <button class="btn-delete btn btn-sm" onclick="deleteTask(${task.id})">Sil</button>
          </div>
        `;
        list.appendChild(div);
      });
    });
}

function addTask() {
  const title = document.getElementById('task-title').value.trim();
  const category = document.getElementById('task-category').value;
  const due_at = document.getElementById('task-due').value;
  const note = document.getElementById('task-note').value;

  if(!title) return alert('Başlık boş olamaz');

  fetch(`${apiUrl}?action=add`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ title, category, due_at, note })
  })
  .then(res => res.json())
  .then(res => {
    if(res.ok) {
      document.getElementById('task-title').value = '';
      document.getElementById('task-due').value = '';
      document.getElementById('task-note').value = '';
      fetchTasks();
    } else {
      alert(res.error || 'Görev eklenemedi');
    }
  });
}

function deleteTask(id) {
  if(!confirm('Bu görevi silmek istediğinize emin misiniz?')) return;
  fetch(`${apiUrl}?action=delete`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id })
  })
  .then(res => res.json())
  .then(res => { if(res.ok) fetchTasks(); });
}

function toggleStatus(id) {
  fetch(`${apiUrl}?action=toggle_status`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id })
  })
  .then(res => res.json())
  .then(res => { if(res.ok) fetchTasks(); });
}

fetchTasks();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
