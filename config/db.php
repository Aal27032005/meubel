<?php
/**
 * Koneksi database menggunakan PDO.
 * Variabel $pdo tersedia secara global setelah file ini di-include.
 */

define('DB_HOST',    '127.0.0.1');
define('DB_NAME',    'db_meubel');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    DB_HOST,
    DB_NAME,
    DB_CHARSET
);

$pdo_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdo_options);
} catch (PDOException $e) {
    // Jangan tampilkan detail error ke pengguna di produksi
    error_log('DB connection failed: ' . $e->getMessage());
    die('Koneksi database gagal. Silakan coba beberapa saat lagi.');
}
