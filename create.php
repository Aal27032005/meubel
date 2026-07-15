<?php
require_once 'config/auth.php';
require_once 'config/db.php';
require_once 'config/helpers.php';

$errors = [];
$data = ['sku'=>'','name'=>'','category'=>'','material'=>'','price'=>'','stock'=>'','image_url'=>'','description'=>'','dimensions'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    foreach ($data as $k => $_) $data[$k] = trim($_POST[$k] ?? '');

    if (empty($data['sku']))      $errors['sku']      = 'SKU wajib diisi.';
    if (empty($data['name']))     $errors['name']     = 'Nama produk wajib diisi.';
    if (empty($data['category'])) $errors['category'] = 'Kategori wajib diisi.';
    if (empty($data['material'])) $errors['material'] = 'Material wajib diisi.';
    if (!is_numeric($data['price']) || $data['price'] < 0)
                                  $errors['price']    = 'Harga harus angka positif.';
    if (!is_numeric($data['stock']) || $data['stock'] < 0)
                                  $errors['stock']    = 'Stok harus angka positif atau nol.';

    if (empty($errors['sku'])) {
        $chk = $pdo->prepare("SELECT COUNT(*) FROM furniture WHERE sku = :sku");
        $chk->execute([':sku' => $data['sku']]);
        if ($chk->fetchColumn() > 0) $errors['sku'] = 'SKU sudah terdaftar.';
    }

    $image_path = null;
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
            $errors['image_file'] = 'Format harus JPG, PNG, atau WEBP.';
        } elseif ($_FILES['image_file']['size'] > 5 * 1024 * 1024) {
            $errors['image_file'] = 'Ukuran file maksimal 5MB.';
        } else {
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            $fname = 'meubel_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], 'uploads/' . $fname)) {
                $image_path = 'uploads/' . $fname;
            } else {
                $errors['image_file'] = 'Gagal menyimpan file.';
            }
        }
    }

    if (empty($errors)) {
        $pdo->prepare("INSERT INTO furniture (sku,name,category,material,price,stock,image_path,image_url,description,dimensions)
                       VALUES (:sku,:name,:category,:material,:price,:stock,:image_path,:image_url,:description,:dimensions)")
            ->execute([
                ':sku'         => $data['sku'],
                ':name'        => $data['name'],
                ':category'    => $data['category'],
                ':material'    => $data['material'],
                ':price'       => $data['price'],
                ':stock'       => $data['stock'],
                ':image_path'  => $image_path,
                ':image_url'   => $data['image_url'] ?: null,
                ':description' => $data['description'] ?: null,
                ':dimensions'  => $data['dimensions'] ?: null,
            ]);
        header("Location: index.php?status=success&message=Produk+berhasil+ditambahkan");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Produk - Jatijaya Furniture</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --cream:#f3f0e8; --cream-dark:#e8e4d8; --brown:#5c4033; --brown-dark:#3e2723; --brown-light:#8d6e63; }
        body { background-color: var(--cream); }
        .card { border-color: var(--cream-dark); }
        .card-header { background:#fff !important; border-bottom:1px solid var(--cream-dark); }
        .btn-brown { background-color:var(--brown-dark); border-color:var(--brown-dark); color:#fff; }
        .btn-brown:hover { background-color:var(--brown); border-color:var(--brown); color:#fff; }
        .form-control:focus, .form-select:focus { border-color:var(--brown-light); box-shadow:0 0 0 .2rem rgba(141,110,99,.25); }
        footer { background-color: var(--brown-dark); color: rgba(255,255,255,0.75); }
    </style>
</head>
<body>

<?php require_once 'config/navbar.php'; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tambah Produk</li>
                </ol>
            </nav>

            <?php if (!empty($errors['db'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errors['db']); ?></div>
            <?php endif; ?>

            <form action="create.php" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="card shadow-sm">
                    <div class="card-header py-3">
                        <h5 class="mb-0 fw-bold" style="color:var(--brown-dark)">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Produk Baru
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">SKU <span class="text-danger">*</span></label>
                                <input type="text" name="sku" value="<?php echo htmlspecialchars($data['sku']); ?>"
                                       class="form-control <?php echo isset($errors['sku']) ? 'is-invalid' : ''; ?>"
                                       placeholder="Contoh: NT-0923">
                                <?php if (isset($errors['sku'])): ?><div class="invalid-feedback"><?php echo $errors['sku']; ?></div><?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($data['name']); ?>"
                                       class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                                       placeholder="Contoh: Meja Makan Nusa">
                                <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?php echo $errors['name']; ?></div><?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Kategori <span class="text-danger">*</span></label>
                                <select name="category" class="form-select <?php echo isset($errors['category']) ? 'is-invalid' : ''; ?>">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach (['Ruang Makan','Ruang Tamu','Kamar Tidur','Pencahayaan','Dekorasi'] as $cat): ?>
                                    <option value="<?php echo $cat; ?>" <?php echo $data['category'] === $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['category'])): ?><div class="invalid-feedback"><?php echo $errors['category']; ?></div><?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Material <span class="text-danger">*</span></label>
                                <select name="material" class="form-select <?php echo isset($errors['material']) ? 'is-invalid' : ''; ?>">
                                    <option value="">-- Pilih Material --</option>
                                    <?php foreach (['Kayu Jati','Kayu Mahoni','Rotan','Kayu Ash','Bambu'] as $mat): ?>
                                    <option value="<?php echo $mat; ?>" <?php echo $data['material'] === $mat ? 'selected' : ''; ?>><?php echo $mat; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['material'])): ?><div class="invalid-feedback"><?php echo $errors['material']; ?></div><?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="price" value="<?php echo htmlspecialchars($data['price']); ?>"
                                       class="form-control <?php echo isset($errors['price']) ? 'is-invalid' : ''; ?>"
                                       min="0" placeholder="Contoh: 3750000">
                                <?php if (isset($errors['price'])): ?><div class="invalid-feedback"><?php echo $errors['price']; ?></div><?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Stok <span class="text-danger">*</span></label>
                                <input type="number" name="stock" value="<?php echo htmlspecialchars($data['stock']); ?>"
                                       class="form-control <?php echo isset($errors['stock']) ? 'is-invalid' : ''; ?>"
                                       min="0" placeholder="Contoh: 10">
                                <?php if (isset($errors['stock'])): ?><div class="invalid-feedback"><?php echo $errors['stock']; ?></div><?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Dimensi (P×L×T)</label>
                                <input type="text" name="dimensions" value="<?php echo htmlspecialchars($data['dimensions']); ?>"
                                       class="form-control" placeholder="Contoh: 180 x 90 x 75 cm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">URL Gambar</label>
                                <input type="url" name="image_url" value="<?php echo htmlspecialchars($data['image_url']); ?>"
                                       class="form-control" placeholder="https://...">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Upload Foto Produk</label>
                                <input type="file" name="image_file"
                                       class="form-control <?php echo isset($errors['image_file']) ? 'is-invalid' : ''; ?>"
                                       accept=".jpg,.jpeg,.png,.webp">
                                <div class="form-text">JPG/PNG/WEBP, maks 5MB. Jika ada upload, URL diabaikan.</div>
                                <?php if (isset($errors['image_file'])): ?><div class="invalid-feedback"><?php echo $errors['image_file']; ?></div><?php endif; ?>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Deskripsi</label>
                                <textarea name="description" rows="3" class="form-control"
                                          placeholder="Deskripsi singkat produk..."><?php echo htmlspecialchars($data['description']); ?></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex gap-2 justify-content-end">
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-brown">
                            <i class="bi bi-save me-1"></i>Simpan Produk
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<footer class="py-3 mt-5 text-center">
    <small>&copy; 2025 Jatijaya Furniture &mdash; Sistem Manajemen Inventaris</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
