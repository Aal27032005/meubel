<?php
/**
 * Auth guard — sertakan di setiap halaman yang membutuhkan login.
 * Memastikan session aktif dan user sudah terautentikasi.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
