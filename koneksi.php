<?php
/**
 * Konfigurasi Koneksi Database MySQL menggunakan PDO
 */

$host    = '127.0.0.1';
$db_name = 'target_mingguan';
$db_user = 'root';
$db_pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$db_name};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    if (defined('IS_API_REQUEST') && IS_API_REQUEST === true) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Koneksi ke database gagal: ' . $e->getMessage()
        ]);
        exit;
    }
    
    die("Koneksi ke database gagal: " . $e->getMessage());
}
