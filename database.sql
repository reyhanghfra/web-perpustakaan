-- ============================================================
-- DATABASE: perpustakaan_mini
-- Project UAS Pemrograman Web
-- Versi FINAL - Semua tabel lengkap, data dummy siap pakai
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS perpustakaan_mini
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE perpustakaan_mini;

-- Hapus tabel lama jika ada (urutan penting karena ada FK)
DROP TABLE IF EXISTS detail_peminjaman;
DROP TABLE IF EXISTS peminjaman;
DROP TABLE IF EXISTS booking;
DROP TABLE IF EXISTS users_anggota;
DROP TABLE IF EXISTS buku;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS anggota;
DROP TABLE IF EXISTS users;

-- ============================================================
-- TABEL: users  (admin / petugas)
-- ============================================================
CREATE TABLE users (
  id_user    INT AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(50)  NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  nama       VARCHAR(100) NOT NULL,
  role       ENUM('admin','petugas') NOT NULL DEFAULT 'petugas',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: kategori
-- ============================================================
CREATE TABLE kategori (
  id_kategori   INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: buku
-- ============================================================
CREATE TABLE buku (
  id_buku     INT AUTO_INCREMENT PRIMARY KEY,
  judul       VARCHAR(200) NOT NULL,
  penulis     VARCHAR(100) NOT NULL,
  penerbit    VARCHAR(100) NOT NULL,
  tahun       YEAR         NOT NULL,
  stok        INT          NOT NULL DEFAULT 0,
  cover       VARCHAR(255) DEFAULT NULL,
  id_kategori INT          NOT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_buku_kategori FOREIGN KEY (id_kategori)
    REFERENCES kategori(id_kategori)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: anggota
-- ============================================================
CREATE TABLE anggota (
  id_anggota INT AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(100) NOT NULL,
  alamat     TEXT         NOT NULL,
  no_hp      VARCHAR(20)  NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: users_anggota  (akun login untuk anggota)
-- ============================================================
CREATE TABLE users_anggota (
  id_users_anggota INT AUTO_INCREMENT PRIMARY KEY,
  id_anggota       INT          NOT NULL,
  username         VARCHAR(100) NOT NULL UNIQUE,
  password         VARCHAR(255) NOT NULL,
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_anggota FOREIGN KEY (id_anggota)
    REFERENCES anggota(id_anggota)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: booking
-- ============================================================
CREATE TABLE booking (
  id_booking      INT AUTO_INCREMENT PRIMARY KEY,
  kode_booking    VARCHAR(30) NOT NULL UNIQUE,
  id_anggota      INT         NOT NULL,
  id_buku         INT         NOT NULL,
  tanggal_booking DATE        NOT NULL,
  tanggal_expired DATE        NOT NULL,
  status          ENUM('Booking','Diambil','Dibatalkan') NOT NULL DEFAULT 'Booking',
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_booking_anggota FOREIGN KEY (id_anggota)
    REFERENCES anggota(id_anggota)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_booking_buku FOREIGN KEY (id_buku)
    REFERENCES buku(id_buku)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: peminjaman
-- ============================================================
CREATE TABLE peminjaman (
  id_peminjaman        INT AUTO_INCREMENT PRIMARY KEY,
  kode_booking         VARCHAR(30)  DEFAULT NULL,
  id_anggota           INT          NOT NULL,
  id_buku              INT          NOT NULL,
  tanggal_pinjam       DATE         NOT NULL,
  tanggal_kembali      DATE         NOT NULL,
  tanggal_dikembalikan DATE         DEFAULT NULL,
  status               ENUM('Diambil','Sedang Dipinjam','Kembali') NOT NULL DEFAULT 'Diambil',
  created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_peminjaman_anggota FOREIGN KEY (id_anggota)
    REFERENCES anggota(id_anggota)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_peminjaman_buku FOREIGN KEY (id_buku)
    REFERENCES buku(id_buku)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DATA DUMMY: users
-- password plain: "admin123"  (bcrypt hash)
-- ============================================================
INSERT INTO users (username, password, nama, role) VALUES
('admin',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator',        'admin'),
('petugas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Petugas Perpustakaan', 'petugas');

-- ============================================================
-- DATA DUMMY: kategori
-- ============================================================
INSERT INTO kategori (nama_kategori) VALUES
('Novel'),
('Pendidikan'),
('Teknologi'),
('Komik'),
('Sains'),
('Sejarah'),
('Agama'),
('Anak-anak'),
('Motivasi'),
('Pengembangan Diri');

-- ============================================================
-- DATA DUMMY: buku
-- ============================================================
INSERT INTO buku (judul, penulis, penerbit, tahun, stok, id_kategori) VALUES
('Laskar Pelangi',              'Andrea Hirata',          'Bentang Pustaka',   2005, 5, 1),
('Bumi Manusia',                'Pramoedya Ananta Toer',  'Lentera Dipantara', 1980, 3, 1),
('Algoritma dan Pemrograman',   'Rinaldi Munir',           'Informatika',       2016, 4, 3),
('Dasar-Dasar Pemrograman Web', 'Betha Sidik',             'Informatika',       2019, 6, 3),
('Harry Potter: Batu Bertuah',  'J.K. Rowling',            'Gramedia',          2000, 2, 1),
('Fisika Dasar Jilid 1',        'Halliday & Resnick',      'Erlangga',          2014, 4, 5),
('Sejarah Indonesia Modern',    'M.C. Ricklefs',           'Serambi',           2008, 3, 6),
('Fiqih Islam Lengkap',         'Sulaiman Rasjid',         'Sinar Baru',        2015, 5, 7),
('Doraemon Vol. 1',             'Fujiko F. Fujio',         'Elex Media',        2010, 7, 4),
('Matematika Diskrit',          'Rinaldi Munir',            'Informatika',       2017, 3, 2),
('Pemrograman PHP & MySQL',     'Budi Raharjo',             'Informatika',       2020, 5, 3),
('Si Kancil dan Buaya',         'Folklore Indonesia',       'Erlangga',          2012, 8, 8);

-- ============================================================
-- DATA DUMMY: anggota
-- ============================================================
INSERT INTO anggota (nama, alamat, no_hp) VALUES
('Andi Saputra',  'Jl. Melati No. 12, Bekasi',       '081234567801'),
('Budi Santoso',  'Jl. Mawar No. 5, Jakarta Timur',  '081234567802'),
('Citra Dewi',    'Jl. Kenanga No. 8, Depok',        '081234567803'),
('Dimas Pratama', 'Jl. Anggrek No. 3, Tangerang',    '081234567804'),
('Eka Rahayu',    'Jl. Flamboyan No. 17, Bekasi',    '081234567805'),
('Fajar Nugroho', 'Jl. Dahlia No. 22, Bogor',        '081234567806'),
('Gita Puspita',  'Jl. Bougenville No. 9, Jakarta',  '081234567807'),
('Hendra Wijaya', 'Jl. Teratai No. 14, Bekasi',      '081234567808'),
('Indah Permata', 'Jl. Cempaka No. 6, Depok',        '081234567809'),
('Joko Sutrisno', 'Jl. Seruni No. 11, Tangerang',    '081234567810'),
('Kiki Amalia',   'Jl. Tulip No. 4, Jakarta Barat',  '081234567811'),
('Lina Marlina',  'Jl. Lavender No. 19, Bekasi',     '081234567812');

-- ============================================================
-- DATA DUMMY: users_anggota
-- password plain: "anggota123"  (bcrypt hash)
-- ============================================================
INSERT INTO users_anggota (id_anggota, username, password) VALUES
(1, 'Andi Saputra', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lh6y'),
(2, 'Budi Santoso', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lh6y'),
(3, 'Citra Dewi',   '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lh6y');

-- ============================================================
-- DATA DUMMY: booking
-- ============================================================
INSERT INTO booking (kode_booking, id_anggota, id_buku, tanggal_booking, tanggal_expired, status) VALUES
('BK-2026060101', 1, 3, '2026-06-01', '2026-06-08', 'Diambil'),
('BK-2026060201', 2, 5, '2026-06-02', '2026-06-09', 'Booking'),
('BK-2026060301', 3, 1, '2026-06-03', '2026-06-10', 'Booking');

-- ============================================================
-- DATA DUMMY: peminjaman
-- ============================================================
INSERT INTO peminjaman (kode_booking, id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, tanggal_dikembalikan, status) VALUES
('BK-2026060101', 1, 3, '2026-06-02', '2026-06-16', NULL,         'Diambil'),
(NULL,            2, 1, '2026-05-20', '2026-06-03', '2026-06-03', 'Kembali');

-- ============================================================
-- SELESAI - Import berhasil
-- ============================================================