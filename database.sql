-- ========================================================
-- Database: target_mingguan
-- ========================================================

CREATE DATABASE IF NOT EXISTS `target-mingguan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `target-mingguan`;

-- --------------------------------------------------------
-- Struktur Tabel `tasks`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` VARCHAR(64) NOT NULL PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `day` ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
  `hours` VARCHAR(100) DEFAULT '',
  `note` TEXT DEFAULT NULL,
  `done` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Data Awal (Seed Data dari File Backup JSON)
-- --------------------------------------------------------

INSERT INTO `tasks` (`id`, `title`, `day`, `hours`, `note`, `done`) VALUES
('6bbcf02c-7991-45ea-bf3c-5b43a81e0423', 'Materi Alpro', 'Senin', '', '', 0),
('a9a733d1-0021-4360-95db-c4db8b72555e', 'Materi Kecerdasan Buatan', 'Selasa', '', '', 0),
('ac84460a-fa50-4c4f-97e8-24609c61ee03', 'Pembimbingan', 'Senin', '', '', 0),
('8b84e55c-a735-4073-bdfb-9425ec107796', 'Kepanitiaan ICMA', 'Senin', '', '', 1),
('ef487bd9-a6a6-44bf-a432-50ea26e3b3cf', 'Materi Sistem Basis Data', 'Selasa', '', '', 0),
('064a8978-978d-4a6a-bcf0-e0a4b2c40054', 'Materi Pemrograman Game', 'Rabu', '', '', 0),
('45f6a552-1f46-4b52-8325-7a37d8bc7959', 'Penelitian', 'Kamis', '', '', 0),
('3b5c14d8-da79-446e-a1e4-6e561dfc21d6', 'Review Jurnal', 'Jumat', '', '', 0),
('86e7e2cd-b59f-4e7a-828b-582eac837c3f', 'Upload Tugas Eldiru Alpro', 'Senin', '', '', 0),
('5d85df0b-d718-4d93-a443-fdcc60733af6', 'Upload Tugas Eldiru Sistem Basis Data', 'Senin', '', '', 1)
ON DUPLICATE KEY UPDATE 
  `title` = VALUES(`title`),
  `day` = VALUES(`day`),
  `hours` = VALUES(`hours`),
  `note` = VALUES(`note`),
  `done` = VALUES(`done`);
