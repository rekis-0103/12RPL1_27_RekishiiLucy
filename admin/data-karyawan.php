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

// Query untuk mendapatkan data karyawan yang diterima bekerja
$query_karyawan = "SELECT 
    a.application_id,
    u.full_name,
    u.email,
    u.no_telepon,
    j.nama_jenjang,
    jr.nama_jurusan,
    l.title as posisi,
    a.start_date,
    a.interview_date
FROM applications a
INNER JOIN users u ON a.user_id = u.user_id
INNER JOIN lowongan l ON a.job_id = l.job_id
LEFT JOIN jenjang_pendidikan j ON a.id_jenjang_pendidikan = j.id_jenjang
LEFT JOIN jurusan_pendidikan jr ON a.id_jurusan_pendidikan = jr.id_jurusan
WHERE a.status = 'diterima bekerja'
ORDER BY a.start_date DESC";

$result_karyawan = mysqli_query($conn, $query_karyawan);
$total_karyawan = mysqli_num_rows($result_karyawan);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Karyawan - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/data-karyawan.css">
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
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Kelola User</a></li>
                <li><a href="logs.php"><i class="fas fa-history"></i> Log Aktivitas</a></li>
                <li><a href="pendidikan.php"><i class="fas fa-graduation-cap"></i> Pendidikan</a></li>
                <li><a href="data-karyawan.php" class="active"><i class="fas fa-address-card"></i> Data Karyawan</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="main-content">

<div class="dashboard-header">
    <h1>Data Karyawan</h1>
    <p>Daftar karyawan yang telah diterima bekerja</p>
</div>

<div class="karyawan-container">
    <div class="karyawan-header">
        <div class="header-info">
            <h2>Total Karyawan: <?php echo $total_karyawan; ?></h2>
        </div>
        <?php if($total_karyawan > 0): ?>
        <div class="header-actions">
            <a href="export_karyawan.php" class="btn-export" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <table class="karyawan-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Pendidikan</th>
                    <th>Jurusan</th>
                    <th>Posisi</th>
                    <th>Tanggal Mulai Kerja</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($total_karyawan > 0):
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result_karyawan)):
                        $pendidikan = $row['nama_jenjang'] ?? '-';
                        $jurusan = $row['nama_jurusan'] ?? '-';
                        $start_date = date('d/m/Y', strtotime($row['start_date']));
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['no_telepon'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($pendidikan); ?></td>
                    <td><?php echo htmlspecialchars($jurusan); ?></td>
                    <td><?php echo htmlspecialchars($row['posisi']); ?></td>
                    <td><?php echo $start_date; ?></td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="9" class="text-center">Belum ada karyawan yang diterima</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
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