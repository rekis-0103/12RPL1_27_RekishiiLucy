<?php
session_start();
require_once 'connect/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Waindo Specterra</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/pages.css">
    <link rel="stylesheet" href="assets/css/bergabung.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <?php
    function logActivity($conn, $userId, $action) {
        $uid = $userId ? (int)$userId : 'NULL';
        $actionEsc = mysqli_real_escape_string($conn, $action);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '::1';
        $ipEsc = mysqli_real_escape_string($conn, $ip);
        mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action, ip_address) VALUES ($uid, '$actionEsc', '$ipEsc')");
    }

    $isLoggedIn = isset($_SESSION['user_id']);
    $userId = $isLoggedIn ? (int)$_SESSION['user_id'] : null;
    $userRole = $isLoggedIn ? $_SESSION['role'] : null;

    // Log page view for logged-in users
    if ($isLoggedIn) {
        logActivity($conn, $userId, 'Buka halaman Bergabung');
    }

    // Handle application submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_public') {
        if (!$isLoggedIn || $userRole !== 'pelamar') {
            $error = 'Silakan login sebagai pelamar untuk mengirim lamaran.';
            logActivity($conn, $userId, 'Gagal kirim lamaran (belum login atau bukan pelamar)');
        } else {
            $job_id = (int)$_POST['job_id'];
            // Upload CV
            $uploadDir = __DIR__ . '/uploads/cv/';
            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
            if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Unggah CV gagal. Pastikan file dipilih.';
                logActivity($conn, $userId, 'Gagal kirim lamaran (CV tidak valid)');
            } else {
                $ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
                $allowed = ['pdf','doc','docx'];
                if (!in_array(strtolower($ext), $allowed)) {
                    $error = 'Format CV harus PDF/DOC/DOCX';
                    logActivity($conn, $userId, 'Gagal kirim lamaran (format CV salah)');
                } else {
                    $safeName = 'cv_'.$userId.'_'.$job_id.'_'.time().'.'.strtolower($ext);
                    $targetPath = $uploadDir.$safeName;
                    $relPath = 'uploads/cv/'.$safeName;
                    if (move_uploaded_file($_FILES['cv']['tmp_name'], $targetPath)) {
                        $cvEsc = mysqli_real_escape_string($conn, $relPath);
                        $q = "INSERT INTO applications (job_id, user_id, cv, status, applied_at) VALUES ($job_id, $userId, '$cvEsc', 'pendaftaran diterima', NOW())";
                        if (mysqli_query($conn, $q)) {
                            $success = 'Lamaran berhasil dikirim';
                            logActivity($conn, $userId, "Kirim lamaran (job #$job_id)");
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
    }

    // Fetch open jobs
    $jobs = mysqli_query($conn, "SELECT job_id, title, description, location, salary_range, posted_at FROM lowongan WHERE status='open' AND hapus=0 ORDER BY posted_at DESC");
    ?>

    <div class="page-container">
        <main class="page-content">
            <div class="container">
                <h1>Bergabung</h1>
                <p>Temukan lowongan yang sesuai dan kirimkan lamaran Anda.</p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-briefcase"></i> Lowongan Tersedia</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($jobs && mysqli_num_rows($jobs) > 0): ?>
                            <?php while ($job = mysqli_fetch_assoc($jobs)): ?>
                                <div class="job-item">
                                    <h4 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h4>
                                    <div class="job-meta">Lokasi: <?php echo htmlspecialchars($job['location'] ?: '-'); ?> | Gaji: <?php echo htmlspecialchars($job['salary_range'] ?: '-'); ?></div>
                                    <div class="job-desc"><?php echo nl2br(htmlspecialchars($job['description'])); ?></div>
                                    <?php if ($isLoggedIn && $userRole === 'pelamar'): ?>
                                        <form method="POST" enctype="multipart/form-data" class="apply-form">
                                            <input type="hidden" name="action" value="apply_public">
                                            <input type="hidden" name="job_id" value="<?php echo (int)$job['job_id']; ?>">
                                            <label>CV (PDF/DOC/DOCX): </label>
                                            <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
                                            <button type="submit" class="btn btn-primary">Kirim Lamaran</button>
                                        </form>
                                    <?php else: ?>
                                        <a class="btn btn-primary" href="login.php">Login untuk melamar</a>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>Tidak ada lowongan aktif saat ini.</p>
                        <?php endif; ?>
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
    <script src="js/navbar.js"></script>
</body>

</html>