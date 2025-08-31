<?php
$host = 'db.fr-pari1.bengt.wasmernet.com';
$port = 10272;
$dbname = 'smarthome';
$user = '4b1493d17ed380008f49b5efeb4e';
$pass = '068b4b14-93d2-70d6-8000-99f755139ae8'; // Buraya şifreni yaz

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $db = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        $options
    );
    echo "DB bağlantısı başarılı!";
} catch (PDOException $e) {
    die("DB bağlantısı başarısız: " . $e->getMessage());
}
