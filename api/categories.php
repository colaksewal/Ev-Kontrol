<?php
require_once __DIR__.'/../core/db.php';
session_start();
header('Content-Type: application/json');

// tasks tablosundan category enum değerlerini çek
$stmt = $db->query("SHOW COLUMNS FROM tasks LIKE 'category'");
$row = $stmt->fetch();

if ($row) {
    preg_match("/^enum\('(.*)'\)$/", $row['Type'], $matches);
    $categories = explode("','", $matches[1]);
    echo json_encode($categories);
} else {
    echo json_encode([]);
}
