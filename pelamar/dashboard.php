<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in and has pelamar role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelamar') {
    header('Location: ../login.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role = $_SESSION['role'];

// Stats
$open_jobs = 0; $sent_apps = 0; $waiting = 0; $accepted = 0;
$r1 = mysqli_query($conn, "SELECT COUNT(*) c FROM lowongan WHERE status='open' AND hapus=0");
if ($r1) { $open_jobs = (int)mysqli_fetch_assoc($r1)['c']; }
$r2 = mysqli_query($conn, "SELECT COUNT(*) c FROM applications WHERE user_id=$user_id");
if ($r2) { $sent_apps = (int)mysqli_fetch_assoc($r2)['c']; }
$r3 = mysqli_query($conn, "SELECT COUNT(*) c FROM applications WHERE user_id=$user_id AND status IN ('pendaftaran diterima','seleksi administrasi')");
if ($r3) { $waiting = (int)mysqli_fetch_assoc($r3)['c']; }
$r4 = mysqli_query($conn, "SELECT COUNT(*) c FROM applications WHERE user_id=$user_id AND status='diterima bekerja'");
if ($r4) { $accepted = (int)mysqli_fetch_assoc($r4)['c']; }

// Recent applications (5)
$recent = mysqli_query($conn, "SELECT a.*, l.title FROM applications a JOIN lowongan l ON a.job_id=l.job_id WHERE a.user_id=$user_id ORDER BY a.applied_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelamar - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Dashboard Pelamar</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="lowongan.php"><i class="fas fa-briefcase"></i> Lihat Lowongan</a></li>
                <li><a href="applications.php"><i class="fas fa-file-alt"></i> Lamaran Saya</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Dashboard Pelamar</h1>
                <p>Kelola profil dan lamaran kerja Anda</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-briefcase"></i>
                    <h3><?php echo $open_jobs; ?></h3>
                    <p>Lowongan Tersedia</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-file-alt"></i>
                    <h3><?php echo $sent_apps; ?></h3>
                    <p>Lamaran Terkirim</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <h3><?php echo $waiting; ?></h3>
                    <p>Menunggu Review</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <h3><?php echo $accepted; ?></h3>
                    <p>Diterima</p>
                </div>
            </div>

            <div class="recent-applications">
                <h3>Lamaran Terbaru</h3>
                <?php if ($recent && mysqli_num_rows($recent) > 0): ?>
                    <?php while ($app = mysqli_fetch_assoc($recent)): ?>
                        <div class="application-item">
                            <div class="application-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="application-content">
                                <h4><?php echo htmlspecialchars($app['title']); ?></h4>
                                <p>Status: <?php echo htmlspecialchars(ucfirst($app['status'])); ?></p>
                            </div>
                            <span class="status-badge <?php echo $app['status']==='diterima bekerja'?'status-accepted':($app['status']==='ditolak'?'status-rejected':'status-pending'); ?>">
                                <?php echo date('d/m/Y', strtotime($app['applied_at'])); ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="application-item">
                        <div class="application-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="application-content">
                            <h4>Belum ada lamaran</h4>
                            <p>Anda belum mengirimkan lamaran apapun</p>
                        </div>
                        <span class="status-badge status-pending">-</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../js/navbar.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
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