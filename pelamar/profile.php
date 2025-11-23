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

function logActivity($conn, $actor_user_id, $action)
{
    $actor_user_id = (int)$actor_user_id;
    $action = mysqli_real_escape_string($conn, $action);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '::1';
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($actor_user_id, '$action')");
}

// Handle CV view logging via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_cv_view'])) {
    header('Content-Type: application/json');
    $cv_query = mysqli_query($conn, "SELECT cv_filename FROM users WHERE user_id = $user_id AND hapus = 0");
    $cv_data = mysqli_fetch_assoc($cv_query);
    $cv_filename = $cv_data ? $cv_data['cv_filename'] : 'unknown';
    echo json_encode(['success' => true]);
    exit();
}

// Fetch current profile with education info
$profile_q = mysqli_query($conn, "
    SELECT u.*, 
           jnj.nama_jenjang, jnj.kode_jenjang, jnj.punya_jurusan,
           jr.nama_jurusan
    FROM users u
    LEFT JOIN jenjang_pendidikan jnj ON u.id_jenjang_pendidikan = jnj.id_jenjang
    LEFT JOIN jurusan_pendidikan jr ON u.id_jurusan_pendidikan = jr.id_jurusan
    WHERE u.user_id=$user_id AND u.hapus=0 
    LIMIT 1
");
$profile = $profile_q ? mysqli_fetch_assoc($profile_q) : null;

// Get all education levels for dropdown
$levels_query = "SELECT * FROM jenjang_pendidikan WHERE status = 1 ORDER BY id_jenjang";
$levels_result = mysqli_query($conn, $levels_query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['log_cv_view'])) {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $new_id_jenjang = !empty($_POST['id_jenjang_pendidikan']) ? (int)$_POST['id_jenjang_pendidikan'] : null;
    $new_id_jurusan = !empty($_POST['id_jurusan_pendidikan']) ? (int)$_POST['id_jurusan_pendidikan'] : null;
    $new_password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Track changes for detailed logging
    $changes = [];
    
    // Check unique username
    $chk_username = mysqli_query($conn, "SELECT user_id FROM users WHERE username='$new_username' AND user_id<>$user_id AND hapus=0 LIMIT 1");
    if ($chk_username && mysqli_num_rows($chk_username) > 0) {
        $error = 'Username sudah digunakan.';
        logActivity($conn, $user_id, 'Gagal update profil (username sudah digunakan)');
    } else {
        // Check unique email
        $chk_email = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$new_email' AND user_id<>$user_id AND hapus=0 LIMIT 1");
        if ($chk_email && mysqli_num_rows($chk_email) > 0) {
            $error = 'Email sudah digunakan.';
            logActivity($conn, $user_id, 'Gagal update profil (email sudah digunakan)');
        } else {
            // Track field changes
            if ($profile['username'] != $new_username) {
                $changes[] = "Username: '{$profile['username']}' → '$new_username'";
            }
            if ($profile['full_name'] != $new_full_name) {
                $changes[] = "Nama: '{$profile['full_name']}' → '$new_full_name'";
            }
            if ($profile['email'] != $new_email) {
                $changes[] = "Email: '{$profile['email']}' → '$new_email'";
            }
            if ($profile['no_telepon'] != $new_no_telepon) {
                $old_telp = $profile['no_telepon'] ?: 'belum diisi';
                $changes[] = "No. Telepon: '$old_telp' → '$new_no_telepon'";
            }
            
            // Track education changes
            if ($profile['id_jenjang_pendidikan'] != $new_id_jenjang) {
                $old_jenjang = $profile['nama_jenjang'] ?: 'belum diisi';
                $new_jenjang_q = mysqli_query($conn, "SELECT nama_jenjang FROM jenjang_pendidikan WHERE id_jenjang=$new_id_jenjang");
                $new_jenjang_data = mysqli_fetch_assoc($new_jenjang_q);
                $new_jenjang_name = $new_jenjang_data ? $new_jenjang_data['nama_jenjang'] : 'tidak dipilih';
                $changes[] = "Jenjang Pendidikan: '$old_jenjang' → '$new_jenjang_name'";
            }
            
            if ($profile['id_jurusan_pendidikan'] != $new_id_jurusan) {
                $old_jurusan = $profile['nama_jurusan'] ?: 'belum diisi';
                if ($new_id_jurusan) {
                    $new_jurusan_q = mysqli_query($conn, "SELECT nama_jurusan FROM jurusan_pendidikan WHERE id_jurusan=$new_id_jurusan");
                    $new_jurusan_data = mysqli_fetch_assoc($new_jurusan_q);
                    $new_jurusan_name = $new_jurusan_data ? $new_jurusan_data['nama_jurusan'] : 'tidak dipilih';
                } else {
                    $new_jurusan_name = 'tidak ada jurusan';
                }
                $changes[] = "Jurusan: '$old_jurusan' → '$new_jurusan_name'";
            }

            // Handle CV upload
            $cv_update = '';
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['cv'];
                $allowed_types = ['application/pdf'];
                $max_size = 5 * 1024 * 1024; // 5MB

                if (!in_array($file['type'], $allowed_types)) {
                    $error = 'File harus berformat PDF.';
                    logActivity($conn, $user_id, 'Gagal upload CV (format tidak valid)');
                } elseif ($file['size'] > $max_size) {
                    $error = 'Ukuran file maksimal 5MB.';
                    logActivity($conn, $user_id, 'Gagal upload CV (ukuran terlalu besar)');
                } else {
                    // Create unique filename
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $new_filename = 'cv_' . $user_id . '_' . time() . '.' . $extension;
                    $upload_path = 'cv/' . $new_filename;

                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        // Delete old CV if exists
                        if (!empty($profile['cv_filename']) && file_exists('cv/' . $profile['cv_filename'])) {
                            unlink('cv/' . $profile['cv_filename']);
                            $changes[] = "CV: '{$profile['cv_filename']}' → '$new_filename'";
                        } else {
                            $changes[] = "CV: Upload CV baru '$new_filename'";
                        }
                        $cv_update = ", cv_filename='$new_filename'";
                    } else {
                        $error = 'Gagal mengupload CV.';
                        logActivity($conn, $user_id, 'Gagal upload CV (error saat memindahkan file)');
                    }
                }
            }

            // Track password change
            if ($new_password !== '') {
                $changes[] = "Password diubah";
            }

            if (!isset($error)) {
                // Build password update
                $set_pass = '';
                if ($new_password !== '') {
                    $hash = md5($new_password);
                    $set_pass = ", password='$hash'";
                }

                // Build education update
                $edu_update = '';
                if ($new_id_jenjang !== null) {
                    $edu_update = ", id_jenjang_pendidikan=$new_id_jenjang";
                    if ($new_id_jurusan !== null) {
                        $edu_update .= ", id_jurusan_pendidikan=$new_id_jurusan";
                    } else {
                        $edu_update .= ", id_jurusan_pendidikan=NULL";
                    }
                } else {
                    $edu_update = ", id_jenjang_pendidikan=NULL, id_jurusan_pendidikan=NULL";
                }

                $upd = mysqli_query($conn, "
                    UPDATE users SET 
                        username='$new_username',
                        full_name='$new_full_name', 
                        email='$new_email',
                        no_telepon='$new_no_telepon'
                        $edu_update
                        $cv_update
                        $set_pass 
                    WHERE user_id=$user_id
                ");

                if ($upd) {
                    $success = 'Profil berhasil diperbarui';
                    $_SESSION['username'] = $new_username;
                    $_SESSION['full_name'] = $new_full_name;
                    
                    // Log with detailed changes
                    if (!empty($changes)) {
                        $change_log = implode(', ', $changes);
                        logActivity($conn, $user_id, "Update profil: $change_log");
                    } else {
                        logActivity($conn, $user_id, 'Update profil (tidak ada perubahan)');
                    }
                    
                    // Refresh loaded profile
                    $profile_q = mysqli_query($conn, "
                        SELECT u.*, 
                               jnj.nama_jenjang, jnj.kode_jenjang, jnj.punya_jurusan,
                               jr.nama_jurusan
                        FROM users u
                        LEFT JOIN jenjang_pendidikan jnj ON u.id_jenjang_pendidikan = jnj.id_jenjang
                        LEFT JOIN jurusan_pendidikan jr ON u.id_jurusan_pendidikan = jr.id_jurusan
                        WHERE u.user_id=$user_id AND u.hapus=0 
                        LIMIT 1
                    ");
                    $profile = $profile_q ? mysqli_fetch_assoc($profile_q) : null;
                } else {
                    $error = 'Gagal memperbarui profil';
                    logActivity($conn, $user_id, 'Gagal update profil (database error)');
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
    <title>Profil - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/pofile.css">
</head>

<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Profil</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="profile.php" class="active"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="lowongan.php"><i class="fas fa-briefcase"></i> Lihat Lowongan</a></li>
                <li><a href="applications.php"><i class="fas fa-file-alt"></i> Lamaran Saya</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Profil</h1>
                <p>Perbarui informasi akun Anda</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($profile['cv_filename']) && file_exists('cv/' . $profile['cv_filename'])): ?>
            <div class="card cv-card">
                <div class="card-header">
                    <h3><i class="fas fa-file-pdf"></i> CV Terdaftar</h3>
                </div>
                <div class="card-body">
                    <div class="cv-info">
                        <div class="cv-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="cv-details">
                            <h4><?php echo htmlspecialchars($profile['cv_filename']); ?></h4>
                            <p class="cv-meta">
                                <i class="fas fa-calendar"></i> 
                                Diupload: <?php echo date('d M Y', filemtime('cv/' . $profile['cv_filename'])); ?>
                                <span class="separator">•</span>
                                <i class="fas fa-hdd"></i>
                                Ukuran: <?php echo number_format(filesize('cv/' . $profile['cv_filename']) / 1024, 2); ?> KB
                            </p>
                        </div>
                        <div class="cv-actions">
                            <a href="cv/<?php echo htmlspecialchars($profile['cv_filename']); ?>" 
                               class="btn btn-view" 
                               target="_blank"
                               onclick="logCVView()">
                                <i class="fas fa-eye"></i> Lihat CV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user-edit"></i> Edit Profil</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Username <span class="required">*</span></label>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($profile['username'] ?? $username); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>No. Telepon <span class="required">*</span></label>
                                <input type="text" name="no_telepon" value="<?php echo htmlspecialchars($profile['no_telepon'] ?? ''); ?>" required placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="form-group">
                                <label>Jenjang Pendidikan <span class="required">*</span></label>
                                <select name="id_jenjang_pendidikan" id="jenjang_select" required onchange="loadJurusan(this.value)">
                                    <option value="">-- Pilih Jenjang --</option>
                                    <?php 
                                    mysqli_data_seek($levels_result, 0);
                                    while ($level = mysqli_fetch_assoc($levels_result)): 
                                    ?>
                                    <option value="<?php echo $level['id_jenjang']; ?>" 
                                            data-punya-jurusan="<?php echo $level['punya_jurusan']; ?>"
                                            <?php echo ($profile['id_jenjang_pendidikan'] == $level['id_jenjang']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($level['nama_jenjang']); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group" id="jurusan_group" style="display: none;">
                                <label>Jurusan Pendidikan <span class="required">*</span></label>
                                <select name="id_jurusan_pendidikan" id="jurusan_select">
                                    <option value="">-- Pilih Jurusan --</option>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label>
                                    <i class="fas fa-file-pdf"></i> Upload CV Baru (PDF, Max 5MB)
                                </label>
                                <input type="file" name="cv" accept=".pdf">
                                <small class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    <?php if (!empty($profile['cv_filename'])): ?>
                                        Upload file baru akan mengganti CV yang sudah ada
                                    <?php else: ?>
                                        Silakan upload CV Anda dalam format PDF
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-lock"></i> Password Baru</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                                <small class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Minimal 6 karakter, gunakan kombinasi huruf dan angka
                                </small>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </form>
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

        // Log CV view activity
        function logCVView() {
            fetch('profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'log_cv_view=1'
            });
        }

        // Load jurusan based on jenjang
        async function loadJurusan(idJenjang) {
            const jurusanGroup = document.getElementById('jurusan_group');
            const jurusanSelect = document.getElementById('jurusan_select');
            const selectedOption = document.querySelector('#jenjang_select option:checked');
            const punyaJurusan = selectedOption ? selectedOption.dataset.punyaJurusan : '0';

            if (punyaJurusan === '1' && idJenjang) {
                jurusanGroup.style.display = 'block';
                jurusanSelect.required = true;

                // Fetch jurusan via AJAX
                try {
                    const formData = new FormData();
                    formData.append('get_jurusan', '1');
                    formData.append('id_jenjang', idJenjang);

                    const response = await fetch('get_jurusan.php', {
                        method: 'POST',
                        body: formData
                    });

                    const jurusanList = await response.json();
                    
                    jurusanSelect.innerHTML = '<option value="">-- Pilih Jurusan --</option>';
                    jurusanList.forEach(jurusan => {
                        const option = document.createElement('option');
                        option.value = jurusan.id_jurusan;
                        option.textContent = jurusan.nama_jurusan;
                        
                        // Keep selected jurusan if editing
                        <?php if (!empty($profile['id_jurusan_pendidikan'])): ?>
                        if (jurusan.id_jurusan == <?php echo $profile['id_jurusan_pendidikan']; ?>) {
                            option.selected = true;
                        }
                        <?php endif; ?>
                        
                        jurusanSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error loading jurusan:', error);
                }
            } else {
                jurusanGroup.style.display = 'none';
                jurusanSelect.required = false;
                jurusanSelect.value = '';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const jenjangSelect = document.getElementById('jenjang_select');
            if (jenjangSelect.value) {
                loadJurusan(jenjangSelect.value);
            }
        });
    </script>
</body>

</html>