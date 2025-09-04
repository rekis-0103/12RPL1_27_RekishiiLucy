<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in and has konten role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'konten') {
    header('Location: ../login.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role = $_SESSION['role'];

$kegiatan = 0;
$webinar = 0;
$livestreaming = 0;
$galeri = 0;
$r1 = mysqli_query($conn, "SELECT COUNT(*) c FROM kegiatan");
if ($r1) {
    $kegiatan = (int)mysqli_fetch_assoc($r1)['c'];
}
$r2 = mysqli_query($conn, "SELECT COUNT(*) c FROM webinar");
if ($r2) {
    $webinar = (int)mysqli_fetch_assoc($r2)['c'];
}
$r3 = mysqli_query($conn, "SELECT COUNT(*) c FROM live_streaming");
if ($r3) {
    $livestreaming = (int)mysqli_fetch_assoc($r3)['c'];
}
$r4 = mysqli_query($conn, "SELECT COUNT(*) c FROM galeri");
if ($r4) {
    $galeri = (int)mysqli_fetch_assoc($r4)['c'];
}

//konten terbaru
$konten_terbaru = mysqli_query($conn, "SELECT * FROM `log_aktivitas` WHERE action LIKE '%Konten%' ORDER BY `log_id` DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Konten - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>

    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Dashboard Konten</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="berita.php"><i class="fas fa-newspaper"></i> Kelola Konten</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Dashboard Konten</h1>
                <p>Kelola konten website dan media</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-calendar"></i>
                    <h3><?php echo $kegiatan; ?></h3>
                    <p>kegiatan</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3><?php echo $webinar; ?></h3>
                    <p>Webinar</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-video"></i>
                    <h3><?php echo $livestreaming; ?></h3>
                    <p>Live Streaming</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-image"></i>
                    <h3><?php echo $galeri; ?></h3>
                    <p>Galeri</p>
                </div>
            </div>

            <div class="recent-content">
                <h3>Konten Terbaru</h3>

                <?php if ($konten_terbaru && mysqli_num_rows($konten_terbaru) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($konten_terbaru)): ?>
                        <div class="content-item">
                            <div class="content-icon">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <div class="content-info">
                                <h4><?php echo htmlspecialchars($row['action']); ?></h4>
                                <p><?php echo date('d M Y H:i', strtotime($row['log_time'])); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <div class="content-info">
                            <h4>Belum ada konten</h4>
                            <p>Belum ada konten yang dipublikasikan</p>
                        </div>
                        <span class="status-badge status-draft">-</span>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="../js/navbar.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.mobile-toggle');

            sidebar.classList.toggle('active');

            // Sembunyikan tombol ketika sidebar muncul
            if (sidebar.classList.contains('active')) {
                toggleBtn.style.display = "none";
            } else {
                toggleBtn.style.display = "block";
            }
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-toggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
</body>

</html>