-- ============================================================
-- Skema Database: Dashboard Mahasiswa & Admin Panel
-- Database: MariaDB
-- ============================================================

CREATE DATABASE IF NOT EXISTS student_portal
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE student_portal;

-- ------------------------------------------------------------
-- Tabel mahasiswa: data induk mahasiswa
-- ------------------------------------------------------------
CREATE TABLE mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    program_studi VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabel users: akun login (admin & mahasiswa)
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,      -- hasil password_hash() PHP (bcrypt)
    role ENUM('admin', 'mahasiswa') NOT NULL DEFAULT 'mahasiswa',
    mahasiswa_id INT DEFAULT NULL,        -- NULL untuk admin
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabel mata_kuliah: daftar master mata kuliah
-- ------------------------------------------------------------
CREATE TABLE mata_kuliah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_mk VARCHAR(20) NOT NULL UNIQUE,
    nama_mk VARCHAR(150) NOT NULL,
    sks INT NOT NULL DEFAULT 3
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabel nilai: relasi mahasiswa - mata kuliah yang telah diambil
-- ------------------------------------------------------------
CREATE TABLE nilai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    mata_kuliah_id INT NOT NULL,
    nilai_huruf ENUM('A','A-','B+','B','B-','C+','C','D','E') NOT NULL,
    semester VARCHAR(30) DEFAULT NULL,   -- contoh: "Ganjil 2024/2025"
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE CASCADE,
    UNIQUE KEY unique_ambil (mahasiswa_id, mata_kuliah_id, semester)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Seed data: akun admin default
-- Username: admin
-- Password: admin123   (SEGERA GANTI setelah login pertama kali!)
-- ------------------------------------------------------------
INSERT INTO users (username, password, role, mahasiswa_id) VALUES
('admin', '$2b$10$7Fsk4EQr0mO7QM.6m9P2kuCOzABjkFdGUfObAPXLCjZ63xPGNcAH6', 'admin', NULL);

-- ------------------------------------------------------------
-- Contoh data mata kuliah (opsional, boleh dihapus)
-- ------------------------------------------------------------
INSERT INTO mata_kuliah (kode_mk, nama_mk, sks) VALUES
('IF101', 'Algoritma dan Pemrograman', 3),
('IF102', 'Struktur Data', 3),
('IF201', 'Basis Data', 3),
('IF202', 'Pemrograman Web', 3),
('MK101', 'Kalkulus I', 4);
