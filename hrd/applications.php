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
        // Hapus bagian interview_date dari sini
        $q = "UPDATE applications SET status='lolos administrasi', updated_at=NOW(), reason='$reason' WHERE application_id=$application_id";
        if (mysqli_query($conn, $q)) {
            $info = mysqli_query($conn, "SELECT a.*, u.email, u.full_name, l.title FROM applications a JOIN users u ON a.user_id=u.user_id JOIN lowongan l ON a.job_id=l.job_id WHERE a.application_id=$application_id");
            if ($info) {
                $row = mysqli_fetch_assoc($info);
                $to = $row['email'];
                $subject = 'Hasil Seleksi Administrasi - ' . $row['title'];
                $msg = "Halo " . $row['full_name'] . ",\n\n" .
                    "Selamat, Anda LOLOS seleksi administrasi untuk posisi " . $row['title'] . ".\n" .
                    "Alasan: " . $reason . "\n\n" .
                    "Kami akan menghubungi Anda untuk jadwal wawancara selanjutnya.\n\n" .
                    "Terima kasih.\nHRD";
                sendEmail($to, $subject, $msg);
                logActivity($conn, $user_id, "HRD: terima administrasi application #$application_id (" . $row['title'] . ")");
            }
            $success = 'Lamaran diterima pada seleksi administrasi. Silakan jadwalkan wawancara di menu Kandidat.';
        } else {
            $error = 'Gagal memperbarui status';
        }
    } elseif ($_POST['action'] === 'reject_admin') {
        $reason = esc($conn, $_POST['reason']);
        $q = "UPDATE applications SET status='ditolak administrasi', updated_at=NOW(), reason='$reason' WHERE application_id=$application_id";
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

    mysqli_query($conn, "
        UPDATE applications 
        SET status='seleksi administrasi', updated_at=NOW() 
        WHERE application_id=$id AND status='pending'
    ");


    $detail = mysqli_query($conn, "
        SELECT a.*, u.full_name, u.email, l.title,
        jenjang.nama_jenjang,
        jurusan.nama_jurusan
        FROM applications a 
        JOIN users u ON a.user_id = u.user_id 
        JOIN lowongan l ON a.job_id = l.job_id 
        LEFT JOIN jenjang_pendidikan jenjang ON a.id_jenjang_pendidikan = jenjang.id_jenjang
        LEFT JOIN jurusan_pendidikan jurusan ON a.id_jurusan_pendidikan = jurusan.id_jurusan
        WHERE a.application_id = $id AND l.posted_by = $user_id
    ");

    if ($detail && mysqli_num_rows($detail) > 0) {
        $detail = mysqli_fetch_assoc($detail);
    } else {
        $detail = null;
    }
}

// List of new applications
$list = mysqli_query($conn, "
    SELECT a.*, u.full_name, u.email, l.title,
    jenjang.nama_jenjang,
    jurusan.nama_jurusan
    FROM applications a 
    JOIN users u ON a.user_id=u.user_id 
    JOIN lowongan l ON a.job_id=l.job_id 
    LEFT JOIN jenjang_pendidikan jenjang ON a.id_jenjang_pendidikan = jenjang.id_jenjang
    LEFT JOIN jurusan_pendidikan jurusan ON a.id_jurusan_pendidikan = jurusan.id_jurusan
    WHERE a.status IN ('pending', 'seleksi administrasi') 
      AND l.posted_by=$user_id
    ORDER BY a.applied_at DESC
");
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

            <?php if (isset($detail)): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user"></i> Detail Lamaran</h3>
                        <a href="applications.php" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                    <div class="card-body">
                        <div class="detail-section">
                            <h4><i class="fas fa-user-circle"></i> Informasi Pelamar</h4>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-user"></i> Nama Lengkap</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($detail['full_name']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-envelope"></i> Email</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($detail['email']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-phone"></i> Nomor Telepon</div>
                                    <div class="detail-value">
                                        <?php echo !empty($detail['no_telepon']) ? htmlspecialchars($detail['no_telepon']) : '-'; ?>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-graduation-cap"></i> Pendidikan Terakhir</div>
                                    <div class="detail-value">
                                        <?php
                                        if (!empty($detail['nama_jenjang'])) {
                                            echo htmlspecialchars($detail['nama_jenjang']);
                                            if (!empty($detail['nama_jurusan'])) {
                                                echo ' - ' . htmlspecialchars($detail['nama_jurusan']);
                                            }
                                        } else {
                                            echo '-';
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
                                    <div class="detail-value"><?php echo htmlspecialchars($detail['title']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-info-circle"></i> Status</div>
                                    <div class="detail-value">
                                        <span class="badge badge-<?php echo $detail['status']; ?>">
                                            <?php echo htmlspecialchars($detail['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-calendar"></i> Tanggal Lamar</div>
                                    <div class="detail-value"><?php echo date('d F Y H:i', strtotime($detail['applied_at'])); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-file-pdf"></i> CV</div>
                                    <div class="detail-value">
                                        <?php if (!empty($detail['cv'])): ?>
                                            <a href="../pelamar/cv/<?php echo htmlspecialchars($detail['cv']); ?>" target="_blank" class="btn-link">
                                                <i class="fas fa-download"></i> Lihat/Download CV
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada CV</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($detail['reason'])): ?>
                            <div class="detail-section">
                                <h4><i class="fas fa-comment-alt"></i> Informasi Tambahan</h4>
                                <div class="detail-grid">
                                    <div class="detail-item full-width">
                                        <div class="detail-label"><i class="fas fa-sticky-note"></i> Alasan/Catatan</div>
                                        <div class="detail-value"><?php echo htmlspecialchars($detail['reason']); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="actions">
                            <form method="POST" class="action-form accept-form">
                                <h4 class="form-title"><i class="fas fa-check-circle"></i> Terima Lamaran</h4>
                                <input type="hidden" name="application_id" value="<?php echo (int)$detail['application_id']; ?>">
                                <input type="hidden" name="action" value="accept_admin">
                                <div class="form-group">
                                    <label><i class="fas fa-comment"></i> Alasan Diterima <span class="required">*</span></label>
                                    <textarea name="reason" rows="3" placeholder="Contoh: Memenuhi persyaratan administrasi" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-check"></i> Terima (Lolos Administrasi)
                                </button>
                            </form>

                            <form method="POST" class="action-form reject-form" onsubmit="return confirm('Yakin tolak lamaran ini?')">
                                <h4 class="form-title"><i class="fas fa-times-circle"></i> Tolak Lamaran</h4>
                                <input type="hidden" name="application_id" value="<?php echo (int)$detail['application_id']; ?>">
                                <input type="hidden" name="action" value="reject_admin">
                                <div class="form-group">
                                    <label><i class="fas fa-comment"></i> Alasan Ditolak <span class="required">*</span></label>
                                    <textarea name="reason" rows="3" placeholder="Contoh: Tidak memenuhi persyaratan minimal" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-times"></i> Tolak Lamaran
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-inbox"></i> Lamaran Baru (Pendaftaran Diterima)</h3>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Posisi</th>
                                    <th>Telepon</th>
                                    <th>Pendidikan</th>
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
                                            <td><?php echo !empty($row['no_telepon']) ? htmlspecialchars($row['no_telepon']) : '-'; ?></td>
                                            <td>
                                                <?php
                                                if (!empty($row['nama_jenjang'])) {
                                                    echo htmlspecialchars($row['nama_jenjang']);
                                                    if (!empty($row['nama_jurusan'])) {
                                                        echo ' - ' . htmlspecialchars($row['nama_jurusan']);
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
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
                                        <td colspan="6" class="text-center">Tidak ada lamaran baru</td>
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
                    <h3><i class="fas fa-users"></i> Semua Pelamar</h3>
                    <form method="GET" class="filter-form">
                        <select name="status_filter" class="form-control">
                            <option value="" <?= empty($_GET['status_filter']) ? 'selected' : '' ?>>-- Semua Status --</option>
                            <option value="pending" <?= ($_GET['status_filter'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="seleksi administrasi" <?= ($_GET['status_filter'] ?? '') === 'seleksi administrasi' ? 'selected' : '' ?>>Seleksi Administrasi</option>
                            <option value="lolos administrasi" <?= ($_GET['status_filter'] ?? '') === 'lolos administrasi' ? 'selected' : '' ?>>Lolos Administrasi</option>
                            <option value="tes & wawancara" <?= ($_GET['status_filter'] ?? '') === 'tes & wawancara' ? 'selected' : '' ?>>Tes & Wawancara</option>
                            <option value="diterima bekerja" <?= ($_GET['status_filter'] ?? '') === 'diterima bekerja' ? 'selected' : '' ?>>Diterima Bekerja</option>
                            <option value="ditolak" <?= ($_GET['status_filter'] ?? '') === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        </select>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="export_pdf.php?status=<?= $_GET['status_filter'] ?? '' ?>" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </form>
                </div>
                <div class="card-body table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Nama Pekerjaan</th>
                                <th>Tempat</th>
                                <th>Telepon</th>
                                <th>Pendidikan</th>
                                <th>Status</th>
                                <th>Tanggal Lamar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $filter = "";
                            if (!empty($_GET['status_filter'])) {
                                $value = mysqli_real_escape_string($conn, $_GET['status_filter']);

                                if ($value === 'ditolak') {
                                    // ambil semua status yg mengandung kata "ditolak"
                                    $filter = "AND a.status LIKE '%ditolak%'";
                                } else {
                                    // normal
                                    $filter = "AND a.status = '$value'";
                                }
                            }

                            $sql = "SELECT u.full_name AS nama, 
                                           l.title AS nama_pekerjaan, 
                                           l.location AS tempat, 
                                           a.no_telepon,
                                           jenjang.nama_jenjang,
                                           jurusan.nama_jurusan,
                                           a.status, 
                                           a.applied_at AS tanggal_lamar
                                    FROM applications a
                                    JOIN users u ON u.user_id = a.user_id
                                    JOIN lowongan l ON l.job_id = a.job_id
                                    LEFT JOIN jenjang_pendidikan jenjang ON a.id_jenjang_pendidikan = jenjang.id_jenjang
                                    LEFT JOIN jurusan_pendidikan jurusan ON a.id_jurusan_pendidikan = jurusan.id_jurusan
                                    WHERE l.posted_by=$user_id $filter
                                    ORDER BY a.applied_at DESC";

                            $result = mysqli_query($conn, $sql);

                            if ($result && mysqli_num_rows($result) > 0) {
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $telepon = !empty($row['no_telepon']) ? htmlspecialchars($row['no_telepon']) : '-';

                                    $pendidikan = '-';
                                    if (!empty($row['nama_jenjang'])) {
                                        $pendidikan = htmlspecialchars($row['nama_jenjang']);
                                        if (!empty($row['nama_jurusan'])) {
                                            $pendidikan .= ' - ' . htmlspecialchars($row['nama_jurusan']);
                                        }
                                    }

                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>" . htmlspecialchars($row['nama']) . "</td>
                                            <td>" . htmlspecialchars($row['nama_pekerjaan']) . "</td>
                                            <td>" . htmlspecialchars($row['tempat']) . "</td>
                                            <td>{$telepon}</td>
                                            <td>{$pendidikan}</td>
                                            <td><span class='badge badge-" . htmlspecialchars($row['status']) . "'>" . htmlspecialchars($row['status']) . "</span></td>
                                            <td>" . date('d/m/Y H:i', strtotime($row['tanggal_lamar'])) . "</td>
                                          </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr>
                                        <td colspan='8' class='text-center'>Tidak ada data untuk filter ini</td>
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
    </script>
</body>

</html>