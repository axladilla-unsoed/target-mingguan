<?php
/**
 * Konfigurasi Koneksi Database MySQL dengan pembacaan file .env
 */

if (!function_exists('loadEnv')) {
    /**
     * Fungsi sederhana untuk membaca dan memuat file .env ke environment PHP
     */
    function loadEnv($filePath) {
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            // Lewati baris komentar atau baris kosong
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Pisahkan key dan value berdasarkan tanda '=' pertama
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $val = trim($parts[1]);

                // Hapus tanda kutip jika ada (" atau ')
                if (
                    (str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                    (str_starts_with($val, "'") && str_ends_with($val, "'"))
                ) {
                    $val = substr($val, 1, -1);
                }

                if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                    putenv(sprintf('%s=%s', $key, $val));
                    $_ENV[$key] = $val;
                    $_SERVER[$key] = $val;
                }
            }
        }
    }
}

if (!function_exists('env')) {
    /**
     * Helper untuk mengambil nilai environment variable dengan nilai default
     */
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }

        if ($value === null) {
            return $default;
        }

        // Konversi tipe data boolean
        $lower = strtolower((string)$value);
        if ($lower === 'true' || $lower === '(true)') return true;
        if ($lower === 'false' || $lower === '(false)') return false;
        if ($lower === 'null' || $lower === '(null)') return null;

        return $value;
    }
}

// Muat konfigurasi dari file .env di direktori yang sama jika ada
loadEnv(__DIR__ . '/.env');

// Ambil konfigurasi database dari .env atau gunakan default
$host    = env('DB_HOST', '127.0.0.1');
$port    = env('DB_PORT', '3306');
$db_name = env('DB_DATABASE', 'target_mingguan');
$db_user = env('DB_USERNAME', 'root');
$db_pass = env('DB_PASSWORD', '');
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};port={$port};dbname={$db_name};charset={$charset}";

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
