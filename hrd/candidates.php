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

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['application_id'])) {
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

// Fetch candidates
$list = mysqli_query($conn, "SELECT a.*, u.full_name, u.email, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.status IN ('lolos administrasi','tes & wawancara') ORDER BY COALESCE(a.interview_date,a.updated_at) ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kandidat - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/candidates.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Kandidat</h3>
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
                <h1>Kandidat</h1>
                <p>Kelola kandidat pada tahap lanjutan</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Daftar Kandidat</h3>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Posisi</th>
                                <th>Status</th>
                                <th>Jadwal Wawancara</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($list && mysqli_num_rows($list) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($list)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><span class="badge"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['interview_date'] ?: '-'); ?></td>
                                        <td>
                                            <div class="row-actions">
                                                <?php if ($row['status'] === 'lolos administrasi'): ?>
                                                    <form method="POST" class="inline">
                                                        <input type="hidden" name="action" value="move_to_interview">
                                                        <input type="hidden" name="application_id" value="<?php echo (int)$row['application_id']; ?>">
                                                        <input type="datetime-local" name="interview_date" class="input-sm">
                                                        <button type="submit" class="btn btn-primary btn-sm">Set Tes & Wawancara</button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if ($row['status'] === 'tes & wawancara' || $row['status'] === 'lolos administrasi'): ?>
                                                    <form method="POST" class="inline" onsubmit="return confirm('Terima kandidat ini?')">
                                                        <input type="hidden" name="action" value="accept_hire">
                                                        <input type="hidden" name="application_id" value="<?php echo (int)$row['application_id']; ?>">
                                                        <input type="date" name="start_date" class="input-sm" required>
                                                        <input type="text" name="reason" class="input-sm" placeholder="Alasan/Note" required>
                                                        <button type="submit" class="btn btn-primary btn-sm">Terima Bekerja</button>
                                                    </form>
                                                    <form method="POST" class="inline" onsubmit="return confirm('Tolak kandidat ini?')">
                                                        <input type="hidden" name="action" value="reject_after_interview">
                                                        <input type="hidden" name="application_id" value="<?php echo (int)$row['application_id']; ?>">
                                                        <input type="text" name="reason" class="input-sm" placeholder="Alasan Ditolak" required>
                                                        <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada kandidat</td>
                                </tr>
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
            const toggleBtn = document.querySelector('.mobile-toggle');

            sidebar.classList.toggle('active');

            // Sembunyikan tombol ketika sidebar muncul
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
                }
            }
        });
    </script>
</body>

</html>