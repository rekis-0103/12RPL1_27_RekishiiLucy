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
        
        // Validasi tanggal tidak boleh mundur
        if ($interview_date) {
            $interview_timestamp = strtotime($interview_date);
            $now = time();
            
            if ($interview_timestamp < $now) {
                $error = 'Tanggal dan waktu wawancara tidak boleh mundur dari sekarang!';
            } else {
                $q = "UPDATE applications SET status='tes & wawancara', updated_at=NOW(), interview_date='$interview_date' WHERE application_id=$application_id";
                if (mysqli_query($conn, $q)) {
                    $info = mysqli_query($conn, "SELECT a.*, u.email, u.full_name, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.application_id=$application_id");
                    if ($info) {
                        $row = mysqli_fetch_assoc($info);
                        $to = $row['email'];
                        $subject = 'Jadwal Wawancara - ' . $row['title'];
                        $msg = "Halo " . $row['full_name'] . ",\n\n" .
                            "Selamat! Anda dijadwalkan untuk mengikuti Tes & Wawancara untuk posisi " . $row['title'] . ".\n" .
                            "Jadwal: " . date('d F Y, H:i', strtotime($interview_date)) . " WIB\n\n" .
                            "Mohon hadir tepat waktu.\n\nTerima kasih.\nHRD";
                        sendEmail($to, $subject, $msg);
                    }
                    $success = 'Jadwal wawancara berhasil diset';
                    logActivity($conn, $user_id, "HRD: set interview application #$application_id");
                } else {
                    $error = 'Gagal memperbarui jadwal wawancara';
                }
            }
        } else {
            $error = 'Tanggal wawancara wajib diisi';
        }
    } elseif ($_POST['action'] === 'accept_hire') {
        $reason = esc($conn, $_POST['reason']);
        $start_date = esc($conn, $_POST['start_date']);
        
        // Validasi tanggal mulai bekerja tidak boleh mundur
        $start_timestamp = strtotime($start_date);
        $today = strtotime(date('Y-m-d'));
        
        if ($start_timestamp < $today) {
            $error = 'Tanggal mulai bekerja tidak boleh mundur dari hari ini!';
        } else {
            $q = "UPDATE applications SET status='diterima bekerja', updated_at=NOW(), reason='$reason', start_date='$start_date' WHERE application_id=$application_id";
            if (mysqli_query($conn, $q)) {
                $info = mysqli_query($conn, "SELECT a.*, u.email, u.full_name, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.application_id=$application_id");
                if ($info) {
                    $row = mysqli_fetch_assoc($info);
                    $to = $row['email'];
                    $subject = 'Selamat Bergabung - ' . $row['title'];
                    $msg = "Halo " . $row['full_name'] . ",\n\n" .
                        "Selamat! Anda DITERIMA BEKERJA pada posisi " . $row['title'] . ".\n" .
                        "Tanggal Mulai: " . date('d F Y', strtotime($start_date)) . "\n" .
                        (!empty($reason) ? "Catatan: " . $reason . "\n" : "") .
                        "\nSampai jumpa di hari pertama.\nHRD";
                    sendEmail($to, $subject, $msg);
                    logActivity($conn, $user_id, "HRD: terima bekerja application #$application_id (" . $row['title'] . ")");
                }
                $success = 'Kandidat diterima bekerja';
            } else {
                $error = 'Gagal memperbarui status';
            }
        }
    } elseif ($_POST['action'] === 'reject_after_interview') {
        $reason = esc($conn, $_POST['reason']);
        $q = "UPDATE applications SET status='ditolak tes & wawancara', updated_at=NOW(), reason='$reason' WHERE application_id=$application_id";
        if (mysqli_query($conn, $q)) {
            $info = mysqli_query($conn, "SELECT a.*, u.email, u.full_name, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.application_id=$application_id");
            if ($info) {
                $row = mysqli_fetch_assoc($info);
                $to = $row['email'];
                $subject = 'Hasil Wawancara - ' . $row['title'];
                $msg = "Halo " . $row['full_name'] . ",\n\n" .
                    "Terima kasih telah mengikuti proses wawancara. Mohon maaf Anda BELUM DITERIMA untuk posisi " . $row['title'] . ".\n" .
                    "Alasan: " . $reason . "\n\nSemoga sukses di kesempatan berikutnya.\nHRD";
                sendEmail($to, $subject, $msg);
                logActivity($conn, $user_id, "HRD: tolak setelah interview application #$application_id (" . $row['title'] . ")");
            }
            $success = 'Kandidat ditolak';
        } else {
            $error = 'Gagal memperbarui status';
        }
    } elseif ($_POST['action'] === 'reject_before_interview') {
        $reason = esc($conn, $_POST['reason']);
        $q = "UPDATE applications SET status='ditolak administrasi', updated_at=NOW(), reason='$reason' WHERE application_id=$application_id";
        if (mysqli_query($conn, $q)) {
            $info = mysqli_query($conn, "SELECT a.*, u.email, u.full_name, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.application_id=$application_id");
            if ($info) {
                $row = mysqli_fetch_assoc($info);
                $to = $row['email'];
                $subject = 'Hasil Seleksi - ' . $row['title'];
                $msg = "Halo " . $row['full_name'] . ",\n\n" .
                    "Terima kasih atas minat Anda. Mohon maaf lamaran Anda DITOLAK untuk posisi " . $row['title'] . ".\n" .
                    "Alasan: " . $reason . "\n\nSemoga sukses di kesempatan berikutnya.\nHRD";
                sendEmail($to, $subject, $msg);
                logActivity($conn, $user_id, "HRD: tolak kandidat application #$application_id (" . $row['title'] . ")");
            }
            $success = 'Kandidat ditolak';
        } else {
            $error = 'Gagal memperbarui status';
        }
    }
}

// Fetch candidate details with education data
$detail = mysqli_query($conn, "
    SELECT a.*, u.full_name, u.email, l.title,
    jenjang.nama_jenjang,
    jurusan.nama_jurusan
    FROM applications a 
    JOIN users u ON a.user_id = u.user_id 
    JOIN lowongan l ON a.job_id = l.job_id 
    LEFT JOIN jenjang_pendidikan jenjang ON a.id_jenjang_pendidikan = jenjang.id_jenjang
    LEFT JOIN jurusan_pendidikan jurusan ON a.id_jurusan_pendidikan = jurusan.id_jurusan
    WHERE a.application_id = $candidate_id
");

if ($detail && mysqli_num_rows($detail) > 0) {
    $candidate = mysqli_fetch_assoc($detail);
} else {
    header('Location: candidates.php');
    exit();
}

// Get minimum datetime for validation (now)
$min_datetime = date('Y-m-d\TH:i');
$min_date = date('Y-m-d');
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
                                        <?php echo htmlspecialchars($candidate['email']); ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-phone"></i> Nomor Telepon</div>
                                <div class="detail-value">
                                    <?php if (!empty($candidate['no_telepon'])): ?>
                                            <i class="fas fa-phone-alt"></i>
                                            <?php echo htmlspecialchars($candidate['no_telepon']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-graduation-cap"></i> Pendidikan Terakhir</div>
                                <div class="detail-value">
                                    <?php 
                                    if (!empty($candidate['nama_jenjang'])) {
                                        echo htmlspecialchars($candidate['nama_jenjang']);
                                        if (!empty($candidate['nama_jurusan'])) {
                                            echo ' - ' . htmlspecialchars($candidate['nama_jurusan']);
                                        }
                                    } else {
                                        echo '<span class="text-muted">Tidak ada</span>';
                                    }
                                    ?>
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
                                        <a href="../pelamar/cv/<?php echo htmlspecialchars($candidate['cv']); ?>" target="_blank" class="btn-link">
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
                            <div class="detail-item full-width">
                                <div class="detail-label"><i class="fas fa-sticky-note"></i> Catatan/Alasan</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($candidate['reason'])); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Form Aksi HRD -->
                    <div class="detail-section">
                        <h4><i class="fas fa-tasks"></i> Aksi HRD</h4>
                        <div class="actions">
                            <!-- Untuk Status: Lolos Administrasi -->
                            <?php if ($candidate['status'] === 'lolos administrasi'): ?>
                            <form method="POST" class="action-form accept-form" onsubmit="return validateInterviewDate()">
                                <h4 class="form-title"><i class="fas fa-calendar-check"></i> Jadwalkan Wawancara</h4>
                                <input type="hidden" name="application_id" value="<?php echo $candidate['application_id']; ?>">
                                <input type="hidden" name="action" value="move_to_interview">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-alt"></i> Tanggal & Waktu Wawancara <span class="required">*</span></label>
                                    <input type="datetime-local" id="interview_date" name="interview_date" class="form-control" min="<?php echo $min_datetime; ?>" required>
                                    <small class="form-hint">Pilih tanggal dan waktu wawancara (tidak boleh mundur dari sekarang)</small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-calendar-check"></i> Set Jadwal Wawancara
                                </button>
                            </form>
                            
                            <?php endif; ?>

                            <!-- Untuk Status: Tes & Wawancara -->
                            <?php if ($candidate['status'] === 'tes & wawancara'): ?>
                            <form method="POST" class="action-form accept-form" onsubmit="return validateStartDate() && confirm('Yakin terima kandidat ini?')">
                                <h4 class="form-title"><i class="fas fa-user-check"></i> Terima Bekerja</h4>
                                <input type="hidden" name="application_id" value="<?php echo $candidate['application_id']; ?>">
                                <input type="hidden" name="action" value="accept_hire">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-alt"></i> Tanggal Mulai Bekerja <span class="required">*</span></label>
                                    <input type="date" id="start_date" name="start_date" class="form-control" min="<?php echo $min_date; ?>" required>
                                    <small class="form-hint">Pilih tanggal mulai bekerja (tidak boleh mundur dari hari ini)</small>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-comment"></i> Catatan (opsional)</label>
                                    <input type="text" name="reason" class="form-control" placeholder="Catatan untuk kandidat">
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-user-check"></i> Terima Bekerja
                                </button>
                            </form>

                            <form method="POST" class="action-form reject-form" onsubmit="return confirm('Yakin tolak kandidat ini?')">
                                <h4 class="form-title"><i class="fas fa-user-times"></i> Tolak Kandidat</h4>
                                <input type="hidden" name="application_id" value="<?php echo $candidate['application_id']; ?>">
                                <input type="hidden" name="action" value="reject_after_interview">
                                <div class="form-group">
                                    <label><i class="fas fa-comment"></i> Alasan Penolakan <span class="required">*</span></label>
                                    <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Hasil wawancara kurang memuaskan" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger btn-block">
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

    <script src="../js/navbar.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.mobile-toggle');

            sidebar.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                toggleBtn.style.display = "none";
            } else {
                toggleBtn.style.display = "block";
            }
        }

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-toggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                    mobileToggle.style.display = "block";
                }
            }
        });

        // Validasi tanggal wawancara
        function validateInterviewDate() {
            const interviewInput = document.getElementById('interview_date');
            if (!interviewInput) return true;
            
            const selectedDate = new Date(interviewInput.value);
            const now = new Date();
            
            if (selectedDate < now) {
                alert('Tanggal dan waktu wawancara tidak boleh mundur dari sekarang!');
                return false;
            }
            return true;
        }

        // Validasi tanggal mulai bekerja
        function validateStartDate() {
            const startInput = document.getElementById('start_date');
            if (!startInput) return true;
            
            const selectedDate = new Date(startInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                alert('Tanggal mulai bekerja tidak boleh mundur dari hari ini!');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>