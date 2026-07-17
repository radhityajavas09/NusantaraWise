-- ============================================
-- SCRIPT PERBAIKAN DATABASE NusantaraWise
-- Jalankan script ini di phpMyAdmin atau MySQL CLI
-- ============================================

USE nusantarawise_db;

-- ============================================
-- 1. UPDATE PASSWORD ADMIN
-- Password baru: admin123
-- Hash yang benar untuk "admin123"
-- ============================================
UPDATE users 
SET password = '$2y$10$TKh8H1.PfuDlDjqCMnRqquqX7BVTZ3ZWmyZ7aQzY7uSvOmP.q7ypC'
WHERE email = 'admin@nusantarawise.id' AND role = 'admin';

-- Jika admin belum ada, insert baru
INSERT IGNORE INTO users (nama, email, password, role) VALUES
('Administrator', 'admin@nusantarawise.id', '$2y$10$TKh8H1.PfuDlDjqCMnRqquqX7BVTZ3ZWmyZ7aQzY7uSvOmP.q7ypC', 'admin');

-- ============================================
-- 2. HAPUS DUPLIKAT DESTINASI
-- Simpan hanya ID terkecil (pertama) per nama
-- ============================================
DELETE d1 FROM destinasi d1
INNER JOIN destinasi d2
WHERE d1.id > d2.id AND d1.nama = d2.nama;

-- ============================================
-- 3. HAPUS DUPLIKAT GALERI
-- Simpan hanya ID terkecil (pertama) per title
-- ============================================
DELETE g1 FROM galeri g1
INNER JOIN galeri g2
WHERE g1.id > g2.id AND g1.title = g2.title;

-- ============================================
-- VERIFIKASI HASIL
-- ============================================
SELECT 'Akun Admin:' as info;
SELECT id, nama, email, role FROM users WHERE role = 'admin';

SELECT 'Jumlah Destinasi:' as info;
SELECT COUNT(*) as total FROM destinasi;

SELECT 'Daftar Destinasi:' as info;
SELECT id, nama, kategori FROM destinasi ORDER BY id;

SELECT 'Jumlah Galeri:' as info;
SELECT COUNT(*) as total FROM galeri;

SELECT 'Daftar Galeri:' as info;
SELECT id, title, loc FROM galeri ORDER BY id;
