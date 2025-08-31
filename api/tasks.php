<?php
session_start();
require_once __DIR__.'/../core/db.php';

// Giriş yapılmamışsa çık
if(!isset($_SESSION['user_id'])){
    echo json_encode(['ok'=>false,'msg'=>'Giriş yapılmamış']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true);

// Görev listeleme
if($action === 'list'){
    $st = $db->prepare('SELECT * FROM tasks WHERE user_id=? AND deleted=0 ORDER BY created_at DESC');
    $st->execute([$user_id]);
    echo json_encode($st->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Yeni görev ekleme
if($action === 'add'){
    $title = trim($data['title'] ?? '');
    $category = $data['category'] ?? 'diğer';
    $due_at = !empty($data['due_at']) ? $data['due_at'] : null; 
    $note = $data['note'] ?? null;

    if($title === ''){
        echo json_encode(['ok'=>false,'error'=>'Başlık boş olamaz']);
        exit;
    }

    $st = $db->prepare('INSERT INTO tasks (user_id, title, category, due_at, note, status, deleted) VALUES (?,?,?,?,?, "pending", 0)');
    $st->execute([$user_id,$title,$category,$due_at,$note]);

    echo json_encode(['ok'=>true,'task_id'=>$db->lastInsertId()]);
    exit;
}

// Görev silme (yumuşak silme → deleted=1)
if($action === 'delete'){
    $id = (int)($data['id'] ?? 0);
    $st = $db->prepare('UPDATE tasks SET deleted=1 WHERE id=? AND user_id=?');
    $st->execute([$id,$user_id]);
    echo json_encode(['ok'=>true]);
    exit;
}

// Görev durumu değiştirme
if($action === 'toggle_status'){
    $id = (int)($data['id'] ?? 0);
    $st = $db->prepare('SELECT status FROM tasks WHERE id=? AND user_id=? AND deleted=0');
    $st->execute([$id,$user_id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if($row){
        $newStatus = ($row['status'] === 'pending') ? 'done' : 'pending';
        $st = $db->prepare('UPDATE tasks SET status=? WHERE id=? AND user_id=?');
        $st->execute([$newStatus,$id,$user_id]);
        echo json_encode(['ok'=>true,'new_status'=>$newStatus]);
    } else {
        echo json_encode(['ok'=>false,'msg'=>'Görev bulunamadı']);
    }
    exit;
}
