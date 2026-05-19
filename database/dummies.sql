-- ================================================================
-- EPIKET — Dummy data INSERT script
-- ----------------------------------------------------------------
-- Prerequisites (run BEFORE this script):
--   php artisan migrate
--   php artisan db:seed
--     -> seeds roles(1-4), users(1-5), kelas(1-30, no wali/ketua),
--        status_siswa(1-4), status_absensi(1-5), status_validasi(1-6),
--        jam_absensi(1-10), periode_akademik(1-2)
--
-- After running this script you will have:
--   users    : id 6..25  (10 walikelas + 10 ketuakelas, password = "password")
--   guru     : id 1..10
--   siswa    : id 1..150 (15 per kelas)
--   kelas    : ids 1..10 updated with wali_kelas_id & ketua_kelas_id
--   absensi  : 40 rows (4 dates 2026-05-10..13 x 10 kelas)
--   plus dispensasi, organisasi, daftar_tamu, staff dummy data.
-- ================================================================

START TRANSACTION;
SET FOREIGN_KEY_CHECKS = 0;

SET @now   = '2026-05-09 08:00:00';
SET @actor = '1';
SET @pwd   = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- ================================================================
-- 1. USERS  (id 6..25)
-- ================================================================
INSERT INTO users (id, nama, username, email, password, status, user_input, tanggal_input, created_at, updated_at) VALUES
(6,  'Wali Kelas X 1',  'walikelasx1',  'walikelasx1@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(7,  'Wali Kelas X 2',  'walikelasx2',  'walikelasx2@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(8,  'Wali Kelas X 3',  'walikelasx3',  'walikelasx3@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(9,  'Wali Kelas X 4',  'walikelasx4',  'walikelasx4@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(10, 'Wali Kelas X 5',  'walikelasx5',  'walikelasx5@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(11, 'Wali Kelas X 6',  'walikelasx6',  'walikelasx6@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(12, 'Wali Kelas X 7',  'walikelasx7',  'walikelasx7@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(13, 'Wali Kelas X 8',  'walikelasx8',  'walikelasx8@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(14, 'Wali Kelas X 9',  'walikelasx9',  'walikelasx9@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(15, 'Wali Kelas X 10', 'walikelasx10', 'walikelasx10@epiket.test', @pwd, 1, @actor, @now, @now, @now),
(16, 'Ketua Kelas X 1',  'ketuakelasx1',  'ketuakelasx1@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(17, 'Ketua Kelas X 2',  'ketuakelasx2',  'ketuakelasx2@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(18, 'Ketua Kelas X 3',  'ketuakelasx3',  'ketuakelasx3@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(19, 'Ketua Kelas X 4',  'ketuakelasx4',  'ketuakelasx4@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(20, 'Ketua Kelas X 5',  'ketuakelasx5',  'ketuakelasx5@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(21, 'Ketua Kelas X 6',  'ketuakelasx6',  'ketuakelasx6@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(22, 'Ketua Kelas X 7',  'ketuakelasx7',  'ketuakelasx7@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(23, 'Ketua Kelas X 8',  'ketuakelasx8',  'ketuakelasx8@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(24, 'Ketua Kelas X 9',  'ketuakelasx9',  'ketuakelasx9@epiket.test',  @pwd, 1, @actor, @now, @now, @now),
(25, 'Ketua Kelas X 10', 'ketuakelasx10', 'ketuakelasx10@epiket.test', @pwd, 1, @actor, @now, @now, @now);

-- ================================================================
-- 2. USER_ROLE
--    walikelas users (6..15) -> role_id 3 (Wali Kelas)
--    ketuakelas users (16..25) -> role_id 4 (Ketua Kelas)
-- ================================================================
INSERT INTO user_role (user_id, role_id, status, user_input, tanggal_input, created_at, updated_at) VALUES
(6,  3, 1, @actor, @now, @now, @now),
(7,  3, 1, @actor, @now, @now, @now),
(8,  3, 1, @actor, @now, @now, @now),
(9,  3, 1, @actor, @now, @now, @now),
(10, 3, 1, @actor, @now, @now, @now),
(11, 3, 1, @actor, @now, @now, @now),
(12, 3, 1, @actor, @now, @now, @now),
(13, 3, 1, @actor, @now, @now, @now),
(14, 3, 1, @actor, @now, @now, @now),
(15, 3, 1, @actor, @now, @now, @now),
(16, 4, 1, @actor, @now, @now, @now),
(17, 4, 1, @actor, @now, @now, @now),
(18, 4, 1, @actor, @now, @now, @now),
(19, 4, 1, @actor, @now, @now, @now),
(20, 4, 1, @actor, @now, @now, @now),
(21, 4, 1, @actor, @now, @now, @now),
(22, 4, 1, @actor, @now, @now, @now),
(23, 4, 1, @actor, @now, @now, @now),
(24, 4, 1, @actor, @now, @now, @now),
(25, 4, 1, @actor, @now, @now, @now);

-- ================================================================
-- 3. GURU  (id 1..10) - each guru linked to a walikelas user (6..15)
-- ================================================================
INSERT INTO guru (id, nama_guru, nip, mata_pelajaran, user_id, status, user_input, tanggal_input, created_at, updated_at) VALUES
(1,  'Wali Kelas X 1',  '198001010001', 'Matematika',       6,  1, @actor, @now, @now, @now),
(2,  'Wali Kelas X 2',  '198001010002', 'Bahasa Indonesia', 7,  1, @actor, @now, @now, @now),
(3,  'Wali Kelas X 3',  '198001010003', 'Bahasa Inggris',   8,  1, @actor, @now, @now, @now),
(4,  'Wali Kelas X 4',  '198001010004', 'IPA',              9,  1, @actor, @now, @now, @now),
(5,  'Wali Kelas X 5',  '198001010005', 'IPS',              10, 1, @actor, @now, @now, @now),
(6,  'Wali Kelas X 6',  '198001010006', 'PKn',              11, 1, @actor, @now, @now, @now),
(7,  'Wali Kelas X 7',  '198001010007', 'Seni Budaya',      12, 1, @actor, @now, @now, @now),
(8,  'Wali Kelas X 8',  '198001010008', 'Penjaskes',        13, 1, @actor, @now, @now, @now),
(9,  'Wali Kelas X 9',  '198001010009', 'Agama',            14, 1, @actor, @now, @now, @now),
(10, 'Wali Kelas X 10', '198001010010', 'Sejarah',          15, 1, @actor, @now, @now, @now);

-- ================================================================
-- 4. SISWA  (id 1..150, 15 per kelas)
--    For each kelas K (1..10):
--      siswa_id (K-1)*15 + 1   -> Ketua Kelas X K (user_id = 15 + K)
--      siswa_id (K-1)*15 + 2..15 -> Siswa X K - 02..15 (user_id NULL)
-- ================================================================
INSERT INTO siswa (id, nis, nama_siswa, jenis_kelamin, tanggal_masuk, kelas_id, user_id, status_siswa_id, status, user_input, tanggal_input, created_at, updated_at) VALUES
-- Kelas X 1 (id 1)
(1,   '2026000001', 'Ketua Kelas X 1', 'L', '2025-07-15', 1, 16,   1, 1, @actor, @now, @now, @now),
(2,   '2026000002', 'Siswa X 1 - 02',  'P', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(3,   '2026000003', 'Siswa X 1 - 03',  'L', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(4,   '2026000004', 'Siswa X 1 - 04',  'P', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(5,   '2026000005', 'Siswa X 1 - 05',  'L', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(6,   '2026000006', 'Siswa X 1 - 06',  'P', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(7,   '2026000007', 'Siswa X 1 - 07',  'L', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(8,   '2026000008', 'Siswa X 1 - 08',  'P', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(9,   '2026000009', 'Siswa X 1 - 09',  'L', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(10,  '2026000010', 'Siswa X 1 - 10',  'P', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(11,  '2026000011', 'Siswa X 1 - 11',  'L', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(12,  '2026000012', 'Siswa X 1 - 12',  'P', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(13,  '2026000013', 'Siswa X 1 - 13',  'L', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(14,  '2026000014', 'Siswa X 1 - 14',  'P', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
(15,  '2026000015', 'Siswa X 1 - 15',  'L', '2025-07-15', 1, NULL, 1, 1, @actor, @now, @now, @now),
-- Kelas X 2 (id 2)
(16,  '2026000016', 'Ketua Kelas X 2', 'P', '2025-07-15', 2, 17,   1, 1, @actor, @now, @now, @now),
(17,  '2026000017', 'Siswa X 2 - 02',  'L', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(18,  '2026000018', 'Siswa X 2 - 03',  'P', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(19,  '2026000019', 'Siswa X 2 - 04',  'L', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(20,  '2026000020', 'Siswa X 2 - 05',  'P', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(21,  '2026000021', 'Siswa X 2 - 06',  'L', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(22,  '2026000022', 'Siswa X 2 - 07',  'P', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(23,  '2026000023', 'Siswa X 2 - 08',  'L', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(24,  '2026000024', 'Siswa X 2 - 09',  'P', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(25,  '2026000025', 'Siswa X 2 - 10',  'L', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(26,  '2026000026', 'Siswa X 2 - 11',  'P', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(27,  '2026000027', 'Siswa X 2 - 12',  'L', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(28,  '2026000028', 'Siswa X 2 - 13',  'P', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(29,  '2026000029', 'Siswa X 2 - 14',  'L', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
(30,  '2026000030', 'Siswa X 2 - 15',  'P', '2025-07-15', 2, NULL, 1, 1, @actor, @now, @now, @now),
-- Kelas X 3 (id 3)
(31,  '2026000031', 'Ketua Kelas X 3', 'L', '2025-07-15', 3, 18,   1, 1, @actor, @now, @now, @now),
(32,  '2026000032', 'Siswa X 3 - 02',  'P', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(33,  '2026000033', 'Siswa X 3 - 03',  'L', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(34,  '2026000034', 'Siswa X 3 - 04',  'P', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(35,  '2026000035', 'Siswa X 3 - 05',  'L', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(36,  '2026000036', 'Siswa X 3 - 06',  'P', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(37,  '2026000037', 'Siswa X 3 - 07',  'L', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(38,  '2026000038', 'Siswa X 3 - 08',  'P', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(39,  '2026000039', 'Siswa X 3 - 09',  'L', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(40,  '2026000040', 'Siswa X 3 - 10',  'P', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(41,  '2026000041', 'Siswa X 3 - 11',  'L', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(42,  '2026000042', 'Siswa X 3 - 12',  'P', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(43,  '2026000043', 'Siswa X 3 - 13',  'L', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(44,  '2026000044', 'Siswa X 3 - 14',  'P', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
(45,  '2026000045', 'Siswa X 3 - 15',  'L', '2025-07-15', 3, NULL, 1, 1, @actor, @now, @now, @now),
-- Kelas X 4 (id 4)
(46,  '2026000046', 'Ketua Kelas X 4', 'P', '2025-07-15', 4, 19,   1, 1, @actor, @now, @now, @now),
(47,  '2026000047', 'Siswa X 4 - 02',  'L', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(48,  '2026000048', 'Siswa X 4 - 03',  'P', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(49,  '2026000049', 'Siswa X 4 - 04',  'L', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(50,  '2026000050', 'Siswa X 4 - 05',  'P', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(51,  '2026000051', 'Siswa X 4 - 06',  'L', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(52,  '2026000052', 'Siswa X 4 - 07',  'P', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(53,  '2026000053', 'Siswa X 4 - 08',  'L', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(54,  '2026000054', 'Siswa X 4 - 09',  'P', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(55,  '2026000055', 'Siswa X 4 - 10',  'L', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(56,  '2026000056', 'Siswa X 4 - 11',  'P', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(57,  '2026000057', 'Siswa X 4 - 12',  'L', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(58,  '2026000058', 'Siswa X 4 - 13',  'P', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(59,  '2026000059', 'Siswa X 4 - 14',  'L', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
(60,  '2026000060', 'Siswa X 4 - 15',  'P', '2025-07-15', 4, NULL, 1, 1, @actor, @now, @now, @now),
-- Kelas X 5 (id 5)
(61,  '2026000061', 'Ketua Kelas X 5', 'L', '2025-07-15', 5, 20,   1, 1, @actor, @now, @now, @now),
(62,  '2026000062', 'Siswa X 5 - 02',  'P', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(63,  '2026000063', 'Siswa X 5 - 03',  'L', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(64,  '2026000064', 'Siswa X 5 - 04',  'P', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(65,  '2026000065', 'Siswa X 5 - 05',  'L', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(66,  '2026000066', 'Siswa X 5 - 06',  'P', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(67,  '2026000067', 'Siswa X 5 - 07',  'L', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(68,  '2026000068', 'Siswa X 5 - 08',  'P', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(69,  '2026000069', 'Siswa X 5 - 09',  'L', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(70,  '2026000070', 'Siswa X 5 - 10',  'P', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(71,  '2026000071', 'Siswa X 5 - 11',  'L', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(72,  '2026000072', 'Siswa X 5 - 12',  'P', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(73,  '2026000073', 'Siswa X 5 - 13',  'L', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(74,  '2026000074', 'Siswa X 5 - 14',  'P', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
(75,  '2026000075', 'Siswa X 5 - 15',  'L', '2025-07-15', 5, NULL, 1, 1, @actor, @now, @now, @now),
-- Kelas X 6 (id 6)
(76,  '2026000076', 'Ketua Kelas X 6', 'P', '2025-07-15', 6, 21,   1, 1, @actor, @now, @now, @now),
(77,  '2026000077', 'Siswa X 6 - 02',  'L', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(78,  '2026000078', 'Siswa X 6 - 03',  'P', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(79,  '2026000079', 'Siswa X 6 - 04',  'L', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(80,  '2026000080', 'Siswa X 6 - 05',  'P', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(81,  '2026000081', 'Siswa X 6 - 06',  'L', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(82,  '2026000082', 'Siswa X 6 - 07',  'P', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(83,  '2026000083', 'Siswa X 6 - 08',  'L', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(84,  '2026000084', 'Siswa X 6 - 09',  'P', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(85,  '2026000085', 'Siswa X 6 - 10',  'L', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(86,  '2026000086', 'Siswa X 6 - 11',  'P', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(87,  '2026000087', 'Siswa X 6 - 12',  'L', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(88,  '2026000088', 'Siswa X 6 - 13',  'P', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(89,  '2026000089', 'Siswa X 6 - 14',  'L', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
(90,  '2026000090', 'Siswa X 6 - 15',  'P', '2025-07-15', 6, NULL, 1, 1, @actor, @now, @now, @now),
-- Kelas X 7 (id 7)
(91,  '2026000091', 'Ketua Kelas X 7', 'L', '2025-07-15', 7, 22,   1, 1, @actor, @now, @now, @now),
(92,  '2026000092', 'Siswa X 7 - 02',  'P', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(93,  '2026000093', 'Siswa X 7 - 03',  'L', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(94,  '2026000094', 'Siswa X 7 - 04',  'P', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(95,  '2026000095', 'Siswa X 7 - 05',  'L', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(96,  '2026000096', 'Siswa X 7 - 06',  'P', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(97,  '2026000097', 'Siswa X 7 - 07',  'L', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(98,  '2026000098', 'Siswa X 7 - 08',  'P', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(99,  '2026000099', 'Siswa X 7 - 09',  'L', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(100, '2026000100', 'Siswa X 7 - 10',  'P', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(101, '2026000101', 'Siswa X 7 - 11',  'L', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(102, '2026000102', 'Siswa X 7 - 12',  'P', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(103, '2026000103', 'Siswa X 7 - 13',  'L', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(104, '2026000104', 'Siswa X 7 - 14',  'P', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
(105, '2026000105', 'Siswa X 7 - 15',  'L', '2025-07-15', 7, NULL, 1, 1, @actor, @now, @now, @now),
-- Kelas X 8 (id 8)
(106, '2026000106', 'Ketua Kelas X 8', 'P', '2025-07-15', 8, 23,   1, 1, @actor, @now, @now, @now),
(107, '2026000107', 'Siswa X 8 - 02',  'L', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(108, '2026000108', 'Siswa X 8 - 03',  'P', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(109, '2026000109', 'Siswa X 8 - 04',  'L', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(110, '2026000110', 'Siswa X 8 - 05',  'P', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(111, '2026000111', 'Siswa X 8 - 06',  'L', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(112, '2026000112', 'Siswa X 8 - 07',  'P', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(113, '2026000113', 'Siswa X 8 - 08',  'L', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(114, '2026000114', 'Siswa X 8 - 09',  'P', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(115, '2026000115', 'Siswa X 8 - 10',  'L', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(116, '2026000116', 'Siswa X 8 - 11',  'P', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(117, '2026000117', 'Siswa X 8 - 12',  'L', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(118, '2026000118', 'Siswa X 8 - 13',  'P', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(119, '2026000119', 'Siswa X 8 - 14',  'L', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
(120, '2026000120', 'Siswa X 8 - 15',  'P', '2025-07-15', 8, NULL, 1, 1, @actor, @now, @now, @now),
-- Kelas X 9 (id 9)
(121, '2026000121', 'Ketua Kelas X 9', 'L', '2025-07-15', 9, 24,   1, 1, @actor, @now, @now, @now),
(122, '2026000122', 'Siswa X 9 - 02',  'P', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(123, '2026000123', 'Siswa X 9 - 03',  'L', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(124, '2026000124', 'Siswa X 9 - 04',  'P', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(125, '2026000125', 'Siswa X 9 - 05',  'L', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(126, '2026000126', 'Siswa X 9 - 06',  'P', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(127, '2026000127', 'Siswa X 9 - 07',  'L', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(128, '2026000128', 'Siswa X 9 - 08',  'P', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(129, '2026000129', 'Siswa X 9 - 09',  'L', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(130, '2026000130', 'Siswa X 9 - 10',  'P', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(131, '2026000131', 'Siswa X 9 - 11',  'L', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(132, '2026000132', 'Siswa X 9 - 12',  'P', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(133, '2026000133', 'Siswa X 9 - 13',  'L', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(134, '2026000134', 'Siswa X 9 - 14',  'P', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
(135, '2026000135', 'Siswa X 9 - 15',  'L', '2025-07-15', 9, NULL, 1, 1, @actor, @now, @now, @now),
-- Kelas X 10 (id 10)
(136, '2026000136', 'Ketua Kelas X 10', 'P', '2025-07-15', 10, 25,   1, 1, @actor, @now, @now, @now),
(137, '2026000137', 'Siswa X 10 - 02',  'L', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(138, '2026000138', 'Siswa X 10 - 03',  'P', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(139, '2026000139', 'Siswa X 10 - 04',  'L', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(140, '2026000140', 'Siswa X 10 - 05',  'P', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(141, '2026000141', 'Siswa X 10 - 06',  'L', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(142, '2026000142', 'Siswa X 10 - 07',  'P', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(143, '2026000143', 'Siswa X 10 - 08',  'L', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(144, '2026000144', 'Siswa X 10 - 09',  'P', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(145, '2026000145', 'Siswa X 10 - 10',  'L', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(146, '2026000146', 'Siswa X 10 - 11',  'P', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(147, '2026000147', 'Siswa X 10 - 12',  'L', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(148, '2026000148', 'Siswa X 10 - 13',  'P', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(149, '2026000149', 'Siswa X 10 - 14',  'L', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now),
(150, '2026000150', 'Siswa X 10 - 15',  'P', '2025-07-15', 10, NULL, 1, 1, @actor, @now, @now, @now);

-- ================================================================
-- 5. UPDATE KELAS — assign wali_kelas_id (guru.id) & ketua_kelas_id (siswa.id)
--    For kelas K (1..10):
--      wali_kelas_id  = K           (guru ids 1..10)
--      ketua_kelas_id = (K-1)*15+1  (siswa 1, 16, 31, 46, 61, 76, 91, 106, 121, 136)
-- ================================================================
UPDATE kelas SET wali_kelas_id = 1,  ketua_kelas_id = 1,   user_update = @actor, tanggal_update = @now, updated_at = @now WHERE id = 1;
UPDATE kelas SET wali_kelas_id = 2,  ketua_kelas_id = 16,  user_update = @actor, tanggal_update = @now, updated_at = @now WHERE id = 2;
UPDATE kelas SET wali_kelas_id = 3,  ketua_kelas_id = 31,  user_update = @actor, tanggal_update = @now, updated_at = @now WHERE id = 3;
UPDATE kelas SET wali_kelas_id = 4,  ketua_kelas_id = 46,  user_update = @actor, tanggal_update = @now, updated_at = @now WHERE id = 4;
UPDATE kelas SET wali_kelas_id = 5,  ketua_kelas_id = 61,  user_update = @actor, tanggal_update = @now, updated_at = @now WHERE id = 5;
UPDATE kelas SET wali_kelas_id = 6,  ketua_kelas_id = 76,  user_update = @actor, tanggal_update = @now, updated_at = @now WHERE id = 6;
UPDATE kelas SET wali_kelas_id = 7,  ketua_kelas_id = 91,  user_update = @actor, tanggal_update = @now, updated_at = @now WHERE id = 7;
UPDATE kelas SET wali_kelas_id = 8,  ketua_kelas_id = 106, user_update = @actor, tanggal_update = @now, updated_at = @now WHERE id = 8;
UPDATE kelas SET wali_kelas_id = 9,  ketua_kelas_id = 121, user_update = @actor, tanggal_update = @now, updated_at = @now WHERE id = 9;
UPDATE kelas SET wali_kelas_id = 10, ketua_kelas_id = 136, user_update = @actor, tanggal_update = @now, updated_at = @now WHERE id = 10;

-- ================================================================
-- 6. STAFF  (3 rows)
-- ================================================================
INSERT INTO staff (id, nama_staff, user_id, status, user_input, tanggal_input, created_at, updated_at) VALUES
(1, 'Budi Sutrisno', NULL, 1, @actor, @now, @now, @now),
(2, 'Siti Aminah',   NULL, 1, @actor, @now, @now, @now),
(3, 'Joko Pranowo',  NULL, 1, @actor, @now, @now, @now);

-- ================================================================
-- 7. ORGANISASI  (4 rows, pembina = guru 1..4)
-- ================================================================
INSERT INTO organisasi (id, nama_organisasi, pembina_id, keterangan, status, user_input, tanggal_input, created_at, updated_at) VALUES
(1, 'OSIS',    1, 'Organisasi Siswa Intra Sekolah', 1, @actor, @now, @now, @now),
(2, 'Pramuka', 2, 'Praja Muda Karana',              1, @actor, @now, @now, @now),
(3, 'PMR',     3, 'Palang Merah Remaja',            1, @actor, @now, @now, @now),
(4, 'Rohis',   4, 'Kerohanian Islam',               1, @actor, @now, @now, @now);

-- ================================================================
-- 8. SISWA_ORGANISASI  (40 rows — each org has 10 members, 1 per kelas)
--    OSIS    : ketua tiap kelas (siswa 1, 16, 31, ...)
--    Pramuka : siswa offset +1
--    PMR     : siswa offset +2
--    Rohis   : siswa offset +3
-- ================================================================
INSERT INTO siswa_organisasi (siswa_id, organisasi_id, status, user_input, tanggal_input, created_at, updated_at) VALUES
-- OSIS
(1,   1, 1, @actor, @now, @now, @now),
(16,  1, 1, @actor, @now, @now, @now),
(31,  1, 1, @actor, @now, @now, @now),
(46,  1, 1, @actor, @now, @now, @now),
(61,  1, 1, @actor, @now, @now, @now),
(76,  1, 1, @actor, @now, @now, @now),
(91,  1, 1, @actor, @now, @now, @now),
(106, 1, 1, @actor, @now, @now, @now),
(121, 1, 1, @actor, @now, @now, @now),
(136, 1, 1, @actor, @now, @now, @now),
-- Pramuka
(2,   2, 1, @actor, @now, @now, @now),
(17,  2, 1, @actor, @now, @now, @now),
(32,  2, 1, @actor, @now, @now, @now),
(47,  2, 1, @actor, @now, @now, @now),
(62,  2, 1, @actor, @now, @now, @now),
(77,  2, 1, @actor, @now, @now, @now),
(92,  2, 1, @actor, @now, @now, @now),
(107, 2, 1, @actor, @now, @now, @now),
(122, 2, 1, @actor, @now, @now, @now),
(137, 2, 1, @actor, @now, @now, @now),
-- PMR
(3,   3, 1, @actor, @now, @now, @now),
(18,  3, 1, @actor, @now, @now, @now),
(33,  3, 1, @actor, @now, @now, @now),
(48,  3, 1, @actor, @now, @now, @now),
(63,  3, 1, @actor, @now, @now, @now),
(78,  3, 1, @actor, @now, @now, @now),
(93,  3, 1, @actor, @now, @now, @now),
(108, 3, 1, @actor, @now, @now, @now),
(123, 3, 1, @actor, @now, @now, @now),
(138, 3, 1, @actor, @now, @now, @now),
-- Rohis
(4,   4, 1, @actor, @now, @now, @now),
(19,  4, 1, @actor, @now, @now, @now),
(34,  4, 1, @actor, @now, @now, @now),
(49,  4, 1, @actor, @now, @now, @now),
(64,  4, 1, @actor, @now, @now, @now),
(79,  4, 1, @actor, @now, @now, @now),
(94,  4, 1, @actor, @now, @now, @now),
(109, 4, 1, @actor, @now, @now, @now),
(124, 4, 1, @actor, @now, @now, @now),
(139, 4, 1, @actor, @now, @now, @now);

-- ================================================================
-- 9. ABSENSI  (40 rows = 4 dates x 10 kelas)
--    status_validasi_id = 5 (Disetujui), periode_akademik_id = 2
--    absensi.id mapping:
--      1..10  -> 2026-05-10, kelas 1..10
--      11..20 -> 2026-05-11
--      21..30 -> 2026-05-12
--      31..40 -> 2026-05-13
-- ================================================================
INSERT INTO absensi (id, kelas_id, tanggal, status_validasi_id, periode_akademik_id, status, user_input, tanggal_input, created_at, updated_at) VALUES
-- 2026-05-10
(1,  1,  '2026-05-10 07:30:00', 5, 2, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(2,  2,  '2026-05-10 07:30:00', 5, 2, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(3,  3,  '2026-05-10 07:30:00', 5, 2, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(4,  4,  '2026-05-10 07:30:00', 5, 2, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(5,  5,  '2026-05-10 07:30:00', 5, 2, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(6,  6,  '2026-05-10 07:30:00', 5, 2, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(7,  7,  '2026-05-10 07:30:00', 5, 2, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(8,  8,  '2026-05-10 07:30:00', 5, 2, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(9,  9,  '2026-05-10 07:30:00', 5, 2, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(10, 10, '2026-05-10 07:30:00', 5, 2, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
-- 2026-05-11
(11, 1,  '2026-05-11 07:30:00', 5, 2, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(12, 2,  '2026-05-11 07:30:00', 5, 2, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(13, 3,  '2026-05-11 07:30:00', 5, 2, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(14, 4,  '2026-05-11 07:30:00', 5, 2, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(15, 5,  '2026-05-11 07:30:00', 5, 2, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(16, 6,  '2026-05-11 07:30:00', 5, 2, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(17, 7,  '2026-05-11 07:30:00', 5, 2, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(18, 8,  '2026-05-11 07:30:00', 5, 2, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(19, 9,  '2026-05-11 07:30:00', 5, 2, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(20, 10, '2026-05-11 07:30:00', 5, 2, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
-- 2026-05-12
(21, 1,  '2026-05-12 07:30:00', 5, 2, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(22, 2,  '2026-05-12 07:30:00', 5, 2, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(23, 3,  '2026-05-12 07:30:00', 5, 2, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(24, 4,  '2026-05-12 07:30:00', 5, 2, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(25, 5,  '2026-05-12 07:30:00', 5, 2, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(26, 6,  '2026-05-12 07:30:00', 5, 2, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(27, 7,  '2026-05-12 07:30:00', 5, 2, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(28, 8,  '2026-05-12 07:30:00', 5, 2, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(29, 9,  '2026-05-12 07:30:00', 5, 2, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(30, 10, '2026-05-12 07:30:00', 5, 2, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
-- 2026-05-13
(31, 1,  '2026-05-13 07:30:00', 5, 2, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(32, 2,  '2026-05-13 07:30:00', 5, 2, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(33, 3,  '2026-05-13 07:30:00', 5, 2, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(34, 4,  '2026-05-13 07:30:00', 5, 2, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(35, 5,  '2026-05-13 07:30:00', 5, 2, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(36, 6,  '2026-05-13 07:30:00', 5, 2, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(37, 7,  '2026-05-13 07:30:00', 5, 2, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(38, 8,  '2026-05-13 07:30:00', 5, 2, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(39, 9,  '2026-05-13 07:30:00', 5, 2, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(40, 10, '2026-05-13 07:30:00', 5, 2, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00');

-- ================================================================
-- 10. ABSENSI_DETAIL  (120 rows = 3 absent siswa per absensi)
--     Per kelas, base_siswa_id = (kelas_id-1)*15.
--     Per day, pattern (offset, status_absensi_id, is_full_day):
--       Day 1 (absensi 1..10):  +2 Izin/full,    +3 Sakit/full,   +4 Terlambat/partial
--       Day 2 (absensi 11..20): +3 Sakit/full,   +5 Alpha/full,   +6 Terlambat/partial
--       Day 3 (absensi 21..30): +4 Izin/full,    +7 Sakit/full,   +8 Terlambat/partial
--       Day 4 (absensi 31..40): +5 Alpha/full,   +2 Sakit/full,   +9 Terlambat/partial
--     absensi_detail.id mapping: per absensi N -> ids (N-1)*3+1, +2, +3.
--     The 3rd id of each absensi is always the Terlambat row.
-- ================================================================
INSERT INTO absensi_detail (id, absensi_id, siswa_id, is_full_day, status_absensi_id, keterangan, lampiran_absensi, status, user_input, tanggal_input, created_at, updated_at) VALUES
-- Day 1 (2026-05-10) — absensi 1..10
(1,   1,  2,   1, 1, 'Izin keluarga',       NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(2,   1,  3,   1, 2, 'Sakit demam',         NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(3,   1,  4,   0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(4,   2,  17,  1, 1, 'Izin keluarga',       NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(5,   2,  18,  1, 2, 'Sakit demam',         NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(6,   2,  19,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(7,   3,  32,  1, 1, 'Izin keluarga',       NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(8,   3,  33,  1, 2, 'Sakit demam',         NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(9,   3,  34,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(10,  4,  47,  1, 1, 'Izin keluarga',       NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(11,  4,  48,  1, 2, 'Sakit demam',         NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(12,  4,  49,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(13,  5,  62,  1, 1, 'Izin keluarga',       NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(14,  5,  63,  1, 2, 'Sakit demam',         NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(15,  5,  64,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(16,  6,  77,  1, 1, 'Izin keluarga',       NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(17,  6,  78,  1, 2, 'Sakit demam',         NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(18,  6,  79,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(19,  7,  92,  1, 1, 'Izin keluarga',       NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(20,  7,  93,  1, 2, 'Sakit demam',         NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(21,  7,  94,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(22,  8,  107, 1, 1, 'Izin keluarga',       NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(23,  8,  108, 1, 2, 'Sakit demam',         NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(24,  8,  109, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(25,  9,  122, 1, 1, 'Izin keluarga',       NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(26,  9,  123, 1, 2, 'Sakit demam',         NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(27,  9,  124, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(28,  10, 137, 1, 1, 'Izin keluarga',       NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(29,  10, 138, 1, 2, 'Sakit demam',         NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(30,  10, 139, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
-- Day 2 (2026-05-11) — absensi 11..20 (offsets +3 Sakit, +5 Alpha, +6 Terlambat)
(31,  11, 3,   1, 2, 'Sakit batuk pilek',   NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(32,  11, 5,   1, 3, 'Tanpa keterangan',    NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(33,  11, 6,   0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(34,  12, 18,  1, 2, 'Sakit batuk pilek',   NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(35,  12, 20,  1, 3, 'Tanpa keterangan',    NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(36,  12, 21,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(37,  13, 33,  1, 2, 'Sakit batuk pilek',   NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(38,  13, 35,  1, 3, 'Tanpa keterangan',    NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(39,  13, 36,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(40,  14, 48,  1, 2, 'Sakit batuk pilek',   NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(41,  14, 50,  1, 3, 'Tanpa keterangan',    NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(42,  14, 51,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(43,  15, 63,  1, 2, 'Sakit batuk pilek',   NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(44,  15, 65,  1, 3, 'Tanpa keterangan',    NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(45,  15, 66,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(46,  16, 78,  1, 2, 'Sakit batuk pilek',   NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(47,  16, 80,  1, 3, 'Tanpa keterangan',    NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(48,  16, 81,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(49,  17, 93,  1, 2, 'Sakit batuk pilek',   NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(50,  17, 95,  1, 3, 'Tanpa keterangan',    NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(51,  17, 96,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(52,  18, 108, 1, 2, 'Sakit batuk pilek',   NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(53,  18, 110, 1, 3, 'Tanpa keterangan',    NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(54,  18, 111, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(55,  19, 123, 1, 2, 'Sakit batuk pilek',   NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(56,  19, 125, 1, 3, 'Tanpa keterangan',    NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(57,  19, 126, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(58,  20, 138, 1, 2, 'Sakit batuk pilek',   NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(59,  20, 140, 1, 3, 'Tanpa keterangan',    NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(60,  20, 141, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
-- Day 3 (2026-05-12) — absensi 21..30 (offsets +4 Izin, +7 Sakit, +8 Terlambat)
(61,  21, 4,   1, 1, 'Izin acara keluarga', NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(62,  21, 7,   1, 2, 'Sakit',               NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(63,  21, 8,   0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(64,  22, 19,  1, 1, 'Izin acara keluarga', NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(65,  22, 22,  1, 2, 'Sakit',               NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(66,  22, 23,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(67,  23, 34,  1, 1, 'Izin acara keluarga', NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(68,  23, 37,  1, 2, 'Sakit',               NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(69,  23, 38,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(70,  24, 49,  1, 1, 'Izin acara keluarga', NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(71,  24, 52,  1, 2, 'Sakit',               NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(72,  24, 53,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(73,  25, 64,  1, 1, 'Izin acara keluarga', NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(74,  25, 67,  1, 2, 'Sakit',               NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(75,  25, 68,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(76,  26, 79,  1, 1, 'Izin acara keluarga', NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(77,  26, 82,  1, 2, 'Sakit',               NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(78,  26, 83,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(79,  27, 94,  1, 1, 'Izin acara keluarga', NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(80,  27, 97,  1, 2, 'Sakit',               NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(81,  27, 98,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(82,  28, 109, 1, 1, 'Izin acara keluarga', NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(83,  28, 112, 1, 2, 'Sakit',               NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(84,  28, 113, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(85,  29, 124, 1, 1, 'Izin acara keluarga', NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(86,  29, 127, 1, 2, 'Sakit',               NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(87,  29, 128, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(88,  30, 139, 1, 1, 'Izin acara keluarga', NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(89,  30, 142, 1, 2, 'Sakit',               NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(90,  30, 143, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
-- Day 4 (2026-05-13) — absensi 31..40 (offsets +5 Alpha, +2 Sakit, +9 Terlambat)
(91,  31, 5,   1, 3, 'Alpha',               NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(92,  31, 2,   1, 2, 'Sakit kepala',        NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(93,  31, 9,   0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(94,  32, 20,  1, 3, 'Alpha',               NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(95,  32, 17,  1, 2, 'Sakit kepala',        NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(96,  32, 24,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(97,  33, 35,  1, 3, 'Alpha',               NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(98,  33, 32,  1, 2, 'Sakit kepala',        NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(99,  33, 39,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(100, 34, 50,  1, 3, 'Alpha',               NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(101, 34, 47,  1, 2, 'Sakit kepala',        NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(102, 34, 54,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(103, 35, 65,  1, 3, 'Alpha',               NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(104, 35, 62,  1, 2, 'Sakit kepala',        NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(105, 35, 69,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(106, 36, 80,  1, 3, 'Alpha',               NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(107, 36, 77,  1, 2, 'Sakit kepala',        NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(108, 36, 84,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(109, 37, 95,  1, 3, 'Alpha',               NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(110, 37, 92,  1, 2, 'Sakit kepala',        NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(111, 37, 99,  0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(112, 38, 110, 1, 3, 'Alpha',               NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(113, 38, 107, 1, 2, 'Sakit kepala',        NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(114, 38, 114, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(115, 39, 125, 1, 3, 'Alpha',               NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(116, 39, 122, 1, 2, 'Sakit kepala',        NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(117, 39, 129, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(118, 40, 140, 1, 3, 'Alpha',               NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(119, 40, 137, 1, 2, 'Sakit kepala',        NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(120, 40, 144, 0, 5, 'Terlambat',           NULL, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00');

-- ================================================================
-- 11. ABSENSI_DETAIL_JAM  (40 rows = one per Terlambat absensi_detail)
--     Every 3rd absensi_detail (ids 3, 6, 9, ..., 120) is Terlambat,
--     linked to jam_ke_id = 1 (Jam ke-1, 06:30-07:15).
-- ================================================================
INSERT INTO absensi_detail_jam (absensi_detail_id, jam_ke_id, status, user_input, tanggal_input, created_at, updated_at) VALUES
(3,   1, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(6,   1, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(9,   1, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(12,  1, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(15,  1, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(18,  1, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(21,  1, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(24,  1, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(27,  1, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(30,  1, 1, @actor, '2026-05-10 07:30:00', '2026-05-10 07:30:00', '2026-05-10 07:30:00'),
(33,  1, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(36,  1, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(39,  1, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(42,  1, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(45,  1, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(48,  1, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(51,  1, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(54,  1, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(57,  1, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(60,  1, 1, @actor, '2026-05-11 07:30:00', '2026-05-11 07:30:00', '2026-05-11 07:30:00'),
(63,  1, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(66,  1, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(69,  1, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(72,  1, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(75,  1, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(78,  1, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(81,  1, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(84,  1, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(87,  1, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(90,  1, 1, @actor, '2026-05-12 07:30:00', '2026-05-12 07:30:00', '2026-05-12 07:30:00'),
(93,  1, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(96,  1, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(99,  1, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(102, 1, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(105, 1, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(108, 1, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(111, 1, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(114, 1, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(117, 1, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00'),
(120, 1, 1, @actor, '2026-05-13 07:30:00', '2026-05-13 07:30:00', '2026-05-13 07:30:00');

-- ================================================================
-- 12. KETERLAMBATAN  (40 rows = one per Terlambat absensi_detail)
--     waktu_masuk = absensi.tanggal + ~0..15 minutes
-- ================================================================
INSERT INTO keterlambatan (absensi_id, siswa_id, waktu_masuk, alasan, periode_akademik_id, status, user_input, tanggal_input, created_at, updated_at) VALUES
-- Day 1 (2026-05-10)
(1,  4,   '2026-05-10 07:35:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-10 07:35:00', '2026-05-10 07:35:00', '2026-05-10 07:35:00'),
(2,  19,  '2026-05-10 07:40:00', 'Macet di jalan',       2, 1, @actor, '2026-05-10 07:40:00', '2026-05-10 07:40:00', '2026-05-10 07:40:00'),
(3,  34,  '2026-05-10 07:32:00', 'Ban sepeda bocor',     2, 1, @actor, '2026-05-10 07:32:00', '2026-05-10 07:32:00', '2026-05-10 07:32:00'),
(4,  49,  '2026-05-10 07:45:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-10 07:45:00', '2026-05-10 07:45:00', '2026-05-10 07:45:00'),
(5,  64,  '2026-05-10 07:38:00', 'Mengantar adik',       2, 1, @actor, '2026-05-10 07:38:00', '2026-05-10 07:38:00', '2026-05-10 07:38:00'),
(6,  79,  '2026-05-10 07:42:00', 'Macet di jalan',       2, 1, @actor, '2026-05-10 07:42:00', '2026-05-10 07:42:00', '2026-05-10 07:42:00'),
(7,  94,  '2026-05-10 07:33:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-10 07:33:00', '2026-05-10 07:33:00', '2026-05-10 07:33:00'),
(8,  109, '2026-05-10 07:48:00', 'Hujan deras',          2, 1, @actor, '2026-05-10 07:48:00', '2026-05-10 07:48:00', '2026-05-10 07:48:00'),
(9,  124, '2026-05-10 07:36:00', 'Sepeda motor mogok',   2, 1, @actor, '2026-05-10 07:36:00', '2026-05-10 07:36:00', '2026-05-10 07:36:00'),
(10, 139, '2026-05-10 07:41:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-10 07:41:00', '2026-05-10 07:41:00', '2026-05-10 07:41:00'),
-- Day 2 (2026-05-11)
(11, 6,   '2026-05-11 07:34:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-11 07:34:00', '2026-05-11 07:34:00', '2026-05-11 07:34:00'),
(12, 21,  '2026-05-11 07:46:00', 'Macet di jalan',       2, 1, @actor, '2026-05-11 07:46:00', '2026-05-11 07:46:00', '2026-05-11 07:46:00'),
(13, 36,  '2026-05-11 07:39:00', 'Mengantar adik',       2, 1, @actor, '2026-05-11 07:39:00', '2026-05-11 07:39:00', '2026-05-11 07:39:00'),
(14, 51,  '2026-05-11 07:43:00', 'Hujan deras',          2, 1, @actor, '2026-05-11 07:43:00', '2026-05-11 07:43:00', '2026-05-11 07:43:00'),
(15, 66,  '2026-05-11 07:35:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-11 07:35:00', '2026-05-11 07:35:00', '2026-05-11 07:35:00'),
(16, 81,  '2026-05-11 07:50:00', 'Ban sepeda bocor',     2, 1, @actor, '2026-05-11 07:50:00', '2026-05-11 07:50:00', '2026-05-11 07:50:00'),
(17, 96,  '2026-05-11 07:37:00', 'Macet di jalan',       2, 1, @actor, '2026-05-11 07:37:00', '2026-05-11 07:37:00', '2026-05-11 07:37:00'),
(18, 111, '2026-05-11 07:44:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-11 07:44:00', '2026-05-11 07:44:00', '2026-05-11 07:44:00'),
(19, 126, '2026-05-11 07:38:00', 'Mengantar adik',       2, 1, @actor, '2026-05-11 07:38:00', '2026-05-11 07:38:00', '2026-05-11 07:38:00'),
(20, 141, '2026-05-11 07:47:00', 'Sepeda motor mogok',   2, 1, @actor, '2026-05-11 07:47:00', '2026-05-11 07:47:00', '2026-05-11 07:47:00'),
-- Day 3 (2026-05-12)
(21, 8,   '2026-05-12 07:32:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-12 07:32:00', '2026-05-12 07:32:00', '2026-05-12 07:32:00'),
(22, 23,  '2026-05-12 07:45:00', 'Macet di jalan',       2, 1, @actor, '2026-05-12 07:45:00', '2026-05-12 07:45:00', '2026-05-12 07:45:00'),
(23, 38,  '2026-05-12 07:40:00', 'Hujan deras',          2, 1, @actor, '2026-05-12 07:40:00', '2026-05-12 07:40:00', '2026-05-12 07:40:00'),
(24, 53,  '2026-05-12 07:35:00', 'Mengantar adik',       2, 1, @actor, '2026-05-12 07:35:00', '2026-05-12 07:35:00', '2026-05-12 07:35:00'),
(25, 68,  '2026-05-12 07:42:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-12 07:42:00', '2026-05-12 07:42:00', '2026-05-12 07:42:00'),
(26, 83,  '2026-05-12 07:36:00', 'Ban sepeda bocor',     2, 1, @actor, '2026-05-12 07:36:00', '2026-05-12 07:36:00', '2026-05-12 07:36:00'),
(27, 98,  '2026-05-12 07:48:00', 'Macet di jalan',       2, 1, @actor, '2026-05-12 07:48:00', '2026-05-12 07:48:00', '2026-05-12 07:48:00'),
(28, 113, '2026-05-12 07:39:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-12 07:39:00', '2026-05-12 07:39:00', '2026-05-12 07:39:00'),
(29, 128, '2026-05-12 07:44:00', 'Mengantar adik',       2, 1, @actor, '2026-05-12 07:44:00', '2026-05-12 07:44:00', '2026-05-12 07:44:00'),
(30, 143, '2026-05-12 07:50:00', 'Hujan deras',          2, 1, @actor, '2026-05-12 07:50:00', '2026-05-12 07:50:00', '2026-05-12 07:50:00'),
-- Day 4 (2026-05-13)
(31, 9,   '2026-05-13 07:33:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-13 07:33:00', '2026-05-13 07:33:00', '2026-05-13 07:33:00'),
(32, 24,  '2026-05-13 07:41:00', 'Sepeda motor mogok',   2, 1, @actor, '2026-05-13 07:41:00', '2026-05-13 07:41:00', '2026-05-13 07:41:00'),
(33, 39,  '2026-05-13 07:46:00', 'Macet di jalan',       2, 1, @actor, '2026-05-13 07:46:00', '2026-05-13 07:46:00', '2026-05-13 07:46:00'),
(34, 54,  '2026-05-13 07:34:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-13 07:34:00', '2026-05-13 07:34:00', '2026-05-13 07:34:00'),
(35, 69,  '2026-05-13 07:38:00', 'Mengantar adik',       2, 1, @actor, '2026-05-13 07:38:00', '2026-05-13 07:38:00', '2026-05-13 07:38:00'),
(36, 84,  '2026-05-13 07:43:00', 'Hujan deras',          2, 1, @actor, '2026-05-13 07:43:00', '2026-05-13 07:43:00', '2026-05-13 07:43:00'),
(37, 99,  '2026-05-13 07:37:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-13 07:37:00', '2026-05-13 07:37:00', '2026-05-13 07:37:00'),
(38, 114, '2026-05-13 07:49:00', 'Ban sepeda bocor',     2, 1, @actor, '2026-05-13 07:49:00', '2026-05-13 07:49:00', '2026-05-13 07:49:00'),
(39, 129, '2026-05-13 07:35:00', 'Macet di jalan',       2, 1, @actor, '2026-05-13 07:35:00', '2026-05-13 07:35:00', '2026-05-13 07:35:00'),
(40, 144, '2026-05-13 07:42:00', 'Bangun kesiangan',     2, 1, @actor, '2026-05-13 07:42:00', '2026-05-13 07:42:00', '2026-05-13 07:42:00');

-- ================================================================
-- 13. DISPENSASI  (3 rows — OSIS / Pramuka / PMR, status Disetujui)
-- ================================================================
INSERT INTO dispensasi (id, organisasi_id, waktu_mulai, waktu_selesai, kegiatan, lampiran_dispensasi, status_validasi_id, periode_akademik_id, status, user_input, tanggal_input, created_at, updated_at) VALUES
(1, 1, '2026-05-10 08:00:00', '2026-05-10 12:00:00', 'Rapat OSIS bulanan',     NULL, 5, 2, 1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(2, 2, '2026-05-11 07:00:00', '2026-05-11 17:00:00', 'Kemah persiapan jambore',NULL, 5, 2, 1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(3, 3, '2026-05-12 09:00:00', '2026-05-12 14:00:00', 'Pelatihan P3K',          NULL, 5, 2, 1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00');

-- ================================================================
-- 14. DISPENSASI_DETAIL  (15 rows — 5 peserta per dispensasi)
-- ================================================================
INSERT INTO dispensasi_detail (dispensasi_id, siswa_id, status, user_input, tanggal_input, created_at, updated_at) VALUES
-- Dispensasi 1 — OSIS
(1, 1,   1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(1, 16,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(1, 31,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(1, 46,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(1, 61,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
-- Dispensasi 2 — Pramuka
(2, 2,   1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(2, 17,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(2, 32,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(2, 47,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(2, 62,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
-- Dispensasi 3 — PMR
(3, 3,   1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(3, 18,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(3, 33,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(3, 48,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00'),
(3, 63,  1, @actor, '2026-05-09 14:00:00', '2026-05-09 14:00:00', '2026-05-09 14:00:00');

-- ================================================================
-- 15. DAFTAR_TAMU  (5 rows)
-- ================================================================
INSERT INTO daftar_tamu (id, tanggal_kunjungan, nama, lembaga_organisasi, alamat, orang_yang_dituju, tujuan_kunjungan, status, user_input, tanggal_input, created_at, updated_at) VALUES
(1, '2026-05-10', 'Bapak Hartono',  'Orang Tua Siswa',         'Jl. Mawar No. 12, Jakarta',     'Wali Kelas X 1',    'Konsultasi nilai anak',            1, '2', '2026-05-10 09:15:00', '2026-05-10 09:15:00', '2026-05-10 09:15:00'),
(2, '2026-05-10', 'Ibu Siti',       'Orang Tua Siswa',         'Jl. Melati No. 5, Jakarta',     'Wali Kelas X 3',    'Antar surat izin',                 1, '2', '2026-05-10 10:30:00', '2026-05-10 10:30:00', '2026-05-10 10:30:00'),
(3, '2026-05-11', 'Pak Joko',       'Dinas Pendidikan',        'Jl. Sudirman No. 1, Jakarta',   'Kepala Sekolah',    'Monitoring sekolah',               1, '2', '2026-05-11 08:45:00', '2026-05-11 08:45:00', '2026-05-11 08:45:00'),
(4, '2026-05-12', 'Andi Wijaya',    'PT Sumber Buku Sejahtera','Jl. Diponegoro 22, Bandung',    'Bagian Sarpras',    'Penawaran buku perpustakaan',      1, '2', '2026-05-12 11:00:00', '2026-05-12 11:00:00', '2026-05-12 11:00:00'),
(5, '2026-05-13', 'Rina Kusuma',    'Alumni Angkatan 2020',    'Jl. Gajahmada 8, Yogyakarta',   'Wali Kelas X 5',    'Silaturahmi & berbagi pengalaman', 1, '2', '2026-05-13 13:20:00', '2026-05-13 13:20:00', '2026-05-13 13:20:00');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ================================================================
-- DONE.
-- Login credentials (all dummy users):
--   username : walikelasx1 .. walikelasx10
--              ketuakelasx1 .. ketuakelasx10
--   password : password
-- ================================================================
