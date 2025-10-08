<?php
session_start();
require_once '../connect/koneksi.php';
require_once '../connect/email_config.php';

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

// Get candidate ID
$candidate_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$candidate_id) {
    header('Location: candidates.php');
    exit();
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $application_id = (int)$_POST['application_id'];

    if ($_POST['action'] === 'move_to_interview') {
        $interview_date = !empty($_POST['interview_date']) ? esc($conn, $_POST['interview_date']) : NULL;
        $q = "UPDATE applications SET status='tes & wawancara', updated_at=NOW()" .
            ($interview_date ? ", interview_date='$interview_date'" : "") .
            " WHERE application_id=$application_id";
        if (mysqli_query($conn, $q)) {
            $success = 'Dipindah ke Tes & Wawancara';
            logActivity($conn, $user_id, "HRD: set interview application #$application_id");
        } else {
            $error = 'Gagal memperbarui';
        }
    } elseif ($_POST['action'] === 'accept_hire') {
        $reason = esc($conn, $_POST['reason']);
        $start_date = esc($conn, $_POST['start_date']);
        $q = "UPDATE applications SET status='diterima bekerja', updated_at=NOW(), reason='$reason', start_date='$start_date' WHERE application_id=$application_id";
        if (mysqli_query($conn, $q)) {
            $info = mysqli_query($conn, "SELECT a.*, u.email, u.full_name, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.application_id=$application_id");
            if ($info) {
                $row = mysqli_fetch_assoc($info);
                $to = $row['email'];
                $subject = 'Selamat Bergabung - ' . $row['title'];
                $msg = "Halo " . $row['full_name'] . ",\n\n" .
                    "Selamat! Anda DITERIMA BEKERJA pada posisi " . $row['title'] . ".\n" .
                    "Tanggal Mulai: " . $start_date . "\n" .
                    "Catatan: " . $reason . "\n\nSampai jumpa di hari pertama.\nHRD";
                sendEmail($to, $subject, $msg);
                logActivity($conn, $user_id, "HRD: terima bekerja application #$application_id (" . $row['title'] . ")");
            }
            $success = 'Kandidat diterima bekerja';
        } else {
            $error = 'Gagal memperbarui';
        }
    } elseif ($_POST['action'] === 'reject_after_interview') {
        $reason = esc($conn, $_POST['reason']);
        $q = "UPDATE applications SET status='ditolak', updated_at=NOW(), reason='$reason' WHERE application_id=$application_id";
        if (mysqli_query($conn, $q)) {
            $info = mysqli_query($conn, "SELECT a.*, u.email, u.full_name, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.application_id=$application_id");
            if ($info) {
                $row = mysqli_fetch_assoc($info);
                $to = $row['email'];
                $subject = 'Hasil Wawancara - ' . $row['title'];
                $msg = "Halo " . $row['full_name'] . ",\n\n" .
                    "Terima kasih telah mengikuti proses. Mohon maaf Anda BELUM DITERIMA untuk posisi " . $row['title'] . ".\n" .
                    "Alasan: " . $reason . "\n\nSemoga sukses di kesempatan berikutnya.\nHRD";
                sendEmail($to, $subject, $msg);
                logActivity($conn, $user_id, "HRD: tolak setelah interview application #$application_id (" . $row['title'] . ")");
            }
            $success = 'Kandidat ditolak';
        } else {
            $error = 'Gagal memperbarui';
        }
    }
}

// Fetch candidate details
$detail = mysqli_query($conn, "
    SELECT a.*, u.full_name, u.email, l.title 
    FROM applications a 
    JOIN users u ON a.user_id = u.user_id 
    JOIN lowongan l ON a.job_id = l.job_id 
    WHERE a.application_id = $candidate_id
");

if ($detail && mysqli_num_rows($detail) > 0) {
    $candidate = mysqli_fetch_assoc($detail);
} else {
    header('Location: candidates.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kandidat - <?php echo htmlspecialchars($candidate['full_name']); ?></title>
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/applications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Detail Kandidat</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="lowongan.php"><i class="fas fa-briefcase"></i> Kelola Lowongan</a></li>
                <li><a href="applications.php"><i class="fas fa-file-alt"></i> Kelola Lamaran</a></li>
                <li><a href="candidates.php" class="active"><i class="fas fa-users"></i> Kandidat</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <div class="header-with-back">
                    <a href="candidates.php" class="back-button">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kandidat
                    </a>
                    <h1>Detail Kandidat</h1>
                    <p>Informasi lengkap kandidat</p>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user"></i> Informasi Kandidat</h3>
                    <a href="candidates.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Tutup
                    </a>
                </div>
                <div class="card-body">
                    <div class="detail-section">
                        <h4><i class="fas fa-user-circle"></i> Data Pribadi</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-user"></i> Nama Lengkap</div>
                                <div class="detail-value"><?php echo htmlspecialchars($candidate['full_name']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-envelope"></i> Email</div>
                                <div class="detail-value">
                                    <a href="mailto:<?php echo htmlspecialchars($candidate['email']); ?>" class="btn-link">
                                        <?php echo htmlspecialchars($candidate['email']); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-phone"></i> Nomor Telepon</div>
                                <div class="detail-value">
                                    <?php if (!empty($candidate['no_telepon'])): ?>
                                        <a href="tel:<?php echo htmlspecialchars($candidate['no_telepon']); ?>" class="btn-link">
                                            <i class="fas fa-phone-alt"></i>
                                            <?php echo htmlspecialchars($candidate['no_telepon']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-graduation-cap"></i> Pendidikan Terakhir</div>
                                <div class="detail-value">
                                    <?php echo !empty($candidate['pendidikan']) ? htmlspecialchars($candidate['pendidikan']) : '<span class="text-muted">Tidak ada</span>'; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h4><i class="fas fa-briefcase"></i> Informasi Lamaran</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-suitcase"></i> Posisi</div>
                                <div class="detail-value"><?php echo htmlspecialchars($candidate['title']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-info-circle"></i> Status</div>
                                <div class="detail-value">
                                    <span class="badge badge-<?php echo $candidate['status']; ?>">
                                        <?php echo htmlspecialchars($candidate['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-calendar"></i> Tanggal Melamar</div>
                                <div class="detail-value"><?php echo date('d F Y H:i', strtotime($candidate['applied_at'])); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-file-pdf"></i> CV</div>
                                <div class="detail-value">
                                    <?php if (!empty($candidate['cv'])): ?>
                                        <a href="../<?php echo htmlspecialchars($candidate['cv']); ?>" target="_blank" class="btn-link">
                                            <i class="fas fa-download"></i> Lihat/Download CV
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada CV</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($candidate['interview_date']) || !empty($candidate['reason']) || !empty($candidate['start_date'])): ?>
                    <div class="detail-section">
                        <h4><i class="fas fa-comment-alt"></i> Informasi Tambahan</h4>
                        <div class="detail-grid">
                            <?php if (!empty($candidate['interview_date'])): ?>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-calendar-check"></i> Jadwal Wawancara</div>
                                <div class="detail-value"><?php echo date('d F Y H:i', strtotime($candidate['interview_date'])); ?></div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($candidate['start_date'])): ?>
<div class="detail-item">
    <div class="detail-label"><i class="fas fa-calendar-day"></i> Tanggal Mulai Bekerja</div>
    <div class="detail-value"><?php echo date('d F Y', strtotime($candidate['start_date'])); ?></div>
</div>
<?php endif; ?>

<?php if (!empty($candidate['reason'])): ?>
<div class="detail-item">
    <div class="detail-label"><i class="fas fa-sticky-note"></i> Catatan/Alasan</div>
    <div class="detail-value"><?php echo nl2br(htmlspecialchars($candidate['reason'])); ?></div>
</div>
<?php endif; ?>
</div>
</div>
<?php endif; ?>

<!-- =======================
     Form Aksi HRD
======================= -->
<div class="detail-section">
    <h4><i class="fas fa-tasks"></i> Aksi HRD</h4>
    <div class="action-grid">
        <!-- Pindah ke Tahap Tes & Wawancara -->
        <?php if ($candidate['status'] === 'lolos administrasi'): ?>
        <form method="POST" class="action-form">
            <input type="hidden" name="application_id" value="<?php echo $candidate['application_id']; ?>">
            <input type="hidden" name="action" value="move_to_interview">
            <input type="datetime-local" name="interview_date" class="input-sm" placeholder="Tanggal Wawancara">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-calendar-check"></i> Jadwalkan Wawancara
            </button>
        </form>
        <?php endif; ?>

        <!-- Terima Bekerja -->
        <?php if ($candidate['status'] === 'tes & wawancara'): ?>
        <form method="POST" class="action-form">
            <input type="hidden" name="application_id" value="<?php echo $candidate['application_id']; ?>">
            <input type="hidden" name="action" value="accept_hire">
            <input type="date" name="start_date" class="input-sm" placeholder="Tanggal Mulai" required>
            <input type="text" name="reason" class="input-sm" placeholder="Catatan (opsional)">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-user-check"></i> Terima Bekerja
            </button>
        </form>

        <!-- Tolak Setelah Wawancara -->
        <form method="POST" class="action-form">
            <input type="hidden" name="application_id" value="<?php echo $candidate['application_id']; ?>">
            <input type="hidden" name="action" value="reject_after_interview">
            <input type="text" name="reason" class="input-sm" placeholder="Alasan Penolakan" required>
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-user-times"></i> Tolak Kandidat
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

</div>
</div>
</div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("open");
}
</script>
</body>
</html>
                                