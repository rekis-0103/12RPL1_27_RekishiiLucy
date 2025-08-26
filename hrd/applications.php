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

// Actions on detail page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['application_id'])) {
    $application_id = (int)$_POST['application_id'];
    if ($_POST['action'] === 'accept_admin') {
        $reason = esc($conn, $_POST['reason']);
        $interview_date = !empty($_POST['interview_date']) ? esc($conn, $_POST['interview_date']) : NULL;
        $q = "UPDATE applications SET status='lolos administrasi', updated_at=NOW(), reason='$reason'" .
            ($interview_date ? ", interview_date='$interview_date'" : "") .
            " WHERE application_id=$application_id";
        if (mysqli_query($conn, $q)) {
            $info = mysqli_query($conn, "SELECT a.*, u.email, u.full_name, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.application_id=$application_id");
            if ($info) {
                $row = mysqli_fetch_assoc($info);
                $to = $row['email'];
                $subject = 'Hasil Seleksi Administrasi - ' . $row['title'];
                $msg = "Halo " . $row['full_name'] . ",\n\n" .
                    "Selamat, Anda LOLOS seleksi administrasi untuk posisi " . $row['title'] . ".\n" .
                    "Alasan: " . $reason . "\n" .
                    ($interview_date ? "Jadwal Wawancara: " . $interview_date . "\n" : "") .
                    "\nTerima kasih.\nHRD";
                sendEmail($to, $subject, $msg);
                logActivity($conn, $user_id, "HRD: terima administrasi application #$application_id (" . $row['title'] . ")");
            }
            $success = 'Lamaran diterima pada seleksi administrasi';
        } else {
            $error = 'Gagal memperbarui status';
        }
    } elseif ($_POST['action'] === 'reject_admin') {
        $reason = esc($conn, $_POST['reason']);
        $q = "UPDATE applications SET status='ditolak', updated_at=NOW(), reason='$reason' WHERE application_id=$application_id";
        if (mysqli_query($conn, $q)) {
            $info = mysqli_query($conn, "SELECT a.*, u.email, u.full_name, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.application_id=$application_id");
            if ($info) {
                $row = mysqli_fetch_assoc($info);
                $to = $row['email'];
                $subject = 'Hasil Seleksi Administrasi - ' . $row['title'];
                $msg = "Halo " . $row['full_name'] . ",\n\n" .
                    "Mohon maaf, lamaran Anda TIDAK LOLOS seleksi administrasi untuk posisi " . $row['title'] . ".\n" .
                    "Alasan: " . $reason . "\n\nTerima kasih.\nHRD";
                sendEmail($to, $subject, $msg);
                logActivity($conn, $user_id, "HRD: tolak administrasi application #$application_id (" . $row['title'] . ")");
            }
            $success = 'Lamaran ditolak';
        } else {
            $error = 'Gagal memperbarui status';
        }
    }
}

// Detail view
$detail = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $detail = mysqli_query($conn, "SELECT a.*, u.full_name, u.email, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.application_id=$id");
    if ($detail && mysqli_num_rows($detail) === 1) {
        $detail_row = mysqli_fetch_assoc($detail);
        if ($detail_row['status'] === 'pendaftaran diterima') {
            mysqli_query($conn, "UPDATE applications SET status='seleksi administrasi', updated_at=NOW() WHERE application_id=$id");
            $detail_row['status'] = 'seleksi administrasi';
            logActivity($conn, $user_id, "HRD: buka detail application #$id => seleksi administrasi");
        }
    } else {
        $detail = null;
    }
}

// List of new applications
$list = mysqli_query($conn, "SELECT a.*, u.full_name, u.email, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.status IN ('pendaftaran diterima', 'seleksi administrasi') ORDER BY a.applied_at DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lamaran - PT Waindo Specterra</title>
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
                <h3>Kelola Lamaran</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="lowongan.php"><i class="fas fa-briefcase"></i> Kelola Lowongan</a></li>
                <li><a href="applications.php" class="active"><i class="fas fa-file-alt"></i> Kelola Lamaran</a></li>
                <li><a href="candidates.php"><i class="fas fa-users"></i> Kandidat</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Kelola Lamaran</h1>
                <p>Review lamaran dan tentukan hasil seleksi administrasi</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($detail_row)): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user"></i> Detail Lamaran</h3>
                        <a href="applications.php" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                    <div class="card-body">
                        <div class="detail-grid">
                            <div>
                                <div><strong>Nama</strong>: <?php echo htmlspecialchars($detail_row['full_name']); ?></div>
                                <div><strong>Email</strong>: <?php echo htmlspecialchars($detail_row['email']); ?></div>
                                <div><strong>Posisi</strong>: <?php echo htmlspecialchars($detail_row['title']); ?></div>
                                <div><strong>Status</strong>: <span class="badge"><?php echo htmlspecialchars($detail_row['status']); ?></span></div>
                                <div><strong>Tanggal Lamar</strong>: <?php echo date('d/m/Y H:i', strtotime($detail_row['applied_at'])); ?></div>
                            </div>
                            <div>
                                <div><strong>CV</strong>:
                                    <?php if (!empty($detail_row['cv'])): ?>
                                        <a href="../<?php echo htmlspecialchars($detail_row['cv']); ?>" target="_blank">Lihat CV</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($detail_row['reason'])): ?>
                                    <div><strong>Alasan</strong>: <?php echo htmlspecialchars($detail_row['reason']); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($detail_row['interview_date'])): ?>
                                    <div><strong>Jadwal Wawancara</strong>: <?php echo htmlspecialchars($detail_row['interview_date']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="actions">
                            <form method="POST" class="action-form">
                                <input type="hidden" name="application_id" value="<?php echo (int)$detail_row['application_id']; ?>">
                                <input type="hidden" name="action" value="accept_admin">
                                <div class="form-group">
                                    <label>Alasan Diterima</label>
                                    <textarea name="reason" rows="2" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Jadwal Wawancara (opsional)</label>
                                    <input type="datetime-local" name="interview_date">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Terima (Lolos Administrasi)</button>
                            </form>
                            <form method="POST" class="action-form" onsubmit="return confirm('Yakin tolak lamaran ini?')">
                                <input type="hidden" name="application_id" value="<?php echo (int)$detail_row['application_id']; ?>">
                                <input type="hidden" name="action" value="reject_admin">
                                <div class="form-group">
                                    <label>Alasan Ditolak</label>
                                    <textarea name="reason" rows="2" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Tolak</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-inbox"></i> Lamaran (Pendaftaran Diterima)</h3>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Posisi</th>
                                    <th>Tanggal Lamar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($list && mysqli_num_rows($list) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($list)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['applied_at'])); ?></td>
                                            <td>
                                                <a class="btn btn-primary btn-sm" href="applications.php?id=<?php echo (int)$row['application_id']; ?>">
                                                    <i class="fas fa-eye"></i> Periksa
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada lamaran baru</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Tabel Semua Pelamar -->
            <div class="card">
                <div class="card-header">
                    <h3>Semua Pelamar</h3>
                    <form method="GET" style="display:flex; gap:10px;">
                        <select name="status_filter" class="form-control">
                            <option value="" <?= empty($_GET['status_filter']) ? 'selected' : '' ?>>-- Semua Status --</option>
                            <option value="pendaftaran diterima" <?= ($_GET['status_filter'] ?? '') === 'pendaftaran diterima' ? 'selected' : '' ?>>Pendaftaran Diterima</option>
                            <option value="seleksi administrasi" <?= ($_GET['status_filter'] ?? '') === 'seleksi administrasi' ? 'selected' : '' ?>>Seleksi Administrasi</option>
                            <option value="lolos administrasi" <?= ($_GET['status_filter'] ?? '') === 'lolos administrasi' ? 'selected' : '' ?>>Lolos Administrasi</option>
                            <option value="tes & wawancara" <?= ($_GET['status_filter'] ?? '') === 'tes & wawancara' ? 'selected' : '' ?>>Tes & Wawancara</option>
                            <option value="diterima bekerja" <?= ($_GET['status_filter'] ?? '') === 'diterima bekerja' ? 'selected' : '' ?>>Diterima Bekerja</option>
                            <option value="ditolak" <?= ($_GET['status_filter'] ?? '') === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        </select>

                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="export_pdf.php?status=<?= $_GET['status_filter'] ?? '' ?>"
                            class="btn btn-danger">Export PDF</a>
                    </form>
                </div>
                <div class="card-body table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Nama Pekerjaan</th>
                                <th>tempat</th>
                                <th>Status</th>
                                <th>Tanggal Lamar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $filter = "";
                            if (!empty($_GET['status_filter'])) {
                                $filter = "WHERE a.status = '" . mysqli_real_escape_string($conn, $_GET['status_filter']) . "'";
                            }

                            $sql = "SELECT u.full_name AS nama, 
               l.title AS nama_pekerjaan, 
               l.location AS tempat, 
               a.status, 
               a.applied_at AS tanggal_lamar
        FROM applications a
        JOIN users u ON u.user_id = a.user_id
        JOIN lowongan l ON l.job_id = a.job_id
        $filter
        ORDER BY a.applied_at DESC";

                            $result = mysqli_query($conn, $sql);

                            if ($result && mysqli_num_rows($result) > 0) {
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                <td>{$no}</td>
                <td>{$row['nama']}</td>
                <td>{$row['nama_pekerjaan']}</td>
                <td>{$row['tempat']}</td>
                <td>{$row['status']}</td>
                <td>{$row['tanggal_lamar']}</td>
              </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr>
            <td colspan='6' class='text-center'>Tidak ada data untuk filter ini</td>
          </tr>";
                            }
                            ?>
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