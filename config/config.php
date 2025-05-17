<?php
// Khởi tạo session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getDbConnection() {
    $host = 'localhost';
    $dbname = 'ttcn';
     //$dbname = 'fresh_garden';
    $username = 'root';
    $password = '';
    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        error_log('Database Connection Error: ' . $e->getMessage());
        die('Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage());
    }
}
?>