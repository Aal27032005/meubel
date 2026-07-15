<?php
/**
 * Fungsi-fungsi utilitas yang dipakai di seluruh aplikasi.
 */

// ── CSRF ──────────────────────────────────────────────────────────────────────

/**
 * Buat atau ambil CSRF token dari session.
 */
function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render hidden input CSRF untuk form.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Validasi CSRF token dari POST request. Redirect jika gagal.
 */
function csrf_verify(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        die('Permintaan tidak valid (CSRF token mismatch).');
    }
}

// ── Output ────────────────────────────────────────────────────────────────────

/**
 * Escape string untuk output HTML yang aman.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Format angka ke format rupiah. Contoh: 3750000 → Rp 3.750.000
 */
function rupiah(float $amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// ── Upload Gambar ─────────────────────────────────────────────────────────────

/**
 * Proses upload file gambar. Mengembalikan path relatif atau null jika gagal/tidak ada file.
 *
 * @param  array  $file    Elemen dari $_FILES
 * @param  array  &$errors Array error (diisi jika ada masalah)
 * @param  string $key     Nama kunci pada $errors
 * @return string|null     Path relatif file yang tersimpan, atau null
 */
function handle_image_upload(array $file, array &$errors, string $key = 'image_file'): ?string
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[$key] = 'Terjadi kesalahan saat upload file (kode: ' . $file['error'] . ').';
        return null;
    }

    $allowed_ext  = ['jpg', 'jpeg', 'png', 'webp'];
    $max_size     = 5 * 1024 * 1024; // 5 MB
    $ext          = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext, true)) {
        $errors[$key] = 'Format tidak diizinkan. Gunakan JPG, PNG, atau WEBP.';
        return null;
    }

    if ($file['size'] > $max_size) {
        $errors[$key] = 'Ukuran file maksimal 5 MB.';
        return null;
    }

    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $filename    = 'meubel_' . uniqid('', true) . '.' . $ext;
    $destination = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errors[$key] = 'Gagal menyimpan file ke server.';
        return null;
    }

    return 'uploads/' . $filename;
}

/**
 * Hapus file gambar dari disk jika ada.
 */
function delete_image(?string $path): void
{
    if ($path && file_exists(__DIR__ . '/../' . $path)) {
        @unlink(__DIR__ . '/../' . $path);
    }
}

// ── Redirect ──────────────────────────────────────────────────────────────────

/**
 * Redirect dengan flash message melalui query string.
 */
function redirect(string $url, string $status = 'success', string $message = ''): void
{
    $query = http_build_query(['status' => $status, 'message' => $message]);
    header('Location: ' . $url . ($query ? '?' . $query : ''));
    exit;
}
