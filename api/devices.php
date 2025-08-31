<?php
session_start();
require_once __DIR__.'/../core/db.php';

// Demo kullanıcı
$user_id = 1;

// JSON header
header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    // Toggle cihaz durumu (POST veya GET)
    if($action === 'toggle'){
        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
        if(!$id) throw new Exception('Geçersiz ID');

        $st = $db->prepare('SELECT state FROM devices WHERE id=? AND user_id=? AND deleted=0');
        $st->execute([$id, $user_id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if($row){
            $newState = $row['state'] ? 0 : 1;
            $st = $db->prepare('UPDATE devices SET state=? WHERE id=? AND user_id=?');
            $st->execute([$newState, $id, $user_id]);
            echo json_encode(['ok'=>true]);
        } else {
            throw new Exception('Cihaz bulunamadı');
        }
        exit;
    }

    // Yeni cihaz ekleme (POST)
    if($action === 'insert'){
        $name = trim($_POST['name'] ?? '');
        $image = trim($_POST['image'] ?? 'cihaz.png');

        $validImages = ['kilit.png','lamba.png','vantilator.png','supurge.png','kamera.png'];
        if(!in_array($image, $validImages)) $image = 'cihaz.png';

        if(!$name) throw new Exception('İsim boş olamaz');

        $st = $db->prepare('INSERT INTO devices (user_id, name, image, state, deleted) VALUES (?, ?, ?, 0, 0)');
        $st->execute([$user_id, $name, $image]);
        echo json_encode(['ok'=>true]);
        exit;
    }

    // Cihaz silme (yumuşak silme)
    if($action === 'delete'){
        $id = intval($_POST['id'] ?? 0);
        if(!$id) throw new Exception('Geçersiz ID');

        $st = $db->prepare('UPDATE devices SET deleted=1 WHERE id=? AND user_id=?');
        $st->execute([$id, $user_id]);
        echo json_encode(['ok'=>true]);
        exit;
    }

    // Cihaz listeleme (GET)
    if($action === 'list'){
        $st = $db->prepare('SELECT * FROM devices WHERE user_id=? AND deleted=0 ORDER BY updated_at DESC');
        $st->execute([$user_id]);
        $devices = $st->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['ok'=>true, 'devices'=>$devices]);
        exit;
    }

    throw new Exception('Geçersiz istek');

} catch(Exception $e){
    echo json_encode(['ok'=>false, 'msg'=>$e->getMessage()]);
}
