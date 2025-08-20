<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="index.php"><img src="assets/logo.png" alt="PT Waindo Specterra"></a>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>Beranda</a></li>
            <li><a href="tentang-kami.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'tentang-kami.php') ? 'class="active"' : ''; ?>>Tentang Kami</a></li>
            <li><a href="produk.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'produk.php') ? 'class="active"' : ''; ?>>Produk</a></li>
            <li><a href="layanan.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'layanan.php') ? 'class="active"' : ''; ?>>Layanan</a></li>
            <li><a href="mitra-kerja.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'mitra-kerja.php') ? 'class="active"' : ''; ?>>Mitra Kerja</a></li>
            <li><a href="berita.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'berita.php') ? 'class="active"' : ''; ?>>Berita</a></li>
            <li><a href="hubungi-kami.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'hubungi-kami.php') ? 'class="active"' : ''; ?>>Hubungi Kami</a></li>
            <li><a href="bergabung.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'bergabung.php') ? 'class="active"' : ''; ?>>Bergabung</a></li>
            <li><a href="https://docs.google.com/forms/d/e/1FAIpQLSfvFEpx0w4Ypo9YcRmKH5IX3SI7kgk8DJGbKjUUuOAoC17IZQ/viewform">Download</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="user-dropdown">
                    <button type="button" class="user-dropdown-btn">
                        <i class="fas fa-user"></i>
                        <?php echo htmlspecialchars($_SESSION['full_name'] ?: $_SESSION['username']); ?>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown-content">
                        <a href="<?php
                                    switch ($_SESSION['role']) {
                                        case 'admin':
                                            echo 'admin/dashboard.php';
                                            break;
                                        case 'pelamar':
                                            echo 'pelamar/dashboard.php';
                                            break;
                                        case 'hrd':
                                            echo 'hrd/dashboard.php';
                                            break;
                                        case 'konten':
                                            echo 'konten/dashboard.php';
                                            break;
                                        default:
                                            echo 'index.php';
                                    }
                                    ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="login.php" class="login-btn <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>">Login</a></li>
            <?php endif; ?>
        </ul>
        <div class="nav-right">
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
</nav>

<script src="js/navbar.js"></script>