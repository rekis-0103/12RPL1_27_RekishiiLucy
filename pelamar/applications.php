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

$list = mysqli_query($conn, "SELECT a.*, l.title FROM applications a JOIN lowongan l ON a.job_id=l.job_id WHERE a.user_id=$user_id ORDER BY a.applied_at DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamaran Saya - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/applications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Lamaran Saya</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="lowongan.php"><i class="fas fa-briefcase"></i> Lihat Lowongan</a></li>
                <li><a href="applications.php" class="active"><i class="fas fa-file-alt"></i> Lamaran Saya</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Lamaran Saya</h1>
                <p>Detail status lamaran yang telah Anda kirim</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Daftar Lamaran</h3>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Posisi</th>
                                <th>Status</th>
                                <th>Tgl Lamar</th>
                                <th>Alasan HRD</th>
                                <th>Jadwal Interview</th>
                                <th>Tgl Mulai</th>
                                <th>CV</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($list && mysqli_num_rows($list)>0): ?>
                                <?php while ($row = mysqli_fetch_assoc($list)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['applied_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['reason'] ?: '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['interview_date'] ?: '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['start_date'] ?: '-'); ?></td>
                                        <td>
                                            <?php if (!empty($row['cv'])): ?>
                                                <a href="../<?php echo htmlspecialchars($row['cv']); ?>" target="_blank">Lihat</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">Belum ada lamaran</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/navbar.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
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