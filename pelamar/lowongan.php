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

// Get user education info
$user_query = mysqli_query($conn, "SELECT id_jenjang_pendidikan, id_jurusan_pendidikan FROM users WHERE user_id = $user_id");
$user_info = mysqli_fetch_assoc($user_query);
$user_jenjang = $user_info['id_jenjang_pendidikan'];
$user_jurusan = $user_info['id_jurusan_pendidikan'];

// Check if user has any ACTIVE application (pending, seleksi administrasi, lolos administrasi, tes & wawancara)
$activeApplicationQuery = mysqli_query($conn, "
    SELECT a.*, l.title 
    FROM applications a 
    JOIN lowongan l ON a.job_id = l.job_id 
    WHERE a.user_id = $user_id 
    AND a.status IN ('pending', 'seleksi administrasi', 'lolos administrasi', 'tes & wawancara')
    ORDER BY a.applied_at DESC 
    LIMIT 1
");
$hasActiveApplication = mysqli_num_rows($activeApplicationQuery) > 0;
$activeApplication = $hasActiveApplication ? mysqli_fetch_assoc($activeApplicationQuery) : null;

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query based on filters and user education
$whereClause = "WHERE l.hapus=0";

// Filter by education requirements
if ($user_jenjang) {
    $whereClause .= " AND (
        l.req_jenjang_pendidikan IS NULL 
        OR (
            l.req_jenjang_pendidikan = $user_jenjang
            AND (
                l.req_jurusan_pendidikan IS NULL 
                OR l.req_jurusan_pendidikan = " . ($user_jurusan ? $user_jurusan : "0") . "
            )
        )
    )";
} else {
    $whereClause .= " AND l.req_jenjang_pendidikan IS NULL";
}

// Add status filter
if ($status_filter === 'open') {
    $whereClause .= " AND l.status='open'";
} elseif ($status_filter === 'closed') {
    $whereClause .= " AND l.status='closed'";
}

// Add search filter
if (!empty($search_query)) {
    $search_escaped = mysqli_real_escape_string($conn, $search_query);
    $whereClause .= " AND l.title LIKE '%$search_escaped%'";
}

// List jobs based on filters
$list = mysqli_query($conn, "SELECT l.*, 
    jenjang.nama_jenjang, 
    jurusan.nama_jurusan
    FROM lowongan l
    LEFT JOIN jenjang_pendidikan jenjang ON l.req_jenjang_pendidikan = jenjang.id_jenjang
    LEFT JOIN jurusan_pendidikan jurusan ON l.req_jurusan_pendidikan = jurusan.id_jurusan
    $whereClause 
    ORDER BY l.status ASC, l.posted_at DESC");
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
    <style>
        .active-application-notice {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .active-application-notice h4 {
            color: #856404;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }
        .active-application-notice h4 i {
            font-size: 24px;
        }
        .active-application-notice p {
            color: #664d03;
            margin: 5px 0;
            line-height: 1.6;
        }
        .active-application-notice strong {
            color: #523d01;
        }
        .application-status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .status-seleksi-administrasi {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .status-lolos-administrasi {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .status-tes-wawancara {
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #c7d2fe;
        }
        .view-application-btn {
            margin-top: 15px;
        }
        .job-item.disabled {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }
        .job-item.disabled::after {
            content: "Anda sudah memiliki lamaran aktif";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            z-index: 10;
        }
    </style>
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
                <p>Daftar lowongan yang sesuai dengan pendidikan Anda</p>
            </div>

            <?php if (!$user_jenjang): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian!</strong> Anda belum melengkapi data pendidikan. 
                    Silakan <a href="profile.php" style="text-decoration: underline; font-weight: bold;">lengkapi profil Anda</a> 
                    untuk melihat lowongan yang sesuai dengan kualifikasi Anda.
                </div>
            <?php endif; ?>

            <?php if ($hasActiveApplication): ?>
                <div class="active-application-notice">
                    <h4>
                        <i class="fas fa-info-circle"></i>
                        Anda Memiliki Lamaran yang Sedang Diproses
                    </h4>
                    <p>
                        <strong>Posisi:</strong> <?php echo htmlspecialchars($activeApplication['title']); ?>
                    </p>
                    <p>
                        <strong>Status Saat Ini:</strong> 
                        <span class="application-status-badge status-<?php echo str_replace(' ', '-', $activeApplication['status']); ?>">
                            <?php echo htmlspecialchars($activeApplication['status']); ?>
                        </span>
                    </p>
                    <p>
                        <strong>Tanggal Melamar:</strong> <?php echo date('d F Y H:i', strtotime($activeApplication['applied_at'])); ?>
                    </p>
                    <p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ffc107;">
                        <i class="fas fa-lock"></i> 
                        <strong>Perhatian:</strong> Anda tidak dapat melamar pekerjaan lain sampai lamaran ini <strong>ditolak</strong> atau <strong>diterima bekerja</strong>.
                    </p>
                    <div class="view-application-btn">
                        <a href="applications.php" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Lihat Detail Lamaran
                        </a>
                    </div>
                </div>
            <?php endif; ?>

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
                                           class="search-input"
                                           <?php echo $hasActiveApplication ? 'disabled' : ''; ?>>
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
                                <select id="status-filter" name="status" onchange="document.getElementById('filterForm').submit()" <?php echo $hasActiveApplication ? 'disabled' : ''; ?>>
                                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Semua Status</option>
                                    <option value="open" <?php echo $status_filter === 'open' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Ditutup</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-search" <?php echo $hasActiveApplication ? 'disabled' : ''; ?>>
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
                            <div class="job-item <?php echo $row['status'] === 'closed' ? 'job-closed' : ''; ?> <?php echo $hasActiveApplication ? 'disabled' : ''; ?>">
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
                                    <?php if ($row['nama_jenjang']): ?>
                                        | Syarat: 
                                        <span class="badge badge-info">
                                            <?php 
                                            echo htmlspecialchars($row['nama_jenjang']);
                                            if ($row['nama_jurusan']) {
                                                echo ' - ' . htmlspecialchars($row['nama_jurusan']);
                                            }
                                            ?>
                                        </span>
                                    <?php endif; ?>
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
                                <?php if (!$user_jenjang): ?>
                                    Silakan lengkapi data pendidikan Anda terlebih dahulu untuk melihat lowongan yang tersedia.
                                <?php elseif (!empty($search_query)): ?>
                                    Tidak ada lowongan yang cocok dengan pencarian "<?php echo htmlspecialchars($search_query); ?>".
                                <?php elseif ($status_filter === 'open'): ?>
                                    Belum ada lowongan aktif yang sesuai dengan pendidikan Anda saat ini.
                                <?php elseif ($status_filter === 'closed'): ?>
                                    Belum ada lowongan yang ditutup.
                                <?php else: ?>
                                    Belum ada lowongan yang sesuai dengan pendidikan Anda saat ini.
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