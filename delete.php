<?php
/**
 * Hapus produk — hanya menerima POST untuk mencegah penghapusan via link.
 */

require_once 'config/auth.php';
require_once 'config/db.php';
require_once 'config/helpers.php';

// Hanya izinkan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

csrf_verify();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirect('index.php', 'error', 'ID produk tidak valid.');
}

try {
    // Ambil data produk sebelum dihapus (untuk nama dan path gambar)
    $stmt = $pdo->prepare('SELECT id, name, image_path FROM furniture WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $item = $stmt->fetch();

    if (!$item) {
        redirect('index.php', 'error', 'Produk tidak ditemukan.');
    }

    // Hapus file gambar dari disk jika ada
    delete_image($item['image_path']);

    // Hapus record dari database
    $pdo->prepare('DELETE FROM furniture WHERE id = :id')
        ->execute([':id' => $id]);

    redirect('index.php', 'success', 'Produk "' . $item['name'] . '" berhasil dihapus.');

} catch (PDOException $e) {
    error_log('Delete product failed: ' . $e->getMessage());
    redirect('index.php', 'error', 'Gagal menghapus produk. Silakan coba lagi.');
}
