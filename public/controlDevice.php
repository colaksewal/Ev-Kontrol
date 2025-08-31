<?php
session_start();
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = 1;
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cihaz Yönetimi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f8f9fa;
    margin: 0;
    min-height: 100vh;
}
.container { max-width: 1200px; margin: 5px auto; }

/* Navbar */
.navbar { padding: 0.5rem 1rem; }
.navbar-brand img { width: 35px; height: 35px; }
.nav-link { color: #fff !important; }
.nav-link:hover { color: #e0e0e0 !important; }

/* Sayfa Başlığı */
h1.page-title { text-align: center; margin-bottom: 20px; color: #333; }

/* Cihaz Kartları */
.devices-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    max-height: 500px;
    overflow-y: auto;
}
.device-card {
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    background: #fff;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}
.device-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}
.device-card img { width: 80px; height: 80px; object-fit: contain; margin-bottom: 10px; }
.device-card h3 { margin-bottom: 10px; font-size: 1.2rem; text-align: center; }

/* Butonlar */
.toggle-btn {
    border: none;
    border-radius: 8px;
    padding: 8px 20px;
    font-weight: bold;
    cursor: pointer;
    margin-bottom: 10px;
    width: 100%;
    color: #fff;
    background: #28a745;
    opacity: 0.8;
}
.toggle-btn.on { opacity: 1; }

.delete-btn {
    background: #ffc107;
    color: #000;
    border: none;
    border-radius: 8px;
    padding: 8px 20px;
    cursor: pointer;
    font-weight: bold;
    width: 100%;
}
.delete-btn:hover { background: #e0a800; }

/* Modal */
.custom-modal {
    background: #17a2b8;
    color: #fff;
    border-radius: 12px;
}
.custom-modal .modal-body { background: #17a2b8; }
.custom-modal input, .custom-modal select { border-radius: 6px; }

@media (max-width: 768px) { .devices-container { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px) { .devices-container { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #e83e8c;">
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
        <li class="nav-item"><a class="nav-link" href="controlTasks.php">Görev Yönetimi</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Sayfa Başlığı -->
<div class="container">
    <h1 class="page-title">Cihaz Yönetimi</h1>

    <!-- Cihaz Ekle Butonu -->
    <div class="text-end mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDeviceModal">+ Cihaz Ekle</button>
    </div>

    <!-- Cihaz Kartları -->
    <div class="devices-container" id="devicesContainer"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content custom-modal">
      <div class="modal-header">
        <h5 class="modal-title" id="addDeviceModalLabel">Yeni Cihaz Ekle</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        <form id="addDeviceForm">
          <input type="text" name="name" placeholder="Cihaz İsmi" class="form-control mb-2" required>
          <select name="image" id="categorySelect" class="form-control mb-2">
            <option value="lamba.png">Lamba</option>
            <option value="kilit.png">Kilit</option>
            <option value="vantilator.png">Vantilatör</option>
            <option value="supurge.png">Süpürge</option>
            <option value="kamera.png">Kamera</option>
          </select>
          <div class="text-center mb-2">
            <img id="previewImage" src="../assets/lamba.png" style="width:80px; height:80px;">
          </div>
          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-warning fw-bold" data-bs-dismiss="modal">Kapat</button>
            <button type="submit" class="btn btn-success fw-bold">Ekle</button>
          </div>
        </form>
        <div id="addDeviceMsg" style="margin-top:10px;"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Önizleme görseli
document.getElementById('categorySelect').addEventListener('change', function(){
    document.getElementById('previewImage').src = '../assets/' + this.value;
});

// Cihazları yükleme
async function loadDevices() {
    try {
        const res = await fetch('../api/devices.php');
        const devices = await res.json();
        const container = document.getElementById('devicesContainer');
        container.innerHTML = '';

        devices.forEach(device => {
            const card = document.createElement('div');
            card.classList.add('device-card');
            const img = device.image || 'cihaz.png';
            const name = device.name || 'Cihaz ' + device.id;

            card.innerHTML = `
                <img src="../assets/${img}" alt="${name}">
                <h3>${name}</h3>
                <button class="toggle-btn ${device.state == 1 ? 'on' : ''}" onclick="toggleDevice(${device.id}, this)">
                    ${device.state == 1 ? 'Açık' : 'Kapalı'}
                </button>
                <button class="delete-btn" onclick="deleteDevice(${device.id}, this)">Sil</button>
            `;
            container.appendChild(card);
        });
    } catch(err) {
        document.getElementById('devicesContainer').innerHTML = '<p style="color:red; text-align:center;">Cihazlar yüklenemedi!</p>';
    }
}

// Toggle cihaz durumu
async function toggleDevice(id, btn) {
    btn.disabled = true;
    const formData = new FormData();
    formData.append('id', id);
    const res = await fetch('../api/devices.php?action=toggle', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.ok){
        btn.classList.toggle('on');
        btn.textContent = btn.classList.contains('on') ? 'Açık' : 'Kapalı';
    }
    btn.disabled = false;
}

// Cihaz ekleme
document.getElementById('addDeviceForm').addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('../api/devices.php?action=insert', { method: 'POST', body: formData });
    const data = await res.json();
    const msg = document.getElementById('addDeviceMsg');

    if(data.ok){
        msg.innerHTML = '<span style="color:yellow;">Cihaz eklendi!</span>';
        e.target.reset();
        document.getElementById('previewImage').src = '../assets/lamba.png';
        loadDevices();
        bootstrap.Modal.getInstance(document.getElementById('addDeviceModal')).hide();
    } else {
        msg.innerHTML = '<span style="color:red;">Cihaz eklenemedi!</span>';
    }
});

// Cihaz silme
async function deleteDevice(id, btn){
    if(!confirm('Bu cihazı silmek istediğinize emin misiniz?')) return;
    btn.disabled = true;
    const formData = new FormData();
    formData.append('id', id);
    const res = await fetch('../api/devices.php?action=delete', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.ok) btn.parentElement.remove();
    else { alert('Cihaz silinemedi!'); btn.disabled = false; }
}

window.onload = loadDevices;
</script>
</body>
</html>
