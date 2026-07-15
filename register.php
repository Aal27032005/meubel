<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); exit;
}

require_once 'config/db.php';

$errors = [];
$data = ['username' => '', 'full_name' => '', 'password' => '', 'password_confirm' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['username']         = trim($_POST['username'] ?? '');
    $data['full_name']        = trim($_POST['full_name'] ?? '');
    $data['password']         = $_POST['password'] ?? '';
    $data['password_confirm'] = $_POST['password_confirm'] ?? '';

    // Validasi
    if (empty($data['full_name'])) {
        $errors['full_name'] = 'Nama lengkap wajib diisi.';
    }

    if (empty($data['username'])) {
        $errors['username'] = 'Username wajib diisi.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $data['username'])) {
        $errors['username'] = 'Username 3–30 karakter, hanya huruf, angka, dan underscore.';
    } else {
        $chk = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $chk->execute([':username' => $data['username']]);
        if ($chk->fetchColumn() > 0) {
            $errors['username'] = 'Username sudah digunakan, pilih yang lain.';
        }
    }

    if (empty($data['password'])) {
        $errors['password'] = 'Password wajib diisi.';
    } elseif (strlen($data['password']) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    if ($data['password'] !== $data['password_confirm']) {
        $errors['password_confirm'] = 'Konfirmasi password tidak cocok.';
    }

    // Simpan jika tidak ada error
    if (empty($errors)) {
        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES (:username, :password, :full_name)")
            ->execute([
                ':username'  => $data['username'],
                ':password'  => $hashed,
                ':full_name' => $data['full_name'],
            ]);
        header("Location: login.php?status=registered&message=Akun+berhasil+dibuat,+silakan+masuk");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun - Jatijaya Furniture</title>
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
            background-color: var(--cream);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .page-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            background:
                linear-gradient(rgba(62,39,35,0.55), rgba(62,39,35,0.55)),
                url('https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=1600&q=80')
                center/cover no-repeat;
        }
        .register-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px 36px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 20px 60px rgba(62,39,35,0.25);
        }
        .brand-title { font-size: 1.4rem; font-weight: 700; color: var(--brown-dark); }
        .btn-brand { background-color: var(--brown-dark); border-color: var(--brown-dark); color: #fff; }
        .btn-brand:hover { background-color: var(--brown); border-color: var(--brown); color: #fff; }
        .btn-outline-brand { border-color: var(--brown-dark); color: var(--brown-dark); }
        .btn-outline-brand:hover { background-color: var(--brown-dark); color: #fff; }
        .form-control:focus {
            border-color: var(--brown-light);
            box-shadow: 0 0 0 .2rem rgba(141,110,99,.25);
        }
        .strength-bar { height: 4px; border-radius: 4px; transition: all .3s; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="register-card">

        <!-- Brand -->
        <div class="text-center mb-4">
            <i class="bi bi-house-heart-fill fs-1" style="color:#3e2723"></i>
            <div class="brand-title mt-2">Jatijaya Furniture</div>
            <p class="text-muted small mt-1">Buat akun baru untuk mengakses sistem</p>
        </div>

        <form method="POST" action="register.php" novalidate>
            <!-- Nama Lengkap -->
            <div class="mb-3">
                <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input type="text" name="full_name"
                           class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>"
                           value="<?php echo htmlspecialchars($data['full_name']); ?>"
                           placeholder="Contoh: Budi Santoso" autofocus>
                    <?php if (isset($errors['full_name'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['full_name']; ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label fw-semibold small">Username <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                    <input type="text" name="username"
                           class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>"
                           value="<?php echo htmlspecialchars($data['username']); ?>"
                           placeholder="Contoh: budi_santoso">
                    <?php if (isset($errors['username'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-text">3–30 karakter, boleh huruf, angka, dan underscore (_).</div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password"
                           class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>"
                           placeholder="Minimal 6 karakter"
                           oninput="checkStrength(this.value)">
                    <button type="button" class="input-group-text" onclick="togglePass('password', this)"
                            style="cursor:pointer">
                        <i class="bi bi-eye"></i>
                    </button>
                    <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- Strength bar -->
                <div class="mt-1 px-1">
                    <div class="bg-light rounded" style="height:4px">
                        <div id="strength-bar" class="strength-bar bg-danger" style="width:0%"></div>
                    </div>
                    <div id="strength-label" class="form-text" style="min-height:1.2em"></div>
                </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-4">
                <label class="form-label fw-semibold small">Konfirmasi Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password_confirm" id="password_confirm"
                           class="form-control <?php echo isset($errors['password_confirm']) ? 'is-invalid' : ''; ?>"
                           placeholder="Ulangi password">
                    <button type="button" class="input-group-text" onclick="togglePass('password_confirm', this)"
                            style="cursor:pointer">
                        <i class="bi bi-eye"></i>
                    </button>
                    <?php if (isset($errors['password_confirm'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['password_confirm']; ?></div>
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

<script>
function togglePass(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function checkStrength(val) {
    const bar   = document.getElementById('strength-bar');
    const label = document.getElementById('strength-label');
    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^a-zA-Z0-9]/.test(val)) score++;

    const levels = [
        { w: '0%',   cls: 'bg-danger',  txt: '' },
        { w: '25%',  cls: 'bg-danger',  txt: 'Lemah' },
        { w: '50%',  cls: 'bg-warning', txt: 'Cukup' },
        { w: '75%',  cls: 'bg-info',    txt: 'Kuat' },
        { w: '90%',  cls: 'bg-success', txt: 'Sangat Kuat' },
        { w: '100%', cls: 'bg-success', txt: 'Sangat Kuat' },
    ];
    const lv = levels[score] || levels[0];
    bar.style.width   = lv.w;
    bar.className     = 'strength-bar ' + lv.cls;
    label.textContent = val.length ? lv.txt : '';
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
