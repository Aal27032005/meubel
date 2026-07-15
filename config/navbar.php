<?php
/**
 * Shared navbar — disertakan di setiap halaman yang butuh navigasi.
 * Mengharapkan $_SESSION['username'] dan $_SESSION['full_name'] sudah terisi.
 */

$_nav_current  = basename($_SERVER['PHP_SELF']);
$_nav_initial  = strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1));
$_nav_fullname = e($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Admin');
$_nav_username = e($_SESSION['username'] ?? '');
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--brown-dark);">
    <div class="container">

        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-house-heart-fill me-2"></i>Jatijaya Furniture
        </a>

        <!-- Toggler mobile -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            <!-- Link utama -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $_nav_current === 'index.php'  ? 'active fw-semibold' : ''; ?>"
                       href="index.php">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $_nav_current === 'create.php' ? 'active fw-semibold' : ''; ?>"
                       href="create.php">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="export.php">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                    </a>
                </li>
            </ul>

            <!-- Dropdown profil -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                       href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <!-- Avatar inisial -->
                        <span class="d-flex align-items-center justify-content-center rounded-circle fw-bold"
                              style="width:32px; height:32px; background:rgba(255,255,255,0.2); font-size:0.85rem;">
                            <?php echo $_nav_initial; ?>
                        </span>
                        <span class="d-none d-lg-inline"><?php echo $_nav_fullname; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <span class="dropdown-item-text">
                                <div class="fw-semibold small"><?php echo $_nav_fullname; ?></div>
                                <div class="text-muted" style="font-size:0.78rem;">@<?php echo $_nav_username; ?></div>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <!-- Logout via form POST agar tidak bisa diakses via link langsung -->
                            <form action="logout.php" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item text-danger"
                                        onclick="return confirm('Yakin ingin keluar?')">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>

        </div>
    </div>
</nav>
