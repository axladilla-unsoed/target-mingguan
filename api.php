<?php
/**
 * API Backend untuk Jadwal Target Mingguan
 * Mendukung autentikasi password statis .env, manajemen workload,
 * pencatatan capaian mingguan, dan tracking produktivitas ala GitHub.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('IS_API_REQUEST', true);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/koneksi.php';

// Pastikan tabel weekly_history tersedia di database
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `weekly_history` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `year` INT NOT NULL,
          `week_number` INT NOT NULL,
          `week_label` VARCHAR(100) NOT NULL,
          `start_date` DATE NOT NULL,
          `end_date` DATE NOT NULL,
          `total_tasks` INT NOT NULL DEFAULT 0,
          `completed_tasks` INT NOT NULL DEFAULT 0,
          `completion_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
          `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          UNIQUE KEY `unique_year_week` (`year`, `week_number`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
} catch (PDOException $e) {
    // Abaikan jika tabel sudah ada atau izin terbatas
}

$valid_days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
$indonesian_months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

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

// Password statis dari .env (default: admin123)
$static_password = (string)env('APP_PASSWORD', 'admin123');

try {
    // ==========================================
    // 1. ENDPOINT AUTENTIKASI PUBLIK
    // ==========================================

    if ($action === 'login') {
        $password = trim((string)($data['password'] ?? ''));
        if ($password === '') {
            jsonResponse(false, 'Password tidak boleh kosong.', ['authenticated' => false], 400);
        }

        if ($password === $static_password) {
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            jsonResponse(true, 'Login berhasil.', ['authenticated' => true]);
        } else {
            jsonResponse(false, 'Password yang Anda masukkan salah.', ['authenticated' => false], 401);
        }
    }

    if ($action === 'logout') {
        $_SESSION['logged_in'] = false;
        unset($_SESSION['logged_in'], $_SESSION['login_time']);
        session_destroy();
        jsonResponse(true, 'Berhasil keluar.', ['authenticated' => false]);
    }

    if ($action === 'check_auth') {
        jsonResponse(true, 'Status autentikasi.', [
            'authenticated' => !empty($_SESSION['logged_in'])
        ]);
    }

    // ==========================================
    // 2. AUTH GUARD: Proteksi Aksi Selanjutnya
    // ==========================================
    if (empty($_SESSION['logged_in'])) {
        jsonResponse(false, 'Sesi belum terautentikasi. Silakan login terlebih dahulu.', ['authenticated' => false], 401);
    }

    // ==========================================
    // 3. ENDPOINT OPERASIONAL APLIKASI
    // ==========================================

    switch ($action) {
        case 'get_tasks':
        case '':
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

            jsonResponse(true, 'Data berhasil dimuat.', ['tasks' => $tasks]);
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

        case 'complete_week':
        case 'reset_week':
            // 1. Ambil jumlah tugas saat ini
            $stmt = $pdo->query("SELECT COUNT(*) AS total, SUM(CASE WHEN done = 1 THEN 1 ELSE 0 END) AS completed FROM tasks");
            $stats = $stmt->fetch();
            $totalTasks = (int)($stats['total'] ?? 0);
            $completedTasks = (int)($stats['completed'] ?? 0);
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0.00;

            // 2. Hitung waktu kalender ISO minggu saat ini
            $tz = new DateTimeZone('Asia/Jakarta');
            $now = new DateTime('now', $tz);
            $year = (int)$now->format('o'); // Tahun ISO
            $weekNumber = (int)$now->format('W'); // Minggu ISO (1-53)

            $monday = clone $now;
            $monday->setISODate($year, $weekNumber, 1);
            $sunday = clone $now;
            $sunday->setISODate($year, $weekNumber, 7);

            $startDate = $monday->format('Y-m-d');
            $endDate   = $sunday->format('Y-m-d');

            $startDay = (int)$monday->format('j');
            $startM   = $indonesian_months[(int)$monday->format('n')];
            $endDay   = (int)$sunday->format('j');
            $endM     = $indonesian_months[(int)$sunday->format('n')];
            $weekLabel = "Minggu {$weekNumber} ({$startDay} {$startM} - {$endDay} {$endM})";

            // 3. Simpan / Perbarui ke weekly_history
            $historyStmt = $pdo->prepare("
                INSERT INTO weekly_history (year, week_number, week_label, start_date, end_date, total_tasks, completed_tasks, completion_rate, completed_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    week_label = VALUES(week_label),
                    start_date = VALUES(start_date),
                    end_date = VALUES(end_date),
                    total_tasks = VALUES(total_tasks),
                    completed_tasks = VALUES(completed_tasks),
                    completion_rate = VALUES(completion_rate),
                    completed_at = NOW()
            ");
            $historyStmt->execute([
                $year,
                $weekNumber,
                $weekLabel,
                $startDate,
                $endDate,
                $totalTasks,
                $completedTasks,
                $completionRate
            ]);

            // 4. Reset checklist semua tugas menjadi belum selesai (done = 0)
            $pdo->query("UPDATE tasks SET done = 0");

            jsonResponse(true, "Tugas {$weekLabel} berhasil diselesaikan dan dicatat ke riwayat produktivitas!", [
                'summary' => [
                    'year'            => $year,
                    'week_number'     => $weekNumber,
                    'week_label'      => $weekLabel,
                    'total_tasks'     => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'completion_rate' => $completionRate
                ]
            ]);
            break;

        case 'get_productivity':
            $reqYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('o');
            if ($reqYear < 2000 || $reqYear > 2100) {
                $reqYear = (int)date('o');
            }

            // Ambil semua riwayat untuk tahun yang diminta
            $histStmt = $pdo->prepare("SELECT * FROM weekly_history WHERE year = ? ORDER BY week_number ASC");
            $histStmt->execute([$reqYear]);
            $histRows = $histStmt->fetchAll();

            $historyMap = [];
            foreach ($histRows as $row) {
                $historyMap[(int)$row['week_number']] = $row;
            }

            // Status minggu sekarang
            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            $currYear = (int)$now->format('o');
            $currWeek = (int)$now->format('W');

            // Ambil live task jika tahun yang dipilih adalah tahun berjalan
            $liveCompleted = 0;
            $liveTotal = 0;
            if ($reqYear === $currYear) {
                $liveStmt = $pdo->query("SELECT COUNT(*) AS total, SUM(CASE WHEN done = 1 THEN 1 ELSE 0 END) AS completed FROM tasks");
                $liveRow = $liveStmt->fetch();
                $liveTotal = (int)($liveRow['total'] ?? 0);
                $liveCompleted = (int)($liveRow['completed'] ?? 0);
            }

            // Bangun matriks 52 minggu untuk tahun yang diminta
            $weeks = [];
            $totalCompletedAll = 0;
            $recordedWeeksCount = 0;
            $maxCompletedWeek = 0;

            for ($w = 1; $w <= 52; $w++) {
                $mon = new DateTime();
                $mon->setISODate($reqYear, $w, 1);
                $sun = new DateTime();
                $sun->setISODate($reqYear, $w, 7);

                $startDateStr = $mon->format('Y-m-d');
                $endDateStr   = $sun->format('Y-m-d');

                $monthNum = (int)$mon->format('n');
                $monthName = $indonesian_months[$monthNum];

                $startD = (int)$mon->format('j');
                $startM = $indonesian_months[(int)$mon->format('n')];
                $endD   = (int)$sun->format('j');
                $endM   = $indonesian_months[(int)$sun->format('n')];
                $label = "Minggu {$w} ({$startD} {$startM} - {$endD} {$endM})";

                $isCurrent = ($reqYear === $currYear && $w === $currWeek);
                $hasHistory = isset($historyMap[$w]);

                $total = 0;
                $completed = 0;
                $rate = 0.0;

                if ($hasHistory) {
                    $item = $historyMap[$w];
                    $total = (int)$item['total_tasks'];
                    $completed = (int)$item['completed_tasks'];
                    $rate = (float)$item['completion_rate'];
                    $recordedWeeksCount++;
                    $totalCompletedAll += $completed;
                } else if ($isCurrent) {
                    // Tampilkan live data minggu berjalan
                    $total = $liveTotal;
                    $completed = $liveCompleted;
                    $rate = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
                    $totalCompletedAll += $completed;
                }

                if ($completed > $maxCompletedWeek) {
                    $maxCompletedWeek = $completed;
                }

                // Level warna hijau 0-4 ala GitHub:
                // Level 0: 0 tugas
                // Level 1: 1-2 tugas
                // Level 2: 3-5 tugas
                // Level 3: 6-8 tugas
                // Level 4: 9+ tugas (atau >=7 tugas dengan 90%+ completion)
                $level = 0;
                if ($completed >= 9 || ($completed >= 7 && $rate >= 90)) {
                    $level = 4;
                } else if ($completed >= 6 || ($completed >= 4 && $rate >= 80)) {
                    $level = 3;
                } else if ($completed >= 3) {
                    $level = 2;
                } else if ($completed >= 1) {
                    $level = 1;
                }

                $weeks[] = [
                    'week_number'      => $w,
                    'week_label'       => $label,
                    'start_date'       => $startDateStr,
                    'end_date'         => $endDateStr,
                    'month_name'       => $monthName,
                    'month_number'     => $monthNum,
                    'total_tasks'      => $total,
                    'completed_tasks'  => $completed,
                    'completion_rate'  => $rate,
                    'level'            => $level,
                    'is_current'       => $isCurrent,
                    'is_recorded'      => $hasHistory
                ];
            }

            // Hitung streak mingguan aktif (minggu berurutan dengan completed > 0 menuju minggu sekarang)
            $currentStreak = 0;
            if ($reqYear === $currYear) {
                for ($checkW = $currWeek; $checkW >= 1; $checkW--) {
                    $weekData = $weeks[$checkW - 1];
                    if ($weekData['completed_tasks'] > 0) {
                        $currentStreak++;
                    } else {
                        // Jika minggu ini belum ada tugas tapi baru mulai hari Senin/Selasa, cek minggu lalu
                        if ($checkW === $currWeek) {
                            continue;
                        }
                        break;
                    }
                }
            }

            $avgCompleted = ($recordedWeeksCount > 0) 
                ? round($totalCompletedAll / max(1, $recordedWeeksCount), 1) 
                : 0;

            jsonResponse(true, 'Data produktivitas berhasil dimuat.', [
                'year'               => $reqYear,
                'current_week'       => $currWeek,
                'current_year'       => $currYear,
                'stats'              => [
                    'total_completed' => $totalCompletedAll,
                    'weeks_recorded'  => $recordedWeeksCount,
                    'avg_per_week'    => $avgCompleted,
                    'current_streak'  => $currentStreak,
                    'max_completed'   => $maxCompletedWeek
                ],
                'weeks'              => $weeks
            ]);
            break;

        case 'seed_demo_history':
            $sampleData = [
                [31, 'Minggu 31 (27 Jul - 2 Agu)', '2026-07-27', '2026-08-02', 8, 5, 62.50],
                [32, 'Minggu 32 (3 Agu - 9 Agu)', '2026-08-03', '2026-08-09', 9, 8, 88.89],
                [33, 'Minggu 33 (10 Agu - 16 Agu)', '2026-08-10', '2026-08-16', 7, 7, 100.00],
                [34, 'Minggu 34 (17 Agu - 23 Agu)', '2026-08-17', '2026-08-23', 10, 4, 40.00],
                [35, 'Minggu 35 (24 Agu - 30 Agu)', '2026-08-24', '2026-08-30', 10, 9, 90.00]
            ];

            $y = (int)date('o');
            $seedStmt = $pdo->prepare("
                INSERT INTO weekly_history (year, week_number, week_label, start_date, end_date, total_tasks, completed_tasks, completion_rate, completed_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    total_tasks = VALUES(total_tasks),
                    completed_tasks = VALUES(completed_tasks),
                    completion_rate = VALUES(completion_rate),
                    completed_at = NOW()
            ");

            foreach ($sampleData as $item) {
                $seedStmt->execute([$y, $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6]]);
            }

            jsonResponse(true, 'Data contoh riwayat berhasil ditambahkan.');
            break;

        default:
            jsonResponse(false, 'Aksi tidak dikenal.', [], 404);
            break;
    }
} catch (PDOException $e) {
    jsonResponse(false, 'Terjadi kesalahan database: ' . $e->getMessage(), [], 500);
}
