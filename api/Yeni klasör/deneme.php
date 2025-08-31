<?php
require_once __DIR__.'/../core/db.php';
require_once __DIR__.'/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

header('Content-Type: application/json');

// JWT kontrol
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if(!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)){
    echo json_encode(['ok'=>false,'msg'=>'Token eksik']);
    exit;
}

$token = $matches[1];
$secretKey  = 'SECRET_KEY_BURAYA_DEĞİŞTİR';

try{
    $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    $user_id = $decoded->uid; // artık demo kullanıcı yok
}catch(Exception $e){
    echo json_encode(['ok'=>false,'msg'=>'Geçersiz veya süresi dolmuş token']);
    exit;
}

// Action al
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING)
          ?? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING)
          ?? '';

try {
    switch ($action) {
        case 'toggle':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)
                  ?? filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id) throw new Exception('Geçersiz ID');

            $st = $db->prepare('SELECT state FROM devices WHERE id=? AND user_id=? AND deleted=0 LIMIT 1');
            $st->execute([$id, $user_id]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if(!$row) throw new Exception('Cihaz bulunamadı');

            $newState = $row['state'] ? 0 : 1;
            $st = $db->prepare('UPDATE devices SET state=?, updated_at=NOW() WHERE id=? AND user_id=?');
            $st->execute([$newState, $id, $user_id]);
            echo json_encode(['ok'=>true]);
            exit;

        case 'insert':
            $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
            $image = trim(filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING)) ?: 'cihaz.png';

            $validImages = ['kilit.png','lamba.png','vantilator.png','supurge.png','kamera.png'];
            if(!in_array($image, $validImages)) $image = 'cihaz.png';
            if(!$name) throw new Exception('İsim boş olamaz');

            $st = $db->prepare('INSERT INTO devices (user_id, name, image, state, deleted, updated_at) VALUES (?, ?, ?, 0, 0, NOW())');
            $st->execute([$user_id, $name, $image]);
            echo json_encode(['ok'=>true]);
            exit;

        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if(!$id) throw new Exception('Geçersiz ID');

            $st = $db->prepare('UPDATE devices SET deleted=1, updated_at=NOW() WHERE id=? AND user_id=?');
            $st->execute([$id, $user_id]);
            echo json_encode(['ok'=>true]);
            exit;

        case 'list':
            $st = $db->prepare('SELECT id, name, image, state, updated_at FROM devices WHERE user_id=? AND deleted=0 ORDER BY updated_at DESC');
            $st->execute([$user_id]);
            $devices = $st->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['ok'=>true, 'devices'=>$devices]);
            exit;

        default:
            throw new Exception('Geçersiz istek');
    }
}catch(Exception $e){
    echo json_encode(['ok'=>false,'msg'=>$e->getMessage()]);
}
