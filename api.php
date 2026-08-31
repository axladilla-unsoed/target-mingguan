<?php
/**
 * API Backend untuk Jadwal Target Mingguan
 * Mendukung JSON input dan x-www-form-urlencoded
 */

define('IS_API_REQUEST', true);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/koneksi.php';

$valid_days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

// Tangani input POST JSON atau Form Data
$raw_input = file_get_contents('php://input');
$json_input = json_decode($raw_input, true);
$data = is_array($json_input) ? $json_input : $_POST;

$action = $_GET['action'] ?? $data['action'] ?? '';

// Helper untuk respons JSON
function jsonResponse($success, $message = '', $extra = [], $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $extra));
    exit;
}

try {
    switch ($action) {
        case 'get_tasks':
        case '':
            // Ambil semua target, urutkan berdasarkan waktu dibuat
            $stmt = $pdo->query("SELECT id, title, day, hours, note, done FROM tasks ORDER BY created_at ASC");
            $rows = $stmt->fetchAll();

            $tasks = array_map(function ($task) {
                return [
                    'id'    => (string)$task['id'],
                    'title' => (string)$task['title'],
                    'day'   => (string)$task['day'],
                    'hours' => (string)($task['hours'] ?? ''),
                    'note'  => (string)($task['note'] ?? ''),
                    'done'  => (bool)$task['done']
                ];
            }, $rows);

            jsonResponse(true, 'Data berhasil dimuat', ['tasks' => $tasks]);
            break;

        case 'create':
            $title = trim($data['title'] ?? '');
            $day   = trim($data['day'] ?? 'Senin');
            $hours = trim($data['hours'] ?? '');
            $note  = trim($data['note'] ?? '');
            $id    = trim($data['id'] ?? '');

            if ($title === '') {
                jsonResponse(false, 'Nama target tidak boleh kosong.', [], 400);
            }

            if (!in_array($day, $valid_days, true)) {
                $day = 'Senin';
            }

            if ($id === '') {
                // Generate ID unik jika tidak dikirim dari client
                if (function_exists('random_bytes')) {
                    $id = bin2hex(random_bytes(16));
                } else {
                    $id = uniqid('task-', true);
                }
            }

            $stmt = $pdo->prepare("INSERT INTO tasks (id, title, day, hours, note, done) VALUES (?, ?, ?, ?, ?, 0)");
            $stmt->execute([$id, $title, $day, $hours, $note]);

            jsonResponse(true, 'Workload berhasil ditambahkan.', [
                'task' => [
                    'id'    => $id,
                    'title' => $title,
                    'day'   => $day,
                    'hours' => $hours,
                    'note'  => $note,
                    'done'  => false
                ]
            ]);
            break;

        case 'toggle_done':
            $id   = trim($data['id'] ?? '');
            $done = !empty($data['done']) ? 1 : 0;

            if ($id === '') {
                jsonResponse(false, 'ID tugas diperlukan.', [], 400);
            }

            $stmt = $pdo->prepare("UPDATE tasks SET done = ? WHERE id = ?");
            $stmt->execute([$done, $id]);

            jsonResponse(true, 'Status tugas diperbarui.', ['id' => $id, 'done' => (bool)$done]);
            break;

        case 'move_day':
            $id  = trim($data['id'] ?? '');
            $day = trim($data['day'] ?? '');

            if ($id === '' || !in_array($day, $valid_days, true)) {
                jsonResponse(false, 'ID tugas atau hari tidak valid.', [], 400);
            }

            $stmt = $pdo->prepare("UPDATE tasks SET day = ? WHERE id = ?");
            $stmt->execute([$day, $id]);

            jsonResponse(true, 'Hari tugas berhasil dipindahkan.', ['id' => $id, 'day' => $day]);
            break;

        case 'delete':
            $id = trim($data['id'] ?? '');

            if ($id === '') {
                jsonResponse(false, 'ID tugas diperlukan.', [], 400);
            }

            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$id]);

            jsonResponse(true, 'Tugas berhasil dihapus.', ['id' => $id]);
            break;

        case 'reset_week':
            // Reset checklist semua tugas menjadi belum selesai (done = 0)
            $pdo->query("UPDATE tasks SET done = 0");
            jsonResponse(true, 'Checklist minggu berhasil direset.');
            break;

        default:
            jsonResponse(false, 'Aksi tidak dikenal.', [], 404);
            break;
    }
} catch (PDOException $e) {
    jsonResponse(false, 'Terjadi kesalahan database: ' . $e->getMessage(), [], 500);
}
