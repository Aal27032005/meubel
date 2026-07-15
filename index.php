<?php
/**
 * Dashboard — daftar produk dengan filter kategori dan pagination.
 */

require_once 'config/auth.php';
require_once 'config/db.php';
require_once 'config/helpers.php';

// ── Query parameters ──────────────────────────────────────────────────────────

$page            = max(1, (int) filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1);
$category_filter = trim($_GET['category'] ?? '');
$per_page        = 8;

// ── Build WHERE ───────────────────────────────────────────────────────────────

$where  = '';
$params = [];

if ($category_filter !== '') {
    $where            = 'WHERE category = :category';
    $params[':category'] = $category_filter;
}

// ── Total & paging ────────────────────────────────────────────────────────────

$total_items = (int) $pdo->prepare("SELECT COUNT(*) FROM furniture $where")
    ->execute($params) ? null : null; // dummy — lihat bawah

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM furniture $where");
$count_stmt->execute($params);
$total_items = (int) $count_stmt->fetchColumn();
$total_pages = max(1, (int) ceil($total_items / $per_page));
$page        = min($page, $total_pages);
$offset      = ($page - 1) * $per_page;

// ── Fetch produk ──────────────────────────────────────────────────────────────

$stmt = $pdo->prepare("SELECT * FROM furniture $where ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->execute();
$products = $stmt->fetchAll();

// ── Statistik ringkasan ───────────────────────────────────────────────────────

$stat_total = (int)   $pdo->query('SELECT COUNT(*) FROM furniture')->fetchColumn();
$stat_low   = (int)   $pdo->query('SELECT COUNT(*) FROM furniture WHERE stock <= 5')->fetchColumn();
$stat_units = (int)   $pdo->query('SELECT COALESCE(SUM(stock), 0) FROM furniture')->fetchColumn();
$stat_value = (float) $pdo->query('SELECT COALESCE(SUM(price * stock), 0) FROM furniture')->fetchColumn();

// ── Daftar kategori untuk filter tab ─────────────────────────────────────────

$categories = $pdo->query("SELECT DISTINCT category FROM furniture WHERE category != '' ORDER BY category")
    ->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Jatijaya Furniture</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --cream:       #f3f0e8;
            --cream-dark:  #e8e4d8;
            --brown:       #5c4033;
            --brown-dark:  #3e2723;
            --brown-light: #8d6e63;
        }

        body { background-color: var(--cream); }

        /* ── Hero banner ── */
        .hero {
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            background:
                linear-gradient(rgba(62, 39, 35, 0.52), rgba(62, 39, 35, 0.52)),
                url('https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=1600&q=80')
                center / cover no-repeat;
        }
        .hero h1 { font-weight: 700; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4); }
        .hero p  { opacity: 0.9; text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3); }

        /* ── Stat cards ── */
        .stat-card {
            background: #fff;
            border: none;
            border-radius: 12px;
        }

        /* ── Tabel ── */
        .table-light th {
            background-color: var(--cream-dark) !important;
            color: var(--brown-dark);
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .table-hover tbody tr:hover { background-color: #faf8f3; }

        .img-thumb {
            width: 46px;
            height: 46px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        /* ── Badge stok ── */
        .badge-ok  { background-color: #d4edda; color: #155724; }
        .badge-low { background-color: #f8d7da; color: #58151c; }

        /* ── Tombol ── */
        .btn-brown         { background-color: var(--brown-dark); border-color: var(--brown-dark); color: #fff; }
        .btn-brown:hover   { background-color: var(--brown); border-color: var(--brown); color: #fff; }
        .btn-outline-brown { border-color: var(--brown-dark); color: var(--brown-dark); }
        .btn-outline-brown:hover { background-color: var(--brown-dark); color: #fff; }

        /* ── Pagination ── */
        .page-link { color: var(--brown-dark); }
        .page-item.active .page-link {
            background-color: var(--brown-dark);
            border-color: var(--brown-dark);
        }

        /* ── Filter tab ── */
        .filter-tab        { background: #fff; border-color: var(--cream-dark); color: var(--brown-dark); }
        .filter-tab:hover  { background: var(--cream-dark); border-color: var(--cream-dark); color: var(--brown-dark); }
        .filter-tab.active { background: var(--brown-dark); border-color: var(--brown-dark); color: #fff; }

        /* ── Card / footer ── */
        .card        { border-color: var(--cream-dark); }
        .card-header { background: #fff !important; border-bottom: 1px solid var(--cream-dark); }
        footer       { background-color: var(--brown-dark); color: rgba(255, 255, 255, 0.75); }
    </style>
</head>
<body>

<?php require_once 'config/navbar.php'; ?>

<!-- Hero Banner -->
<div class="hero">
    <div>
        <h1 class="fs-2 mb-2">Jatijaya Furniture</h1>
        <p class="mb-0">Kelola produk Jatijaya Furniture dengan mudah dan efisien</p>
    </div>
</div>

<div class="container py-4">

    <!-- Flash message -->
    <?php if (isset($_GET['status'])): ?>
    <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show py-2"
         role="alert">
        <i class="bi bi-<?php echo $_GET['status'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
        <?php echo e($_GET['message'] ?? ''); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
    <?php endif; ?>

    <!-- Stat cards -->
    <div class="row g-3 mb-4">
        <?php
        $stats = [
            ['label' => 'Total Produk',     'value' => $stat_total,                                 'icon' => 'bi-box-seam',          'color' => 'var(--brown-light)'],
            ['label' => 'Stok Rendah',      'value' => $stat_low,                                   'icon' => 'bi-exclamation-triangle','color' => '#dc3545',         'danger' => true],
            ['label' => 'Total Unit',       'value' => number_format($stat_units),                  'icon' => 'bi-archive',           'color' => 'var(--brown-light)'],
            ['label' => 'Nilai Inventaris', 'value' => 'Rp ' . number_format($stat_value / 1e6, 1, ',', '.') . 'M', 'icon' => 'bi-currency-exchange', 'color' => 'var(--brown-light)', 'small' => true],
        ];
        foreach ($stats as $s):
        ?>
        <div class="col-6 col-md-3">
            <div class="stat-card shadow-sm p-3 h-100">
                <div class="text-muted small mb-1"><?php echo $s['label']; ?></div>
                <div class="fw-bold mb-1 <?php echo !empty($s['small']) ? 'fs-5' : 'fs-2'; ?> <?php echo !empty($s['danger']) ? 'text-danger' : ''; ?>"
                     <?php echo empty($s['danger']) ? 'style="color: var(--brown-dark);"' : ''; ?>>
                    <?php echo $s['value']; ?>
                </div>
                <i class="bi <?php echo $s['icon']; ?> fs-4"
                   style="color: <?php echo $s['color']; ?>;"></i>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabel produk -->
    <div class="card shadow-sm">
        <div class="card-header py-3">

            <!-- Header baris atas -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0 fw-bold" style="color: var(--brown-dark);">Daftar Produk</h5>
                <div class="d-flex gap-2">
                    <a href="create.php" class="btn btn-brown btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Tambah
                    </a>
                    <a href="export.php" class="btn btn-outline-brown btn-sm">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export
                    </a>
                </div>
            </div>

            <!-- Filter kategori -->
            <?php if (!empty($categories)): ?>
            <div class="mt-3 d-flex flex-wrap gap-1">
                <a href="index.php"
                   class="btn btn-sm filter-tab <?php echo $category_filter === '' ? 'active' : ''; ?>">
                    Semua
                </a>
                <?php foreach ($categories as $cat): ?>
                <a href="index.php?category=<?php echo urlencode($cat); ?>"
                   class="btn btn-sm filter-tab <?php echo $category_filter === $cat ? 'active' : ''; ?>">
                    <?php echo e($cat); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div><!-- /card-header -->

        <div class="card-body p-0">

            <?php if (empty($products)): ?>

            <!-- Empty state -->
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                <p class="mb-2">
                    <?php echo $category_filter ? 'Tidak ada produk di kategori ini.' : 'Belum ada produk.'; ?>
                </p>
                <?php if ($category_filter): ?>
                <a href="index.php" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
                <?php endif; ?>
            </div>

            <?php else: ?>

            <!-- Tabel -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 40px;">#</th>
                            <th>Produk</th>
                            <th class="d-none d-md-table-cell">Kategori</th>
                            <th class="d-none d-md-table-cell">Material</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th class="text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $i => $product):
                        $img = $product['image_path']
                            ?: ($product['image_url']
                            ?: 'https://placehold.co/48x48?text=?');
                    ?>
                        <tr>
                            <td class="ps-3 text-muted small"><?php echo $offset + $i + 1; ?></td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo e($img); ?>"
                                         alt="<?php echo e($product['name']); ?>"
                                         class="img-thumb">
                                    <div>
                                        <div class="fw-semibold" style="color: var(--brown-dark);">
                                            <?php echo e($product['name']); ?>
                                        </div>
                                        <div class="text-muted small">SKU: <?php echo e($product['sku']); ?></div>
                                    </div>
                                </div>
                            </td>

                            <td class="d-none d-md-table-cell text-muted small">
                                <?php echo e($product['category']); ?>
                            </td>

                            <td class="d-none d-md-table-cell">
                                <span class="badge" style="background: #c8b9a8; color: #3e2723;">
                                    <?php echo e($product['material']); ?>
                                </span>
                            </td>

                            <td class="fw-semibold small">
                                <?php echo rupiah((float) $product['price']); ?>
                            </td>

                            <td>
                                <?php if ($product['stock'] > 5): ?>
                                <span class="badge badge-ok"><?php echo $product['stock']; ?> unit</span>
                                <?php else: ?>
                                <span class="badge badge-low">
                                    <i class="bi bi-exclamation me-1"></i><?php echo $product['stock']; ?> unit
                                </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="detail.php?id=<?php echo $product['id']; ?>"
                                       class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $product['id']; ?>"
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <!-- Hapus via POST form -->
                                    <form action="delete.php" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus produk \'<?php echo e(addslashes($product['name'])); ?>\'?')">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top"
                 style="background: var(--cream);">
                <small class="text-muted">
                    <?php echo $offset + 1; ?>–<?php echo min($offset + $per_page, $total_items); ?>
                    dari <?php echo $total_items; ?> produk
                </small>
                <nav aria-label="Navigasi halaman">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link"
                               href="?page=<?php echo $page - 1; ?>&category=<?php echo urlencode($category_filter); ?>"
                               aria-label="Sebelumnya">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
                            <a class="page-link"
                               href="?page=<?php echo $p; ?>&category=<?php echo urlencode($category_filter); ?>">
                                <?php echo $p; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link"
                               href="?page=<?php echo $page + 1; ?>&category=<?php echo urlencode($category_filter); ?>"
                               aria-label="Berikutnya">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>

            <?php endif; // end empty check ?>

        </div><!-- /card-body -->
    </div><!-- /card -->

</div><!-- /container -->

<footer class="py-3 mt-5 text-center">
    <small>&copy; <?php echo date('Y'); ?> Jatijaya Furniture &mdash; Sistem Manajemen Inventaris</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
