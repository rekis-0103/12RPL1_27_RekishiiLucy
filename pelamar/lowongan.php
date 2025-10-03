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

function esc($conn, $s)
{
    return mysqli_real_escape_string($conn, $s);
}
function logActivity($conn, $actor_user_id, $action)
{
    $actor_user_id = (int)$actor_user_id;
    $action = mysqli_real_escape_string($conn, $action);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '::1';
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($actor_user_id, '$action')");
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query based on filters
$whereClause = "WHERE hapus=0";

// Add status filter
if ($status_filter === 'open') {
    $whereClause .= " AND status='open'";
} elseif ($status_filter === 'closed') {
    $whereClause .= " AND status='closed'";
}

// Add search filter
if (!empty($search_query)) {
    $search_escaped = mysqli_real_escape_string($conn, $search_query);
    $whereClause .= " AND title LIKE '%$search_escaped%'";
}

// List jobs based on filters
$list = mysqli_query($conn, "SELECT * FROM lowongan $whereClause ORDER BY status ASC, posted_at DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lowongan - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/lowongan.css">
</head>

<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Lowongan</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="lowongan.php" class="active"><i class="fas fa-briefcase"></i> Lihat Lowongan</a></li>
                <li><a href="applications.php"><i class="fas fa-file-alt"></i> Lamaran Saya</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Lowongan</h1>
                <p>Daftar lowongan yang tersedia</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-briefcase"></i> Daftar Lowongan</h3>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form method="GET" action="" id="filterForm" class="filter-form">
                            <!-- Search Bar -->
                            <div class="filter-group search-group">
                                <label for="search-input">
                                    <i class="fas fa-search"></i> Cari Pekerjaan:
                                </label>
                                <div class="search-input-wrapper">
                                    <input type="text" 
                                           id="search-input" 
                                           name="search" 
                                           placeholder="Masukkan nama pekerjaan..." 
                                           value="<?php echo htmlspecialchars($search_query); ?>"
                                           class="search-input">
                                    <?php if (!empty($search_query)): ?>
                                        <button type="button" class="clear-search" onclick="clearSearch()" title="Hapus pencarian">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div class="filter-group">
                                <label for="status-filter">
                                    <i class="fas fa-filter"></i> Filter Status:
                                </label>
                                <select id="status-filter" name="status" onchange="document.getElementById('filterForm').submit()">
                                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Semua Status</option>
                                    <option value="open" <?php echo $status_filter === 'open' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Ditutup</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-search">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </form>

                        <?php if (!empty($search_query)): ?>
                            <div class="search-info">
                                <i class="fas fa-info-circle"></i>
                                Menampilkan hasil pencarian untuk: <strong>"<?php echo htmlspecialchars($search_query); ?>"</strong>
                                <?php 
                                $total_results = $list ? mysqli_num_rows($list) : 0;
                                echo " ($total_results hasil ditemukan)";
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($list && mysqli_num_rows($list) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($list)): ?>
                            <div class="job-item <?php echo $row['status'] === 'closed' ? 'job-closed' : ''; ?>">
                                <div class="job-header">
                                    <div class="job-title"><?php echo htmlspecialchars($row['title']); ?></div>
                                    <div class="job-status">
                                        <?php if ($row['status'] === 'open'): ?>
                                            <span class="status-badge status-open">
                                                <i class="fas fa-check-circle"></i> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-closed">
                                                <i class="fas fa-times-circle"></i> Ditutup
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="job-meta">
                                    Lokasi: <?php echo htmlspecialchars($row['location'] ?: '-'); ?> | 
                                    Gaji: <?php echo htmlspecialchars($row['salary_range'] ?: '-'); ?> |
                                    Diposting: <?php echo date('d M Y', strtotime($row['posted_at'])); ?>
                                </div>
                                <div class="job-desc-preview">
                                    <?php 
                                    $description = strip_tags($row['description']);
                                    echo htmlspecialchars(strlen($description) > 200 ? substr($description, 0, 200) . '...' : $description); 
                                    ?>
                                </div>
                                
                                <div class="job-actions">
                                    <a href="detail-lowongan.php?id=<?php echo $row['job_id']; ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-jobs">
                            <i class="fas fa-briefcase"></i>
                            <p>
                                <?php if (!empty($search_query)): ?>
                                    Tidak ada lowongan yang cocok dengan pencarian "<?php echo htmlspecialchars($search_query); ?>".
                                <?php elseif ($status_filter === 'open'): ?>
                                    Belum ada lowongan aktif tersedia.
                                <?php elseif ($status_filter === 'closed'): ?>
                                    Belum ada lowongan yang ditutup.
                                <?php else: ?>
                                    Belum ada lowongan tersedia.
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($search_query) || $status_filter !== 'all'): ?>
                                <a href="lowongan.php" class="btn btn-primary btn-sm" style="margin-top: 15px;">
                                    <i class="fas fa-redo"></i> Tampilkan Semua Lowongan
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/navbar.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        
        function clearSearch() {
            document.getElementById('search-input').value = '';
            document.getElementById('filterForm').submit();
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