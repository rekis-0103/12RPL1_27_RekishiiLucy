<?php
session_start();
require_once 'connect/koneksi.php';

function esc($conn, $s)
{
    return mysqli_real_escape_string($conn, $s);
}

function logActivity($conn, $userId, $action)
{
    $uid = $userId ? (int)$userId : 'NULL';
    $actionEsc = mysqli_real_escape_string($conn, $action);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '::1';
    $ipEsc = mysqli_real_escape_string($conn, $ip);
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($uid, '$actionEsc')");
}

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? (int)$_SESSION['user_id'] : null;
$userRole = $isLoggedIn ? $_SESSION['role'] : null;

// Get job ID from URL
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$show_apply_form = isset($_GET['apply']) && $_GET['apply'] == 1;

if (!$job_id) {
    header('Location: bergabung.php');
    exit();
}

// Get job details
$query = "SELECT * FROM lowongan WHERE job_id = $job_id AND hapus = 0";
$result = mysqli_query($conn, $query);
$job = mysqli_fetch_assoc($result);

if (!$job) {
    header('Location: bergabung.php');
    exit();
}

// Check if user has already applied for this job (only if logged in as pelamar)
$hasApplied = false;
if ($isLoggedIn && $userRole === 'pelamar') {
    $checkApplication = mysqli_query($conn, "SELECT * FROM applications WHERE job_id = $job_id AND user_id = $userId");
    $hasApplied = mysqli_num_rows($checkApplication) > 0;
}

// Handle apply action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply') {
    if (!$isLoggedIn || $userRole !== 'pelamar') {
        $error = 'Silakan login sebagai pelamar untuk mengirim lamaran.';
        logActivity($conn, $userId, 'Gagal kirim lamaran (belum login atau bukan pelamar)');
    } elseif ($job['status'] !== 'open') {
        $error = 'Lowongan sudah ditutup, tidak dapat mengirim lamaran.';
        logActivity($conn, $userId, 'Gagal kirim lamaran (lowongan sudah ditutup)');
    } elseif ($hasApplied) {
        $error = 'Anda sudah mengirim lamaran untuk lowongan ini sebelumnya.';
        logActivity($conn, $userId, 'Gagal kirim lamaran (sudah apply)');
    } else {
        // Ensure cv upload dir
        $uploadDir = __DIR__ . '/uploads/cv/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0775, true);
        }

        if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Unggah CV gagal. Pastikan file dipilih.';
            logActivity($conn, $userId, 'Gagal kirim lamaran (CV tidak valid)');
        } else {
            $ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
            $safeName = 'cv_' . $userId . '_' . $job_id . '_' . time() . '.' . strtolower($ext);
            $targetPath = $uploadDir . $safeName;
            $relPath = 'uploads/cv/' . $safeName;
            $allowed = ['pdf', 'doc', 'docx'];
            
            if (!in_array(strtolower($ext), $allowed)) {
                $error = 'Format CV harus PDF/DOC/DOCX';
                logActivity($conn, $userId, 'Gagal kirim lamaran (format CV salah)');
            } elseif (move_uploaded_file($_FILES['cv']['tmp_name'], $targetPath)) {
                $q = "INSERT INTO applications (job_id, user_id, cv, status, applied_at) VALUES ($job_id, $userId, '" . esc($conn, $relPath) . "', 'pendaftaran diterima', NOW())";
                if (mysqli_query($conn, $q)) {
                    $success = 'Lamaran berhasil dikirim';
                    logActivity($conn, $userId, "Kirim lamaran (job #$job_id)");
                    $hasApplied = true;
                    // Refresh job data
                    $result = mysqli_query($conn, $query);
                    $job = mysqli_fetch_assoc($result);
                } else {
                    $error = 'Gagal mengirim lamaran';
                    logActivity($conn, $userId, 'Gagal kirim lamaran (database error)');
                }
            } else {
                $error = 'Gagal menyimpan file CV';
                logActivity($conn, $userId, 'Gagal kirim lamaran (simpan CV gagal)');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lowongan - <?php echo htmlspecialchars($job['title']); ?> | PT Waindo Specterra</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/pages.css">
    <link rel="stylesheet" href="assets/css/bergabung.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="page-container">
        <main class="page-content">
            <div class="container">
                <div class="page-header">
                    <div class="header-with-back">
                        <a href="bergabung.php" class="back-button">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Lowongan
                        </a>
                        <h1><?php echo htmlspecialchars($job['title']); ?></h1>
                        <p>Detail lengkap lowongan pekerjaan</p>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="job-detail-container">
                    <div class="job-detail-card">
                        <div class="job-detail-header">
                            <div class="job-title-section">
                                <h2><?php echo htmlspecialchars($job['title']); ?></h2>
                                <div class="job-status-large">
                                    <?php if ($job['status'] === 'open'): ?>
                                        <span class="status-badge status-open">
                                            <i class="fas fa-check-circle"></i> Lowongan Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge status-closed">
                                            <i class="fas fa-times-circle"></i> Lowongan Ditutup
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="job-detail-info">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-map-marker-alt"></i> Lokasi
                                    </div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($job['location'] ?: 'Tidak disebutkan'); ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-money-bill-wave"></i> Gaji
                                    </div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($job['salary_range'] ?: 'Nego'); ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-alt"></i> Tanggal Posting
                                    </div>
                                    <div class="info-value">
                                        <?php echo date('d F Y', strtotime($job['posted_at'])); ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-clock"></i> Terakhir Update
                                    </div>
                                    <div class="info-value">
                                        <?php echo date('d F Y H:i', strtotime($job['updated_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="job-detail-section">
                            <h3><i class="fas fa-file-alt"></i> Deskripsi Pekerjaan</h3>
                            <div class="job-description">
                                <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                            </div>
                        </div>

                        <?php if (!empty($job['requirements'])): ?>
                        <div class="job-detail-section">
                            <h3><i class="fas fa-list-check"></i> Persyaratan</h3>
                            <div class="job-requirements">
                                <?php echo nl2br(htmlspecialchars($job['requirements'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Application Section -->
                        <div class="application-section">
                            <?php if (!$isLoggedIn): ?>
                                <!-- Not logged in -->
                                <div class="login-required-section">
                                    <div class="login-notice">
                                        <i class="fas fa-sign-in-alt"></i>
                                        <div class="login-text">
                                            <h4>Login Diperlukan</h4>
                                            <p>Silakan login terlebih dahulu untuk dapat mengirim lamaran.</p>
                                        </div>
                                    </div>
                                    <div class="login-actions">
                                        <a href="login.php" class="btn btn-primary">
                                            <i class="fas fa-sign-in-alt"></i> Login Sekarang
                                        </a>
                                        <a href="register.php" class="btn btn-secondary">
                                            <i class="fas fa-user-plus"></i> Daftar Akun
                                        </a>
                                    </div>
                                </div>
                            <?php elseif ($userRole !== 'pelamar'): ?>
                                <!-- Not pelamar role -->
                                <div class="role-restricted-section">
                                    <div class="role-notice">
                                        <i class="fas fa-user-slash"></i>
                                        <div class="role-text">
                                            <h4>Akses Terbatas</h4>
                                            <p>Hanya pengguna dengan role "pelamar" yang dapat mengirim lamaran.</p>
                                            <p>Role Anda saat ini: <strong><?php echo htmlspecialchars($userRole); ?></strong></p>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($hasApplied): ?>
                                <!-- Already applied -->
                                <div class="already-applied">
                                    <div class="applied-notice">
                                        <i class="fas fa-check-circle"></i>
                                        <div class="applied-text">
                                            <h4>Lamaran Sudah Dikirim</h4>
                                            <p>Anda sudah mengirim lamaran untuk lowongan ini. Silakan cek status lamaran di dashboard Anda.</p>
                                        </div>
                                    </div>
                                    <?php if ($isLoggedIn && $userRole === 'pelamar'): ?>
                                    <div class="applied-actions">
                                        <a href="pelamar/applications.php" class="btn btn-info">
                                            <i class="fas fa-file-alt"></i> Lihat Lamaran Saya
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($job['status'] === 'open'): ?>
                                <!-- Can apply -->
                                <div class="apply-section <?php echo $show_apply_form ? 'show-form' : ''; ?>">
                                    <div class="apply-header">
                                        <h3><i class="fas fa-paper-plane"></i> Kirim Lamaran</h3>
                                        <?php if (!$show_apply_form): ?>
                                            <button type="button" class="btn btn-primary" onclick="showApplyForm()">
                                                <i class="fas fa-plus"></i> Lamar Sekarang
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="apply-form-container" id="applyFormContainer" <?php echo !$show_apply_form ? 'style="display:none;"' : ''; ?>>
                                        <form method="POST" enctype="multipart/form-data" class="apply-form-detail">
                                            <input type="hidden" name="action" value="apply">
                                            <div class="form-group">
                                                <label for="cv"><i class="fas fa-file-pdf"></i> Upload CV (PDF/DOC/DOCX)</label>
                                                <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" required>
                                                <small class="form-text">Maksimal ukuran file 5MB. Format yang diterima: PDF, DOC, DOCX</small>
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-paper-plane"></i> Kirim Lamaran
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="hideApplyForm()">
                                                    <i class="fas fa-times"></i> Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Job closed -->
                                <div class="job-closed-section">
                                    <div class="closed-notice">
                                        <i class="fas fa-lock"></i>
                                        <div class="closed-text">
                                            <h4>Lowongan Ditutup</h4>
                                            <p>Lowongan ini sudah ditutup dan tidak menerima lamaran baru.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>PT Waindo Specterra</h3>
                    <p>Total Solution for Digital Information</p>
                </div>
                <div class="footer-section">
                    <ul>
                        <h4>Kontak</h4>
                        <p>Alamat : Kompleks Perkantoran Pejaten Raya #7-8 Jl. Pejaten Raya No.2 Jakarta Selatan 12510</p>
                        <p>Telepon : 021 7986816; 7986405</p>
                        <p>Fax : 021 7995539</p>
                        <p>Email : marketing@waindo.co.id</p>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Social</h4>
                    <p><a href="https://www.instagram.com/waindo_specterra?igshid=fysfd3j6l41n"><i class="fa-brands fa-instagram"></i> @waindo_specterra</a></p>
                    <p><a href="https://x.com/WSpecterra?s=08"><i class="fa-brands fa-twitter"></i> @WSpecterra</a></p>
                    <p><a href="https://www.instagram.com/waindo_specterra?igshid=fysfd3j6l41n"><i class="fa-brands fa-facebook"></i> @waindo_specterra</a></p>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/common.js"></script>
    <script>
        function showApplyForm() {
            document.getElementById('applyFormContainer').style.display = 'block';
            document.querySelector('.apply-section').classList.add('show-form');
        }
        
        function hideApplyForm() {
            document.getElementById('applyFormContainer').style.display = 'none';
            document.querySelector('.apply-section').classList.remove('show-form');
        }
    </script>
</body>

</html>