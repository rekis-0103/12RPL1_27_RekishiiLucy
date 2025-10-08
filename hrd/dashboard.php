<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in and has hrd role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hrd') {
    header('Location: ../login.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role = $_SESSION['role'];

// Stats
$total_active_jobs = 0;
$total_new_apps = 0; // pendaftaran diterima
$total_under_review = 0; // seleksi administrasi
$total_candidates = 0; // lolos administrasi + tes & wawancara

$qs1 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM lowongan WHERE status='open' AND hapus=0");
if ($qs1) { $total_active_jobs = (int)mysqli_fetch_assoc($qs1)['c']; }

$qs2 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM applications WHERE status='pending'");
if ($qs2) { $total_new_apps = (int)mysqli_fetch_assoc($qs2)['c']; }

$qs3 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM applications WHERE status='seleksi administrasi'");
if ($qs3) { $total_under_review = (int)mysqli_fetch_assoc($qs3)['c']; }

$qs4 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM applications WHERE status IN ('lolos administrasi','tes & wawancara')");
if ($qs4) { $total_candidates = (int)mysqli_fetch_assoc($qs4)['c']; }

// Recent applications (5 latest)
$recent_apps = mysqli_query($conn, "SELECT a.*, u.full_name, u.email, l.title 
                                    FROM applications a
                                    JOIN users u ON a.user_id = u.user_id
                                    JOIN lowongan l ON a.job_id = l.job_id
                                    ORDER BY a.applied_at DESC
                                    LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard HRD - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Dashboard HRD</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="lowongan.php"><i class="fas fa-briefcase"></i> Kelola Lowongan</a></li>
                <li><a href="applications.php"><i class="fas fa-file-alt"></i> Kelola Lamaran</a></li>
                <li><a href="candidates.php"><i class="fas fa-users"></i> Kandidat</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Dashboard HRD</h1>
                <p>Kelola lowongan dan lamaran kerja</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-briefcase"></i>
                    <h3><?php echo $total_active_jobs; ?></h3>
                    <p>Lowongan Aktif</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-file-alt"></i>
                    <h3><?php echo $total_new_apps; ?></h3>
                    <p>Lamaran Masuk</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <h3><?php echo $total_under_review; ?></h3>
                    <p>Seleksi Administrasi</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $total_candidates; ?></h3>
                    <p>Kandidat</p>
                </div>
            </div>
            
            <div class="recent-applications">
                <h3>Lamaran Terbaru</h3>
                <?php if ($recent_apps && mysqli_num_rows($recent_apps) > 0): ?>
                    <?php while ($app = mysqli_fetch_assoc($recent_apps)): ?>
                        <div class="application-item">
                            <div class="application-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="application-content">
                                <h4><?php echo htmlspecialchars($app['full_name']); ?> - <?php echo htmlspecialchars($app['title']); ?></h4>
                                <p>Status: <span class="status-badge"><?php echo htmlspecialchars($app['status']); ?></span></p>
                            </div>
                            <span class="status-badge">
                                <?php echo date('d/m/Y H:i', strtotime($app['applied_at'])); ?>
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
                            <p>Belum ada lamaran yang masuk</p>
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
            const toggleBtn = document.querySelector('.mobile-toggle');

            sidebar.classList.toggle('active');

            // Sembunyikan tombol ketika sidebar muncul
            if (sidebar.classList.contains('active')) {
                toggleBtn.style.display = "none";
            } else {
                toggleBtn.style.display = "block";
            }
        }

        // Tutup sidebar kalau klik di luar
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-toggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                    mobileToggle.style.display = "block"; // tampilkan kembali tombol
                }
            }
        });
    </script>
</body>

</html>
