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

// Handle apply (only for open jobs)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply') {
    $job_id = (int)$_POST['job_id'];

    // Check if job is still open
    $checkJob = mysqli_query($conn, "SELECT status FROM lowongan WHERE job_id = $job_id");
    $jobData = mysqli_fetch_assoc($checkJob);
    
    if ($jobData['status'] !== 'open') {
        $error = 'Lowongan sudah ditutup, tidak dapat mengirim lamaran.';
        logActivity($conn, $user_id, 'Gagal kirim lamaran (lowongan sudah ditutup)');
    } else {
        // Ensure cv upload dir
        $uploadDir = __DIR__ . '/../uploads/cv/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0775, true);
        }

        if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Unggah CV gagal. Pastikan file dipilih.';
            logActivity($conn, $user_id, 'Gagal kirim lamaran (CV tidak valid)');
        } else {
            $ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
            $safeName = 'cv_' . $user_id . '_' . $job_id . '_' . time() . '.' . strtolower($ext);
            $targetPath = $uploadDir . $safeName;
            $relPath = 'uploads/cv/' . $safeName;
            $allowed = ['pdf', 'doc', 'docx'];
            if (!in_array(strtolower($ext), $allowed)) {
                $error = 'Format CV harus PDF/DOC/DOCX';
                logActivity($conn, $user_id, 'Gagal kirim lamaran (format CV salah)');
            } elseif (move_uploaded_file($_FILES['cv']['tmp_name'], $targetPath)) {
                $q = "INSERT INTO applications (job_id, user_id, cv, status, applied_at) VALUES ($job_id, $user_id, '" . esc($conn, $relPath) . "', 'pendaftaran diterima', NOW())";
                if (mysqli_query($conn, $q)) {
                    $success = 'Lamaran berhasil dikirim';
                    logActivity($conn, $user_id, "Kirim lamaran (job #$job_id)");
                } else {
                    $error = 'Gagal mengirim lamaran';
                    logActivity($conn, $user_id, 'Gagal kirim lamaran (database error)');
                }
            } else {
                $error = 'Gagal menyimpan file CV';
                logActivity($conn, $user_id, 'Gagal kirim lamaran (simpan CV gagal)');
            }
        }
    }
}

// List all jobs (both open and closed) that are not deleted
$list = mysqli_query($conn, "SELECT * FROM lowongan WHERE hapus=0 ORDER BY status ASC, posted_at DESC");
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

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-briefcase"></i> Daftar Lowongan</h3>
                </div>
                <div class="card-body">
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
                                <div class="job-desc"><?php echo nl2br(htmlspecialchars($row['description'])); ?></div>
                                
                                <?php if ($row['status'] === 'open'): ?>
                                    <form method="POST" enctype="multipart/form-data" class="apply-form">
                                        <input type="hidden" name="action" value="apply">
                                        <input type="hidden" name="job_id" value="<?php echo (int)$row['job_id']; ?>">
                                        <div class="form-inline">
                                            <label>CV (PDF/DOC/DOCX): </label>
                                            <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
                                            <button type="submit" class="btn btn-primary btn-sm">Kirim Lamaran</button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div class="apply-form apply-form-disabled">
                                        <div class="form-inline">
                                            <label class="disabled">CV (PDF/DOC/DOCX): </label>
                                            <input type="file" disabled class="disabled">
                                            <button type="button" class="btn btn-disabled btn-sm" disabled>
                                                <i class="fas fa-lock"></i> Lowongan Ditutup
                                            </button>
                                        </div>
                                        <p class="closure-notice">
                                            <i class="fas fa-info-circle"></i> 
                                            Lowongan ini sudah ditutup dan tidak menerima lamaran baru.
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">Belum ada lowongan tersedia.</p>
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