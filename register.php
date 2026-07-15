<?php
/**
 * Halaman Registrasi Akun Baru
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/db.php';
require_once 'config/helpers.php';

$errors = [];
$data   = [
    'full_name'        => '',
    'username'         => '',
    'password'         => '',
    'password_confirm' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $data['full_name']        = trim($_POST['full_name'] ?? '');
    $data['username']         = trim($_POST['username']  ?? '');
    $data['password']         = $_POST['password']         ?? '';
    $data['password_confirm'] = $_POST['password_confirm'] ?? '';

    // ── Validasi ──────────────────────────────────────────────────────────────

    if ($data['full_name'] === '') {
        $errors['full_name'] = 'Nama lengkap wajib diisi.';
    } elseif (strlen($data['full_name']) > 100) {
        $errors['full_name'] = 'Nama lengkap maksimal 100 karakter.';
    }

    if ($data['username'] === '') {
        $errors['username'] = 'Username wajib diisi.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $data['username'])) {
        $errors['username'] = 'Username 3–30 karakter. Boleh huruf, angka, dan underscore (_).';
    } else {
        // Cek duplikat username
        $chk = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
        $chk->execute([':username' => $data['username']]);
        if ((int) $chk->fetchColumn() > 0) {
            $errors['username'] = 'Username sudah digunakan. Pilih username lain.';
        }
    }

    if ($data['password'] === '') {
        $errors['password'] = 'Password wajib diisi.';
    } elseif (strlen($data['password']) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    if ($data['password'] !== $data['password_confirm']) {
        $errors['password_confirm'] = 'Konfirmasi password tidak cocok.';
    }

    // ── Simpan ke database ────────────────────────────────────────────────────

    if (empty($errors)) {
        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);

        $pdo->prepare('INSERT INTO users (username, password, full_name) VALUES (:username, :password, :full_name)')
            ->execute([
                ':username'  => $data['username'],
                ':password'  => $hashed,
                ':full_name' => $data['full_name'],
            ]);

        header('Location: login.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun — Jatijaya Furniture</title>
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

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--cream);
        }

        .auth-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            background:
                linear-gradient(rgba(62, 39, 35, 0.55), rgba(62, 39, 35, 0.55)),
                url('https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=1600&q=80')
                center / cover no-repeat;
        }

        .auth-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px 36px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 20px 60px rgba(62, 39, 35, 0.25);
        }

        .auth-card .brand-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--brown-dark);
        }

        .btn-brand {
            background-color: var(--brown-dark);
            border-color: var(--brown-dark);
            color: #fff;
        }
        .btn-brand:hover {
            background-color: var(--brown);
            border-color: var(--brown);
            color: #fff;
        }

        .btn-outline-brand {
            border-color: var(--brown-dark);
            color: var(--brown-dark);
            background: transparent;
        }
        .btn-outline-brand:hover {
            background-color: var(--brown-dark);
            color: #fff;
        }

        .form-control:focus {
            border-color: var(--brown-light);
            box-shadow: 0 0 0 0.2rem rgba(141, 110, 99, 0.25);
        }

        /* Strength bar */
        .strength-bar {
            height: 4px;
            border-radius: 4px;
            transition: width 0.3s, background-color 0.3s;
        }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <!-- Brand -->
        <div class="text-center mb-4">
            <i class="bi bi-house-heart-fill fs-1" style="color: var(--brown-dark);"></i>
            <div class="brand-title mt-2">Jatijaya Furniture</div>
            <p class="text-muted small mt-1 mb-0">Buat akun baru untuk mengakses sistem</p>
        </div>

        <!-- Form registrasi -->
        <form method="POST" action="register.php" novalidate>
            <?php echo csrf_field(); ?>

            <!-- Nama Lengkap -->
            <div class="mb-3">
                <label for="full_name" class="form-label fw-semibold small">
                    Nama Lengkap <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input type="text" id="full_name" name="full_name"
                           class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>"
                           value="<?php echo e($data['full_name']); ?>"
                           placeholder="Contoh: Budi Santoso"
                           autocomplete="name"
                           autofocus>
                    <?php if (isset($errors['full_name'])): ?>
                    <div class="invalid-feedback"><?php echo e($errors['full_name']); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold small">
                    Username <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                    <input type="text" id="username" name="username"
                           class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>"
                           value="<?php echo e($data['username']); ?>"
                           placeholder="Contoh: budi_santoso"
                           autocomplete="username">
                    <?php if (isset($errors['username'])): ?>
                    <div class="invalid-feedback"><?php echo e($errors['username']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-text">3–30 karakter. Huruf, angka, dan underscore (_).</div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold small">
                    Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" id="password" name="password"
                           class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>"
                           placeholder="Minimal 6 karakter"
                           autocomplete="new-password"
                           oninput="updateStrength(this.value)">
                    <button type="button" class="input-group-text"
                            onclick="togglePassword('password', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                    <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?php echo e($errors['password']); ?></div>
                    <?php endif; ?>
                </div>
                <!-- Indikator kekuatan password -->
                <div class="mt-1 px-1">
                    <div class="bg-light rounded overflow-hidden" style="height: 4px;">
                        <div id="strengthBar" class="strength-bar" style="width: 0%;"></div>
                    </div>
                    <div id="strengthLabel" class="form-text" style="min-height: 1.2em;"></div>
                </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-4">
                <label for="password_confirm" class="form-label fw-semibold small">
                    Konfirmasi Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" id="password_confirm" name="password_confirm"
                           class="form-control <?php echo isset($errors['password_confirm']) ? 'is-invalid' : ''; ?>"
                           placeholder="Ulangi password"
                           autocomplete="new-password">
                    <button type="button" class="input-group-text"
                            onclick="togglePassword('password_confirm', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                    <?php if (isset($errors['password_confirm'])): ?>
                    <div class="invalid-feedback"><?php echo e($errors['password_confirm']); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2 fw-semibold mb-3">
                <i class="bi bi-person-plus me-2"></i>Buat Akun
            </button>

            <a href="login.php" class="btn btn-outline-brand w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sudah punya akun? Masuk
            </a>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/**
 * Toggle visibilitas password.
 */
function togglePassword(inputId, btn) {
    const input    = document.getElementById(inputId);
    const icon     = btn.querySelector('i');
    const isHidden = input.type === 'password';

    input.type     = isHidden ? 'text' : 'password';
    icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
}

/**
 * Perbarui indikator kekuatan password.
 * @param {string} value - Nilai input password
 */
function updateStrength(value) {
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');

    let score = 0;
    if (value.length >= 6)               score++;
    if (value.length >= 10)              score++;
    if (/[A-Z]/.test(value))             score++;
    if (/[0-9]/.test(value))             score++;
    if (/[^a-zA-Z0-9]/.test(value))      score++;

    const levels = [
        { width: '0%',   color: '',           label: '' },
        { width: '25%',  color: 'bg-danger',  label: 'Lemah' },
        { width: '50%',  color: 'bg-warning', label: 'Cukup' },
        { width: '75%',  color: 'bg-info',    label: 'Kuat' },
        { width: '90%',  color: 'bg-success', label: 'Sangat Kuat' },
        { width: '100%', color: 'bg-success', label: 'Sangat Kuat' },
    ];

    const level = levels[score] ?? levels[0];

    bar.style.width = level.width;
    bar.className   = 'strength-bar ' + level.color;
    label.textContent = value.length > 0 ? level.label : '';
}
</script>
</body>
</html>
