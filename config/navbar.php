<?php
// Pastikan session sudah aktif
if (session_status() === PHP_SESSION_NONE) session_start();
$_current = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#3e2723;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php" style="letter-spacing:0.5px;">
            <i class="bi bi-house-heart-fill me-2"></i>Jatijaya Furniture
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $_current === 'index.php' ? 'active fw-semibold' : ''; ?>" href="index.php">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $_current === 'create.php' ? 'active fw-semibold' : ''; ?>" href="create.php">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="export.php">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                    </a>
                </li>
            </ul>
            <!-- Profil dropdown -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="rounded-circle d-flex align-items-center justify-content-center"
                              style="width:32px;height:32px;background:rgba(255,255,255,0.2);font-size:0.85rem;font-weight:700;">
                            <?php echo strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)); ?>
                        </span>
                        <span class="d-none d-lg-inline"><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Admin'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <span class="dropdown-item-text">
                                <div class="fw-semibold"><?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?></div>
                                <div class="text-muted small">@<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></div>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php"
                               onclick="return confirm('Yakin ingin keluar?')">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
