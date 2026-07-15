<?php
require_once 'config/auth.php';
require_once 'config/db.php';

// Pagination
$limit  = 8;
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filter kategori saja (no search)
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
$where_sql = '';
$params    = [];
if ($category_filter !== '') {
    $where_sql = 'WHERE category = :category';
    $params[':category'] = $category_filter;
}

// Total count
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM furniture $where_sql");
$count_stmt->execute($params);
$total_items = (int)$count_stmt->fetchColumn();
$total_pages = max(1, (int)ceil($total_items / $limit));
if ($page > $total_pages) { $page = $total_pages; $offset = ($page - 1) * $limit; }

// Fetch items
$sql = "SELECT * FROM furniture $where_sql ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->execute();
$items = $stmt->fetchAll();

// Stats
$stat_total = (int)$pdo->query("SELECT COUNT(*) FROM furniture")->fetchColumn();
$stat_low   = (int)$pdo->query("SELECT COUNT(*) FROM furniture WHERE stock <= 5")->fetchColumn();
$stat_units = (int)$pdo->query("SELECT COALESCE(SUM(stock),0) FROM furniture")->fetchColumn();
$stat_value = (float)$pdo->query("SELECT COALESCE(SUM(price*stock),0) FROM furniture")->fetchColumn();

// Categories
$categories = $pdo->query("SELECT DISTINCT category FROM furniture WHERE category != '' ORDER BY category")
                   ->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jatijaya Furniture - Dashboard</title>
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
        body            { background-color: var(--cream); }
        /* ── Hero ── */
        .hero-section {
            position: relative;
            height: 280px;
            background:
                linear-gradient(rgba(62,39,35,0.50), rgba(62,39,35,0.55)),
                url('https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=1600&q=80')
                center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
        }
        .hero-section h1 { font-weight: 700; letter-spacing: 0.5px; text-shadow: 0 2px 8px rgba(0,0,0,0.4); }
        .hero-section p  { opacity: 0.88; text-shadow: 0 1px 4px rgba(0,0,0,0.3); }
        /* ── Cards ── */
        .stat-card  { border: none; border-radius: 12px; background: #fff; }
        .card       { border-color: var(--cream-dark); }
        .card-header{ background: #fff !important; border-bottom: 1px solid var(--cream-dark); }
        /* ── Table ── */
        .table-light th { background-color: var(--cream-dark) !important; color: var(--brown-dark); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .table-hover tbody tr:hover { background-color: #faf8f3; }
        .img-thumb  { width: 46px; height: 46px; object-fit: cover; border-radius: 8px; }
        /* ── Badges ── */
        .badge-ok   { background-color: #d4edda; color: #155724; }
        .badge-low  { background-color: #f8d7da; color: #58151c; }
        /* ── Buttons ── */
        .btn-brown  { background-color: var(--brown-dark); border-color: var(--brown-dark); color: #fff; }
        .btn-brown:hover  { background-color: var(--brown); border-color: var(--brown); color: #fff; }
        .btn-outline-brown { border-color: var(--brown-dark); color: var(--brown-dark); }
        .btn-outline-brown:hover { background-color: var(--brown-dark); color: #fff; }
        /* ── Pagination ── */
        .page-link { color: var(--brown-dark); }
        .page-item.active .page-link { background-color: var(--brown-dark); border-color: var(--brown-dark); }
        /* ── Filter tabs ── */
        .filter-tab         { border-color: var(--cream-dark); color: var(--brown-dark); background: #fff; }
        .filter-tab:hover   { background: var(--cream-dark); color: var(--brown-dark); border-color: var(--cream-dark); }
        .filter-tab.active  { background: var(--brown-dark); border-color: var(--brown-dark); color: #fff; }
        /* ── Footer ── */
        footer { background-color: var(--brown-dark); color: rgba(255,255,255,0.75); }
    </style>
</head>
<body>

<?php require_once 'config/navbar.php'; ?>

<!-- Hero Banner -->
<div class="hero-section">
    <div>
        <h1 class="fs-2 fs-md-1 mb-2">Jatijaya Furniture</h1>
        <p class="mb-0 fs-6">Kelola produk Jatijaya Furniture dengan mudah dan efisien</p>
    </div>
</div>

<div class="container py-4">

    <!-- Alert -->
    <?php if (isset($_GET['status'])): ?>
    <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show py-2" role="alert">
        <i class="bi bi-<?php echo $_GET['status'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
        <?php echo htmlspecialchars($_GET['message'] ?? ''); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card shadow-sm p-3 h-100">
                <div class="text-muted small mb-1">Total Produk</div>
                <div class="fs-2 fw-bold" style="color:var(--brown-dark)"><?php echo $stat_total; ?></div>
                <i class="bi bi-box-seam fs-4 mt-1" style="color:var(--brown-light)"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card shadow-sm p-3 h-100">
                <div class="text-muted small mb-1">Stok Rendah</div>
                <div class="fs-2 fw-bold text-danger"><?php echo $stat_low; ?></div>
                <i class="bi bi-exclamation-triangle fs-4 mt-1 text-danger"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card shadow-sm p-3 h-100">
                <div class="text-muted small mb-1">Total Unit</div>
                <div class="fs-2 fw-bold" style="color:var(--brown-dark)"><?php echo number_format($stat_units); ?></div>
                <i class="bi bi-archive fs-4 mt-1" style="color:var(--brown-light)"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card shadow-sm p-3 h-100">
                <div class="text-muted small mb-1">Nilai Inventaris</div>
                <div class="fs-5 fw-bold" style="color:var(--brown-dark)">Rp <?php echo number_format($stat_value / 1e6, 1, ',', '.'); ?>M</div>
                <i class="bi bi-currency-exchange fs-4 mt-1" style="color:var(--brown-light)"></i>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-header py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0 fw-bold" style="color:var(--brown-dark)">Daftar Produk</h5>
                <div class="d-flex gap-2">
                    <a href="create.php" class="btn btn-brown btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Tambah
                    </a>
                    <a href="export.php" class="btn btn-outline-brown btn-sm">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export
                    </a>
                </div>
            </div>

            <!-- Filter Kategori -->
            <?php if (!empty($categories)): ?>
            <div class="mt-3 d-flex flex-wrap gap-1">
                <a href="index.php"
                   class="btn btn-sm filter-tab <?php echo $category_filter === '' ? 'active' : ''; ?>">
                    Semua
                </a>
                <?php foreach ($categories as $cat): ?>
                <a href="index.php?category=<?php echo urlencode($cat); ?>"
                   class="btn btn-sm filter-tab <?php echo $category_filter === $cat ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="card-body p-0">
            <?php if (empty($items)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                <p>Belum ada produk<?php echo $category_filter ? ' pada kategori ini' : ''; ?>.</p>
                <a href="index.php" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:40px">#</th>
                            <th>Produk</th>
                            <th class="d-none d-md-table-cell">Kategori</th>
                            <th class="d-none d-md-table-cell">Material</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th class="text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $i => $item):
                        $img = !empty($item['image_path']) ? $item['image_path']
                             : (!empty($item['image_url']) ? $item['image_url']
                             : 'https://placehold.co/48x48?text=?');
                    ?>
                        <tr>
                            <td class="ps-3 text-muted small"><?php echo $offset + $i + 1; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo $img; ?>" alt="" class="img-thumb">
                                    <div>
                                        <div class="fw-semibold" style="color:var(--brown-dark)"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="text-muted small">SKU: <?php echo htmlspecialchars($item['sku']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell text-muted small"><?php echo htmlspecialchars($item['category']); ?></td>
                            <td class="d-none d-md-table-cell">
                                <span class="badge" style="background:#c8b9a8;color:#3e2723"><?php echo htmlspecialchars($item['material']); ?></span>
                            </td>
                            <td class="fw-semibold small">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($item['stock'] > 5): ?>
                                <span class="badge badge-ok"><?php echo $item['stock']; ?> unit</span>
                                <?php else: ?>
                                <span class="badge badge-low"><i class="bi bi-exclamation me-1"></i><?php echo $item['stock']; ?> unit</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="detail.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <a href="delete.php?id=<?php echo $item['id']; ?>"
                                       onclick="return confirm('Hapus \'<?php echo htmlspecialchars(addslashes($item['name'])); ?>\'?')"
                                       class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top" style="background:var(--cream)">
                <small class="text-muted">
                    <?php echo $offset+1; ?>–<?php echo min($offset+$limit, $total_items); ?> dari <?php echo $total_items; ?> produk
                </small>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>&category=<?php echo urlencode($category_filter); ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $p; ?>&category=<?php echo urlencode($category_filter); ?>"><?php echo $p; ?></a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>&category=<?php echo urlencode($category_filter); ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /container -->

<footer class="py-3 mt-5 text-center">
    <small>&copy; 2025 Jatijaya Furniture &mdash; Sistem Manajemen Inventaris</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
