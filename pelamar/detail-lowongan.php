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

// Get job ID from URL
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$show_apply_form = isset($_GET['apply']) && $_GET['apply'] == 1;

if (!$job_id) {
    header('Location: lowongan.php');
    exit();
}

// Get job details
$query = "SELECT * FROM lowongan WHERE job_id = $job_id AND hapus = 0";
$result = mysqli_query($conn, $query);
$job = mysqli_fetch_assoc($result);

if (!$job) {
    header('Location: lowongan.php');
    exit();
}

// Check if user has already applied for this job
$checkApplication = mysqli_query($conn, "SELECT * FROM applications WHERE job_id = $job_id AND user_id = $user_id");
$hasApplied = mysqli_num_rows($checkApplication) > 0;

// Handle apply action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply') {
    // Check if job is still open
    if ($job['status'] !== 'open') {
        $error = 'Lowongan sudah ditutup, tidak dapat mengirim lamaran.';
        logActivity($conn, $user_id, 'Gagal kirim lamaran (lowongan sudah ditutup)');
    } elseif ($hasApplied) {
        $error = 'Anda sudah mengirim lamaran untuk lowongan ini sebelumnya.';
        logActivity($conn, $user_id, 'Gagal kirim lamaran (sudah apply)');
    } else {
        $noTelepon = isset($_POST['no_telepon']) ? trim($_POST['no_telepon']) : '';
        $pendidikanJenjang = isset($_POST['pendidikan_jenjang']) ? trim($_POST['pendidikan_jenjang']) : '';
        $pendidikanJurusan = isset($_POST['pendidikan_jurusan']) ? trim($_POST['pendidikan_jurusan']) : '';
        $pendidikanSarjana = isset($_POST['pendidikan_sarjana']) ? trim($_POST['pendidikan_sarjana']) : '';
        
        // Validation
        if (empty($noTelepon)) {
            $error = 'Nomor telepon harus diisi!';
        } elseif (!is_numeric($noTelepon)) {
            $error = 'Nomor telepon harus berupa angka!';
        } elseif (empty($pendidikanJenjang)) {
            $error = 'Pendidikan terakhir harus dipilih!';
        } else {
            // Build pendidikan string based on jenjang
            $pendidikanFinal = '';
            if ($pendidikanJenjang === 'SMA') {
                $pendidikanFinal = 'SMA';
            } elseif ($pendidikanJenjang === 'SMK') {
                if (empty($pendidikanJurusan)) {
                    $error = 'Jurusan SMK harus diisi!';
                } else {
                    $pendidikanFinal = 'SMK - ' . $pendidikanJurusan;
                }
            } elseif ($pendidikanJenjang === 'Kuliah') {
                if (empty($pendidikanSarjana) || empty($pendidikanJurusan)) {
                    $error = 'Jenjang sarjana dan jurusan harus diisi!';
                } else {
                    $pendidikanFinal = 'Kuliah ' . $pendidikanSarjana . ' - ' . $pendidikanJurusan;
                }
            }
            
            // If no validation errors, proceed with file upload
            if (empty($error)) {
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
                        // Escape inputs
                        $noTeleponEsc = mysqli_real_escape_string($conn, $noTelepon);
                        $pendidikanEsc = mysqli_real_escape_string($conn, $pendidikanFinal);
                        $cvEsc = mysqli_real_escape_string($conn, $relPath);
                        
                        $q = "INSERT INTO applications (job_id, user_id, no_telepon, pendidikan, cv, status, applied_at) 
                             VALUES ($job_id, $user_id, '$noTeleponEsc', '$pendidikanEsc', '$cvEsc', 'pending', NOW())";
                        
                        if (mysqli_query($conn, $q)) {
                            $success = 'Lamaran berhasil dikirim';
                            logActivity($conn, $user_id, "Kirim lamaran (job #$job_id)");
                            $hasApplied = true;
                            // Refresh job data
                            $result = mysqli_query($conn, $query);
                            $job = mysqli_fetch_assoc($result);
                        } else {
                            $error = 'Gagal mengirim lamaran';
                            logActivity($conn, $user_id, 'Gagal kirim lamaran (database error)');
                            unlink($targetPath); // Delete uploaded file
                        }
                    } else {
                        $error = 'Gagal menyimpan file CV';
                        logActivity($conn, $user_id, 'Gagal kirim lamaran (simpan CV gagal)');
                    }
                }
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
    <title>Detail Lowongan - <?php echo htmlspecialchars($job['title']); ?></title>
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
                <h3>Detail Lowongan</h3>
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
                <div class="header-with-back">
                    <a href="lowongan.php" class="back-button">
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
                        <?php if ($hasApplied): ?>
                            <div class="already-applied">
                                <div class="applied-notice">
                                    <i class="fas fa-check-circle"></i>
                                    <div class="applied-text">
                                        <h4>Lamaran Sudah Dikirim</h4>
                                        <p>Anda sudah mengirim lamaran untuk lowongan ini. Silakan cek status lamaran di menu "Lamaran Saya".</p>
                                    </div>
                                </div>
                                <div class="applied-actions">
                                    <a href="applications.php" class="btn btn-info">
                                        <i class="fas fa-file-alt"></i> Lihat Lamaran Saya
                                    </a>
                                </div>
                            </div>
                        <?php elseif ($job['status'] === 'open'): ?>
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
                                    <form method="POST" enctype="multipart/form-data" class="apply-form-detail" id="applicationForm">
                                        <input type="hidden" name="action" value="apply">
                                        
                                        <!-- Phone Number -->
                                        <div class="form-group">
                                            <label for="no_telepon">
                                                <i class="fas fa-phone"></i> Nomor Telepon <span style="color: red;">*</span>
                                            </label>
                                            <input type="number" 
                                                   id="no_telepon" 
                                                   name="no_telepon" 
                                                   class="form-control" 
                                                   placeholder="Contoh: 081234567890"
                                                   required>
                                            <small class="form-text">Masukkan nomor telepon yang dapat dihubungi.</small>
                                        </div>

                                        <!-- Education Level -->
                                        <div class="form-group">
                                            <label for="pendidikan_jenjang">
                                                <i class="fas fa-graduation-cap"></i> Pendidikan Terakhir <span style="color: red;">*</span>
                                            </label>
                                            <select id="pendidikan_jenjang" 
                                                    name="pendidikan_jenjang" 
                                                    class="form-control" 
                                                    required
                                                    onchange="handleEducationChange()">
                                                <option value="">-- Pilih Jenjang Pendidikan --</option>
                                                <option value="SMA">SMA</option>
                                                <option value="SMK">SMK</option>
                                                <option value="Kuliah">Kuliah</option>
                                            </select>
                                        </div>

                                        <!-- SMK Major (Hidden by default) -->
                                        <div class="form-group" id="smk_jurusan_group" style="display: none;">
                                            <label for="smk_jurusan">
                                                <i class="fas fa-book"></i> Jurusan SMK <span style="color: red;">*</span>
                                            </label>
                                            <input type="text" 
                                                   id="smk_jurusan" 
                                                   name="pendidikan_jurusan" 
                                                   class="form-control" 
                                                   placeholder="Contoh: Teknik Komputer Jaringan">
                                        </div>

                                        <!-- University Degree Level (Hidden by default) -->
                                        <div class="form-group" id="sarjana_group" style="display: none;">
                                            <label for="pendidikan_sarjana">
                                                <i class="fas fa-user-graduate"></i> Jenjang Sarjana <span style="color: red;">*</span>
                                            </label>
                                            <select id="pendidikan_sarjana" 
                                                    name="pendidikan_sarjana" 
                                                    class="form-control">
                                                <option value="">-- Pilih Jenjang Sarjana --</option>
                                                <option value="D3">D3 (Diploma 3)</option>
                                                <option value="D4">D4 (Diploma 4)</option>
                                                <option value="S1">S1 (Sarjana)</option>
                                                <option value="S2">S2 (Magister)</option>
                                                <option value="S3">S3 (Doktor)</option>
                                            </select>
                                        </div>

                                        <!-- University Major (Hidden by default) -->
                                        <div class="form-group" id="kuliah_jurusan_group" style="display: none;">
                                            <label for="kuliah_jurusan">
                                                <i class="fas fa-book-open"></i> Jurusan / Program Studi <span style="color: red;">*</span>
                                            </label>
                                            <input type="text" 
                                                   id="kuliah_jurusan" 
                                                   class="form-control" 
                                                   placeholder="Contoh: Teknik Informatika">
                                        </div>
                                        
                                        <!-- CV Upload -->
                                        <div class="form-group">
                                            <label for="cv"><i class="fas fa-file-pdf"></i> Upload CV (PDF/DOC/DOCX) <span style="color: red;">*</span></label>
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
    </div>

    <script src="../js/navbar.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        
        function showApplyForm() {
            document.getElementById('applyFormContainer').style.display = 'block';
            document.querySelector('.apply-section').classList.add('show-form');
        }
        
        function hideApplyForm() {
            document.getElementById('applyFormContainer').style.display = 'none';
            document.querySelector('.apply-section').classList.remove('show-form');
        }
        
        // Handle Education Level Change
        function handleEducationChange() {
            const jenjang = document.getElementById('pendidikan_jenjang').value;
            const smkJurusanGroup = document.getElementById('smk_jurusan_group');
            const sarjanaGroup = document.getElementById('sarjana_group');
            const kuliahJurusanGroup = document.getElementById('kuliah_jurusan_group');
            
            const smkJurusan = document.getElementById('smk_jurusan');
            const sarjanaSelect = document.getElementById('pendidikan_sarjana');
            const kuliahJurusan = document.getElementById('kuliah_jurusan');
            
            // Hide all conditional fields first
            smkJurusanGroup.style.display = 'none';
            sarjanaGroup.style.display = 'none';
            kuliahJurusanGroup.style.display = 'none';
            
            // Remove required attributes
            smkJurusan.removeAttribute('required');
            sarjanaSelect.removeAttribute('required');
            kuliahJurusan.removeAttribute('required');
            
            // Clear values
            smkJurusan.value = '';
            sarjanaSelect.value = '';
            kuliahJurusan.value = '';
            
            // Show relevant fields based on selection
            if (jenjang === 'SMK') {
                smkJurusanGroup.style.display = 'block';
                smkJurusan.setAttribute('required', 'required');
            } else if (jenjang === 'Kuliah') {
                sarjanaGroup.style.display = 'block';
                kuliahJurusanGroup.style.display = 'block';
                sarjanaSelect.setAttribute('required', 'required');
                kuliahJurusan.setAttribute('required', 'required');
                
                // For college, we need to use a hidden input to pass the jurusan
                if (!document.getElementById('hidden_kuliah_jurusan')) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.id = 'hidden_kuliah_jurusan';
                    hiddenInput.name = 'pendidikan_jurusan';
                    document.getElementById('applicationForm').appendChild(hiddenInput);
                }
            }
        }
        
        // Update hidden input when kuliah jurusan changes
        document.addEventListener('DOMContentLoaded', function() {
            const kuliahJurusan = document.getElementById('kuliah_jurusan');
            if (kuliahJurusan) {
                kuliahJurusan.addEventListener('input', function() {
                    const hiddenInput = document.getElementById('hidden_kuliah_jurusan');
                    if (hiddenInput) {
                        hiddenInput.value = this.value;
                    }
                });
            }
        });
        
        // Form validation before submit
        document.getElementById('applicationForm').addEventListener('submit', function(e) {
            const jenjang = document.getElementById('pendidikan_jenjang').value;
            
            if (jenjang === 'SMK') {
                const jurusan = document.getElementById('smk_jurusan').value.trim();
                if (!jurusan) {
                    e.preventDefault();
                    alert('Jurusan SMK harus diisi!');
                    return false;
                }
            } else if (jenjang === 'Kuliah') {
                const sarjana = document.getElementById('pendidikan_sarjana').value;
                const jurusan = document.getElementById('kuliah_jurusan').value.trim();
                
                if (!sarjana) {
                    e.preventDefault();
                    alert('Jenjang Sarjana harus dipilih!');
                    return false;
                }
                if (!jurusan) {
                    e.preventDefault();
                    alert('Jurusan harus diisi!');
                    return false;
                }
                
                // Update hidden input
                const hiddenInput = document.getElementById('hidden_kuliah_jurusan');
                if (hiddenInput) {
                    hiddenInput.value = jurusan;
                }
            }
            
            return true;
        });
        
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