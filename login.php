<?php
/**
 * Halaman Login
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

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, full_name, password FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Jatijaya Furniture</title>
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

        /* Wrapper full-screen dengan background hero */
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

        /* Kartu form */
        .auth-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(62, 39, 35, 0.25);
        }

        .auth-card .brand-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brown-dark);
            letter-spacing: 0.5px;
        }

        /* Tombol utama */
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

        /* Tombol outline */
        .btn-outline-brand {
            border-color: var(--brown-dark);
            color: var(--brown-dark);
            background: transparent;
        }
        .btn-outline-brand:hover {
            background-color: var(--brown-dark);
            color: #fff;
        }

        /* Focus ring */
        .form-control:focus {
            border-color: var(--brown-light);
            box-shadow: 0 0 0 0.2rem rgba(141, 110, 99, 0.25);
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
            <p class="text-muted small mt-1 mb-0">Sistem Manajemen Inventaris</p>
        </div>

        <!-- Flash: akun baru berhasil dibuat -->
        <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success py-2 small">
            <i class="bi bi-check-circle me-1"></i>Akun berhasil dibuat. Silakan masuk.
        </div>
        <?php endif; ?>

        <!-- Error login -->
        <?php if ($error !== ''): ?>
        <div class="alert alert-danger py-2 small">
            <i class="bi bi-exclamation-triangle me-1"></i><?php echo e($error); ?>
        </div>
        <?php endif; ?>

        <!-- Form login -->
        <form method="POST" action="login.php" novalidate>
            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label for="username" class="form-label fw-semibold small">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" id="username" name="username"
                           class="form-control"
                           value="<?php echo e($username); ?>"
                           placeholder="Masukkan username"
                           autocomplete="username"
                           autofocus required>
                </div>
            </div>

            <div class="mb-4">
                <label for="loginPassword" class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" id="loginPassword" name="password"
                           class="form-control"
                           placeholder="Masukkan password"
                           autocomplete="current-password"
                           required>
                    <button type="button" class="input-group-text"
                            onclick="togglePassword('loginPassword', this)"
                            title="Tampilkan / sembunyikan password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2 fw-semibold mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>

            <a href="register.php" class="btn btn-outline-brand w-100 py-2 fw-semibold">
                <i class="bi bi-person-plus me-2"></i>Belum punya akun? Daftar
            </a>
        </form>

        <p class="text-center text-muted mt-4 mb-0" style="font-size: 0.75rem;">
            <i class="bi bi-info-circle me-1"></i>
            Default: <strong>admin</strong> / <strong>admin123</strong>
        </p>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/**
 * Toggle visibilitas password pada input yang diberikan.
 * @param {string} inputId  - ID elemen <input>
 * @param {HTMLElement} btn - Tombol yang diklik (berisi <i>)
 */
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    const isHidden = input.type === 'password';

    input.type      = isHidden ? 'text' : 'password';
    icon.className  = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
}
</script>
</body>
</html>
