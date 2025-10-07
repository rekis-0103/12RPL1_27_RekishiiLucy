<?php
session_start();
require_once 'connect/koneksi.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? (int)$_SESSION['user_id'] : null;
$userRole = $isLoggedIn ? $_SESSION['role'] : null;

// Get job ID
$jobId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($jobId === 0) {
    header('Location: bergabung.php?no_popup=1');
    exit;
}

// Fetch job details
$jobQuery = mysqli_query($conn, "SELECT * FROM lowongan WHERE job_id = $jobId AND hapus = 0");

if (!$jobQuery || mysqli_num_rows($jobQuery) === 0) {
    header('Location: bergabung.php?no_popup=1');
    exit;
}

$job = mysqli_fetch_assoc($jobQuery);

// Check if user has already applied
$hasApplied = false;
if ($isLoggedIn) {
    $checkQuery = mysqli_query($conn, "SELECT application_id FROM applications WHERE job_id = $jobId AND user_id = $userId");
    $hasApplied = $checkQuery && mysqli_num_rows($checkQuery) > 0;
}

// Handle form submission
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn && $userRole === 'pelamar' && $job['status'] === 'open' && !$hasApplied) {
    $noTelepon = isset($_POST['no_telepon']) ? trim($_POST['no_telepon']) : '';
    $pendidikanJenjang = isset($_POST['pendidikan_jenjang']) ? trim($_POST['pendidikan_jenjang']) : '';
    $pendidikanJurusan = isset($_POST['pendidikan_jurusan']) ? trim($_POST['pendidikan_jurusan']) : '';
    $pendidikanSarjana = isset($_POST['pendidikan_sarjana']) ? trim($_POST['pendidikan_sarjana']) : '';
    
    // Validation
    if (empty($noTelepon)) {
        $errorMessage = 'Nomor telepon harus diisi!';
    } elseif (!is_numeric($noTelepon)) {
        $errorMessage = 'Nomor telepon harus berupa angka!';
    } elseif (empty($pendidikanJenjang)) {
        $errorMessage = 'Pendidikan terakhir harus dipilih!';
    } else {
        // Build pendidikan string based on jenjang
        $pendidikanFinal = '';
        if ($pendidikanJenjang === 'SMA') {
            $pendidikanFinal = 'SMA';
        } elseif ($pendidikanJenjang === 'SMK') {
            if (empty($pendidikanJurusan)) {
                $errorMessage = 'Jurusan SMK harus diisi!';
            } else {
                $pendidikanFinal = 'SMK - ' . $pendidikanJurusan;
            }
        } elseif ($pendidikanJenjang === 'Kuliah') {
            if (empty($pendidikanSarjana) || empty($pendidikanJurusan)) {
                $errorMessage = 'Jenjang sarjana dan jurusan harus diisi!';
            } else {
                $pendidikanFinal = 'Kuliah ' . $pendidikanSarjana . ' - ' . $pendidikanJurusan;
            }
        }
        
        // If no validation errors, proceed with file upload
        if (empty($errorMessage)) {
            // Handle CV upload
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $allowedExtensions = ['pdf', 'doc', 'docx'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB
                
                $fileName = $_FILES['cv']['name'];
                $fileSize = $_FILES['cv']['size'];
                $fileTmpName = $_FILES['cv']['tmp_name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    $errorMessage = 'Format file CV tidak valid. Gunakan PDF, DOC, atau DOCX.';
                } elseif ($fileSize > $maxFileSize) {
                    $errorMessage = 'Ukuran file CV terlalu besar. Maksimal 5MB.';
                } else {
                    // Generate unique filename
                    $newFileName = 'cv_' . $userId . '_' . $jobId . '_' . time() . '.' . $fileExtension;
                    $uploadPath = 'uploads/cv/' . $newFileName;
                    
                    // Create directory if not exists
                    if (!is_dir('uploads/cv/')) {
                        mkdir('uploads/cv/', 0755, true);
                    }
                    
                    if (move_uploaded_file($fileTmpName, $uploadPath)) {
                        // Escape inputs
                        $noTeleponEsc = mysqli_real_escape_string($conn, $noTelepon);
                        $pendidikanEsc = mysqli_real_escape_string($conn, $pendidikanFinal);
                        $cvEsc = mysqli_real_escape_string($conn, $uploadPath);
                        
                        // Insert application
                        $insertQuery = "INSERT INTO applications (job_id, user_id, no_telepon, pendidikan, cv, status, applied_at) 
                                       VALUES ($jobId, $userId, '$noTeleponEsc', '$pendidikanEsc', '$cvEsc', 'pending', NOW())";
                        
                        if (mysqli_query($conn, $insertQuery)) {
                            $successMessage = 'Lamaran berhasil dikirim!';
                            $hasApplied = true;
                        } else {
                            $errorMessage = 'Gagal mengirim lamaran. Silakan coba lagi.';
                            unlink($uploadPath); // Delete uploaded file
                        }
                    } else {
                        $errorMessage = 'Gagal mengupload file CV.';
                    }
                }
            } else {
                $errorMessage = 'File CV harus diupload!';
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
    <title><?php echo htmlspecialchars($job['title']); ?> - PT Waindo Specterra</title>
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
            <div class="container job-detail-container">
                <div class="header-with-back">
                    <a href="bergabung.php?no_popup=1" class="back-button">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Lowongan
                    </a>
                </div>

                <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                <?php endif; ?>

                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                <?php endif; ?>

                <div class="job-detail-card">
                    <!-- Job Header -->
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

                    <!-- Job Info Grid -->
                    <div class="job-detail-info">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-map-marker-alt"></i> Lokasi
                                </div>
                                <div class="info-value"><?php echo htmlspecialchars($job['location'] ?: '-'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-money-bill-wave"></i> Gaji
                                </div>
                                <div class="info-value"><?php echo htmlspecialchars($job['salary_range'] ?: '-'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-calendar-alt"></i> Diposting
                                </div>
                                <div class="info-value"><?php echo date('d M Y', strtotime($job['posted_at'])); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="job-detail-section">
                        <h3><i class="fas fa-file-alt"></i> Deskripsi Pekerjaan</h3>
                        <div class="job-description">
                            <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                        </div>
                    </div>

                    <!-- Job Requirements -->
                    <div class="job-detail-section">
                        <h3><i class="fas fa-list-check"></i> Persyaratan</h3>
                        <div class="job-requirements">
                            <?php echo nl2br(htmlspecialchars($job['requirements'])); ?>
                        </div>
                    </div>

                    <!-- Application Section -->
                    <div class="application-section">
                        <?php if (!$isLoggedIn): ?>
                            <!-- Not Logged In -->
                            <div class="login-required-section">
                                <div class="login-notice">
                                    <i class="fas fa-info-circle"></i>
                                    <div class="login-text">
                                        <h4>Login Diperlukan</h4>
                                        <p>Silakan login terlebih dahulu untuk melamar pekerjaan ini.</p>
                                    </div>
                                </div>
                                <div class="login-actions">
                                    <a href="login.php?redirect=detail-lowongan-public.php?id=<?php echo $jobId; ?>" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt"></i> Login
                                    </a>
                                    <a href="register.php" class="btn btn-secondary">
                                        <i class="fas fa-user-plus"></i> Daftar Akun Baru
                                    </a>
                                </div>
                            </div>
                        <?php elseif ($userRole !== 'pelamar'): ?>
                            <!-- Not a Job Seeker -->
                            <div class="role-restricted-section">
                                <div class="role-notice">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <div class="role-text">
                                        <h4>Akses Terbatas</h4>
                                        <p>Hanya akun pelamar yang dapat melamar pekerjaan.</p>
                                        <p>Anda saat ini login sebagai: <strong><?php echo ucfirst($userRole); ?></strong></p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($job['status'] === 'closed'): ?>
                            <!-- Job Closed -->
                            <div class="job-closed-section">
                                <div class="closed-notice">
                                    <i class="fas fa-times-circle"></i>
                                    <div class="closed-text">
                                        <h4>Lowongan Ditutup</h4>
                                        <p>Maaf, lowongan ini sudah tidak menerima lamaran baru.</p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($hasApplied): ?>
                            <!-- Already Applied -->
                            <div class="already-applied">
                                <div class="applied-notice">
                                    <i class="fas fa-check-circle"></i>
                                    <div class="applied-text">
                                        <h4>Sudah Melamar</h4>
                                        <p>Anda sudah mengirimkan lamaran untuk posisi ini.</p>
                                    </div>
                                </div>
                                <div class="applied-actions">
                                    <a href="pelamar/applications.php" class="btn btn-info">
                                        <i class="fas fa-history"></i> Lihat Riwayat Lamaran
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Application Form -->
                            <div class="apply-section">
                                <div class="apply-header">
                                    <h3><i class="fas fa-paper-plane"></i> Kirim Lamaran</h3>
                                </div>
                                <div class="apply-form-container">
                                    <form method="POST" enctype="multipart/form-data" class="apply-form-detail" id="applicationForm">
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
                                            <label for="cv">
                                                <i class="fas fa-file-upload"></i> Upload CV <span style="color: red;">*</span>
                                            </label>
                                            <input type="file" 
                                                   id="cv" 
                                                   name="cv" 
                                                   accept=".pdf,.doc,.docx" 
                                                   required>
                                            <small class="form-text">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
                                        </div>

                                        <!-- Form Actions -->
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-paper-plane"></i> Kirim Lamaran
                                            </button>
                                            <a href="bergabung.php?no_popup=1" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Batal
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
    <script>
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
                // Let's create it dynamically
                if (!document.getElementById('hidden_kuliah_jurusan')) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.id = 'hidden_kuliah_jurusan';
                    hiddenInput.name = 'pendidikan_jurusan';
                    document.getElementById('applicationForm').appendChild(hiddenInput);
                }
            } else if (jenjang === 'SMA') {
                // SMA doesn't need any additional fields
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
    </script>
</body>
</html>