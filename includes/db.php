<?php
/**
 * Koneksi database menggunakan PDO
 * Sesuaikan konfigurasi di bawah dengan environment Anda.
 */

$DB_HOST = 'localhost';
$DB_NAME = 'student_portal';
$DB_USER = 'root';        // ganti sesuai user MariaDB Anda
$DB_PASS = '';             // ganti sesuai password MariaDB Anda

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}
