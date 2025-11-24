<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role = $_SESSION['role'];

// Get statistics
$total_users_query = "SELECT COUNT(*) as total FROM users WHERE hapus = 0";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result)['total'];

$active_jobs_query = "SELECT COUNT(*) as total FROM lowongan WHERE status = 'open' AND hapus = 0";
$active_jobs_result = mysqli_query($conn, $active_jobs_query);
$active_jobs = mysqli_fetch_assoc($active_jobs_result)['total'];

$total_applications_query = "SELECT COUNT(*) as total FROM applications";
$total_applications_result = mysqli_query($conn, $total_applications_query);
$total_applications = mysqli_fetch_assoc($total_applications_result)['total'];

// Get recent activity (5 most recent)
$recent_activity_query = "SELECT l.*, u.username, u.full_name 
                         FROM log_aktivitas l 
                         LEFT JOIN users u ON l.user_id = u.user_id 
                         ORDER BY l.log_time DESC 
                         LIMIT 5";
$recent_activity_result = mysqli_query($conn, $recent_activity_query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <!-- Tombol hamburger -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Admin Dashboard</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Kelola User</a></li>
                <li><a href="logs.php"><i class="fas fa-history"></i> Log Aktivitas</a></li>
                <li><a href="pendidikan.php"><i class="fas fa-graduation-cap"></i> Pendidikan</a></li>
                <li><a href="data-karyawan.php"><i class="fas fa-address-card"></i> Data Karyawan</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Dashboard Admin</h1>
                <p>Kelola sistem dan monitor aktivitas pengguna</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total User</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-briefcase"></i>
                    <h3><?php echo $active_jobs; ?></h3>
                    <p>Lowongan Aktif</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-file-alt"></i>
                    <h3><?php echo $total_applications; ?></h3>
                    <p>Lamaran Masuk</p>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Aktivitas Terbaru</h3>
                <?php if (mysqli_num_rows($recent_activity_result) > 0): ?>
                    <?php while ($activity = mysqli_fetch_assoc($recent_activity_result)): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-<?php echo ($activity['action'] == 'Login') ? 'sign-in-alt' : (($activity['action'] == 'Logout') ? 'sign-out-alt' : 'user'); ?>"></i>
                            </div>
                            <div class="activity-content">
                                <h4><?php echo htmlspecialchars($activity['action']); ?></h4>
                                <p><?php echo htmlspecialchars($activity['full_name'] ?: $activity['username']); ?> - <?php echo date('d/m/Y H:i', strtotime($activity['log_time'])); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="activity-item">
                        <div class="activity-content">
                            <p>Tidak ada aktivitas terbaru</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

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