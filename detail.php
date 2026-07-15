<?php
require_once 'config/auth.php';
require_once 'config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php"); exit;
}
$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM furniture WHERE id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch();
if (!$item) {
    header("Location: index.php?status=error&message=Produk+tidak+ditemukan"); exit;
}

$img = !empty($item['image_path']) ? $item['image_path']
     : (!empty($item['image_url']) ? $item['image_url']
     : 'https://placehold.co/500x350?text=No+Image');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($item['name']); ?> - Jatijaya Furniture</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --cream:#f3f0e8; --cream-dark:#e8e4d8; --brown:#5c4033; --brown-dark:#3e2723; --brown-light:#8d6e63; }
        body { background-color: var(--cream); }
        .card { border-color: var(--cream-dark); }
        .detail-img { width:100%; max-height:320px; object-fit:cover; border-radius:12px; }
        footer { background-color: var(--brown-dark); color: rgba(255,255,255,0.75); }
    </style>
</head>
<body>

<?php require_once 'config/navbar.php'; ?>

<div class="container py-4">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($item['name']); ?></li>
        </ol>
    </nav>

    <div class="row g-4">

        <!-- Gambar -->
        <div class="col-md-5">
            <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="detail-img shadow-sm">
        </div>

        <!-- Info -->
        <div class="col-md-7">
            <div class="card shadow-sm h-100" style="border-color:var(--cream-dark)">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                        <span class="badge" style="background:#c8b9a8;color:#3e2723"><?php echo htmlspecialchars($item['category']); ?></span>
                        <?php if ($item['stock'] > 5): ?>
                        <span class="badge bg-success">Tersedia: <?php echo $item['stock']; ?> unit</span>
                        <?php else: ?>
                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Stok Rendah: <?php echo $item['stock']; ?> unit</span>
                        <?php endif; ?>
                    </div>

                    <h2 class="h4 fw-bold mb-1" style="color:var(--brown-dark)"><?php echo htmlspecialchars($item['name']); ?></h2>
                    <p class="text-muted small mb-2">SKU: <strong><?php echo htmlspecialchars($item['sku']); ?></strong></p>
                    <p class="h5 fw-bold mb-3" style="color:var(--brown-dark)">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>

                    <hr style="border-color:var(--cream-dark)">

                    <table class="table table-sm table-borderless mb-3 small">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width:120px">Material</td>
                                <td><strong><?php echo htmlspecialchars($item['material']); ?></strong></td>
                            </tr>
                            <?php if (!empty($item['dimensions'])): ?>
                            <tr>
                                <td class="text-muted">Dimensi</td>
                                <td><?php echo htmlspecialchars($item['dimensions']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-muted">Ditambahkan</td>
                                <td><?php echo date('d M Y', strtotime($item['created_at'])); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <?php if (!empty($item['description'])): ?>
                    <p class="text-muted small"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="delete.php?id=<?php echo $item['id']; ?>"
                           onclick="return confirm('Hapus produk ini?')"
                           class="btn btn-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<footer class="py-3 mt-5 text-center">
    <small>&copy; 2025 Jatijaya Furniture &mdash; Sistem Manajemen Inventaris</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
