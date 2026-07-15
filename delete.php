<?php
require_once 'config/auth.php';
require_once 'config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php"); exit;
}
$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM furniture WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $item = $stmt->fetch();

    if (!$item) {
        header("Location: index.php?status=error&message=Produk+tidak+ditemukan"); exit;
    }

    if (!empty($item['image_path']) && file_exists($item['image_path'])) {
        @unlink($item['image_path']);
    }

    $pdo->prepare("DELETE FROM furniture WHERE id = :id")->execute([':id' => $id]);

    header("Location: index.php?status=success&message=Produk+" . urlencode($item['name']) . "+berhasil+dihapus");
    exit;

} catch (\PDOException $e) {
    header("Location: index.php?status=error&message=Gagal+menghapus:+" . urlencode($e->getMessage()));
    exit;
}
