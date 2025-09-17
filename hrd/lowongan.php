<?php
session_start();
require_once '../connect/koneksi.php';

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

// Helpers
function esc($conn, $str)
{
    return mysqli_real_escape_string($conn, $str);
}
function logActivity($conn, $actor_user_id, $action)
{
    $actor_user_id = (int)$actor_user_id;
    $action = mysqli_real_escape_string($conn, $action);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '::1';
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($actor_user_id, '$action')");
}

// Function to handle image upload
function uploadPopupImage($file, $orientation) {
    $uploadDir = '../uploads/popups/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, PNG, atau GIF.'];
    }
    
    if ($file['size'] > $maxFileSize) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 5MB.'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'popup_' . $orientation . '_' . uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload file.'];
    }
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'add') {
        $title = esc($conn, $_POST['title']);
        $description = esc($conn, $_POST['description']);
        $requirements = esc($conn, $_POST['requirements']);
        $location = esc($conn, $_POST['location']);
        $salary_range = esc($conn, $_POST['salary_range']);
        $status = esc($conn, $_POST['status']);
        $q = "INSERT INTO lowongan (title, description, requirements, location, salary_range, status, posted_by) VALUES ('$title', '$description', '$requirements', '$location', '$salary_range', '$status', $user_id)";
        if (mysqli_query($conn, $q)) {
            $success = 'Lowongan berhasil ditambahkan';
            $new_id = mysqli_insert_id($conn);
            logActivity($conn, $user_id, "HRD: tambah lowongan #$new_id - $title");
        } else {
            $error = 'Gagal menambahkan lowongan';
        }
    } elseif ($action === 'edit') {
        $job_id = (int)$_POST['job_id'];
        $title = esc($conn, $_POST['title']);
        $description = esc($conn, $_POST['description']);
        $requirements = esc($conn, $_POST['requirements']);
        $location = esc($conn, $_POST['location']);
        $salary_range = esc($conn, $_POST['salary_range']);
        $status = esc($conn, $_POST['status']);
        $q = "UPDATE lowongan SET title='$title', description='$description', requirements='$requirements', location='$location', salary_range='$salary_range', status='$status', updated_at=NOW() WHERE job_id=$job_id";
        if (mysqli_query($conn, $q)) {
            $success = 'Lowongan berhasil diubah';
            logActivity($conn, $user_id, "HRD: edit lowongan #$job_id - $title");
        } else {
            $error = 'Gagal mengubah lowongan';
        }
    } elseif ($action === 'toggle') {
        $job_id = (int)$_POST['job_id'];
        $new_status = esc($conn, $_POST['new_status']);
        $q = "UPDATE lowongan SET status='$new_status', updated_at=NOW() WHERE job_id=$job_id";
        if (mysqli_query($conn, $q)) {
            $success = 'Status lowongan diperbarui';
            logActivity($conn, $user_id, "HRD: ubah status lowongan #$job_id -> $new_status");
        } else {
            $error = 'Gagal memperbarui status';
        }
    } elseif ($action === 'delete') {
        $job_id = (int)$_POST['job_id'];
        $q = "UPDATE lowongan SET hapus=1, updated_at=NOW() WHERE job_id=$job_id";
        if (mysqli_query($conn, $q)) {
            $success = 'Lowongan dihapus';
            logActivity($conn, $user_id, "HRD: hapus lowongan #$job_id");
        } else {
            $error = 'Gagal menghapus lowongan';
        }
    } elseif ($action === 'add_popup') {
        $popup_title = esc($conn, $_POST['popup_title']);
        $orientation = esc($conn, $_POST['orientation']);
        
        if (isset($_FILES['popup_image']) && $_FILES['popup_image']['error'] === 0) {
            $uploadResult = uploadPopupImage($_FILES['popup_image'], $orientation);
            
            if ($uploadResult['success']) {
                $filename = $uploadResult['filename'];
                $q = "INSERT INTO popup_images (title, image_filename, orientation, created_by) VALUES ('$popup_title', '$filename', '$orientation', $user_id)";
                if (mysqli_query($conn, $q)) {
                    $success = 'Popup gambar berhasil ditambahkan';
                    $new_popup_id = mysqli_insert_id($conn);
                    logActivity($conn, $user_id, "HRD: tambah popup gambar #$new_popup_id - $popup_title");
                } else {
                    $error = 'Gagal menyimpan data popup ke database';
                    // Delete uploaded file if database insert fails
                    unlink('../uploads/popups/' . $filename);
                }
            } else {
                $error = $uploadResult['message'];
            }
        } else {
            $error = 'Harap pilih gambar untuk diupload';
        }
    } elseif ($action === 'edit_popup') {
        $popup_id = (int)$_POST['popup_id'];
        $popup_title = esc($conn, $_POST['popup_title']);
        $orientation = esc($conn, $_POST['orientation']);
        
        // Get current data
        $currentData = mysqli_query($conn, "SELECT * FROM popup_images WHERE popup_id = $popup_id");
        $current = mysqli_fetch_assoc($currentData);
        
        $filename = $current['image_filename'];
        
        // If new image is uploaded
        if (isset($_FILES['popup_image']) && $_FILES['popup_image']['error'] === 0) {
            $uploadResult = uploadPopupImage($_FILES['popup_image'], $orientation);
            
            if ($uploadResult['success']) {
                // Delete old image
                if (file_exists('../uploads/popups/' . $current['image_filename'])) {
                    unlink('../uploads/popups/' . $current['image_filename']);
                }
                $filename = $uploadResult['filename'];
            } else {
                $error = $uploadResult['message'];
            }
        }
        
        if (!isset($error)) {
            $q = "UPDATE popup_images SET title='$popup_title', image_filename='$filename', orientation='$orientation', updated_at=NOW() WHERE popup_id=$popup_id";
            if (mysqli_query($conn, $q)) {
                $success = 'Popup gambar berhasil diubah';
                logActivity($conn, $user_id, "HRD: edit popup gambar #$popup_id - $popup_title");
            } else {
                $error = 'Gagal mengubah popup gambar';
            }
        }
    } elseif ($action === 'toggle_popup') {
        $popup_id = (int)$_POST['popup_id'];
        $is_active = (int)$_POST['is_active'];
        
        // Allow multiple active popups: toggle only the requested one
        $q = "UPDATE popup_images SET is_active=" . ($is_active ? '1' : '0') . ", updated_at=NOW() WHERE popup_id=$popup_id";
        
        if (mysqli_query($conn, $q)) {
            $success = 'Status popup diperbarui';
            logActivity($conn, $user_id, "HRD: toggle popup gambar #$popup_id -> " . ($is_active ? 'aktif' : 'nonaktif'));
        } else {
            $error = 'Gagal memperbarui status popup';
        }
    } elseif ($action === 'delete_popup') {
        $popup_id = (int)$_POST['popup_id'];
        
        // Get image filename to delete
        $result = mysqli_query($conn, "SELECT image_filename FROM popup_images WHERE popup_id = $popup_id");
        $popup = mysqli_fetch_assoc($result);
        
        if ($popup) {
            // Delete image file
            if (file_exists('../uploads/popups/' . $popup['image_filename'])) {
                unlink('../uploads/popups/' . $popup['image_filename']);
            }
            
            // Delete from database
            $q = "DELETE FROM popup_images WHERE popup_id=$popup_id";
            if (mysqli_query($conn, $q)) {
                $success = 'Popup gambar dihapus';
                logActivity($conn, $user_id, "HRD: hapus popup gambar #$popup_id");
            } else {
                $error = 'Gagal menghapus popup gambar';
            }
        } else {
            $error = 'Popup tidak ditemukan';
        }
    }
}

// Fetch jobs
$list = mysqli_query($conn, "SELECT l.*, u.full_name AS poster 
    FROM lowongan l 
    LEFT JOIN users u ON l.posted_by = u.user_id 
    WHERE l.hapus = 0 AND l.posted_by = $user_id 
    ORDER BY l.posted_at DESC");

// Fetch popup images
$popups = mysqli_query($conn, "SELECT p.*, u.full_name AS creator 
    FROM popup_images p 
    LEFT JOIN users u ON p.created_by = u.user_id 
    ORDER BY p.is_active DESC, p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lowongan - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/lowongan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Kelola Lowongan</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="lowongan.php" class="active"><i class="fas fa-briefcase"></i> Kelola Lowongan</a></li>
                <li><a href="applications.php"><i class="fas fa-file-alt"></i> Kelola Lamaran</a></li>
                <li><a href="candidates.php"><i class="fas fa-users"></i> Kandidat</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Kelola Lowongan</h1>
                <p>Tambah, ubah, dan kelola lowongan</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Popup Image Management Section -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-image"></i> Kelola Popup Gambar</h3>
                </div>
                <form method="POST" enctype="multipart/form-data" class="card-body">
                    <input type="hidden" name="action" value="add_popup">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Judul Popup</label>
                            <input type="text" name="popup_title" required placeholder="Masukkan judul popup">
                        </div>
                        <div class="form-group">
                            <label>Orientasi</label>
                            <select name="orientation" required>
                                <option value="vertical">Vertikal (Portrait)</option>
                                <option value="horizontal">Horizontal (Landscape)</option>
                            </select>
                        </div>
                        <div class="form-group full">
                            <label>Upload Gambar</label>
                            <div class="file-input-wrapper">
                                <input type="file" name="popup_image" id="popup_image" accept="image/*" required>
                                <label for="popup_image" class="file-input-display">
                                    <i class="fas fa-cloud-upload-alt"></i><br>
                                    Klik untuk memilih gambar
                                </label>
                            </div>
                            <div class="file-info">
                                Format: JPG, PNG, GIF | Maksimal: 5MB
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah Popup</button>
                </form>

                <!-- Popup List -->
                <div class="card-body">
                    <h4><i class="fas fa-list"></i> Daftar Popup Gambar</h4>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Preview</th>
                                    <th>Judul</th>
                                    <th>Orientasi</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Oleh</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($popups && mysqli_num_rows($popups) > 0): ?>
                                    <?php while ($popup = mysqli_fetch_assoc($popups)): ?>
                                        <tr>
                                            <td>
                                                <img src="../uploads/popups/<?php echo htmlspecialchars($popup['image_filename']); ?>" 
                                                     alt="Preview" class="image-preview"
                                                     onerror="this.src='../assets/images/no-image.png'">
                                            </td>
                                            <td><?php echo htmlspecialchars($popup['title']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $popup['orientation'] === 'vertical' ? 'info' : 'warning'; ?>">
                                                    <?php echo $popup['orientation'] === 'vertical' ? 'Vertikal' : 'Horizontal'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $popup['is_active'] ? 'success' : 'secondary'; ?>">
                                                    <?php echo $popup['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($popup['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($popup['creator'] ?: '-'); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="openEditPopup(<?php echo (int)$popup['popup_id']; ?>, <?php echo htmlspecialchars(json_encode($popup)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" style="display:inline" onsubmit="return confirm('Hapus popup ini?')">
                                                    <input type="hidden" name="action" value="delete_popup">
                                                    <input type="hidden" name="popup_id" value="<?php echo (int)$popup['popup_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                                <form method="POST" style="display:inline">
                                                    <input type="hidden" name="action" value="toggle_popup">
                                                    <input type="hidden" name="popup_id" value="<?php echo (int)$popup['popup_id']; ?>">
                                                    <input type="hidden" name="is_active" value="<?php echo $popup['is_active'] ? '0' : '1'; ?>">
                                                    <button type="submit" class="btn btn-sm btn-<?php echo $popup['is_active'] ? 'secondary' : 'success'; ?>">
                                                        <?php echo $popup['is_active'] ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum Ada Popup Gambar</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Existing Job Management Section -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-plus"></i> Tambah Lowongan</h3>
                </div>
                <form method="POST" class="card-body">
                    <input type="hidden" name="action" value="add">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Lokasi</label>
                            <input type="text" name="location">
                        </div>
                        <div class="form-group">
                            <label>Range Gaji</label>
                            <input type="text" name="salary_range">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="form-group full">
                            <label>Deskripsi</label>
                            <textarea name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group full">
                            <label>Persyaratan</label>
                            <textarea name="requirements" rows="3"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Daftar Lowongan</h3>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Lokasi</th>
                                <th>Gaji</th>
                                <th>Status</th>
                                <th>Diposting</th>
                                <th>Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($list && mysqli_num_rows($list) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($list)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                                        <td><?php echo htmlspecialchars($row['salary_range']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $row['status'] === 'open' ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['posted_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['poster'] ?: '-'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="openEdit(<?php echo (int)$row['job_id']; ?>, <?php echo htmlspecialchars(json_encode($row)); ?>)"><i class="fas fa-edit"></i></button>
                                            <form method="POST" style="display:inline" onsubmit="return confirm('Hapus lowongan ini?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="job_id" value="<?php echo (int)$row['job_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                            <form method="POST" style="display:inline">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="job_id" value="<?php echo (int)$row['job_id']; ?>">
                                                <input type="hidden" name="new_status" value="<?php echo $row['status'] === 'open' ? 'closed' : 'open'; ?>">
                                                <button type="submit" class="btn btn-sm btn-secondary">
                                                    <?php echo $row['status'] === 'open' ? 'Tutup' : 'Buka'; ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Anda Belum Membuat Lowongan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Job Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Edit Lowongan</h3>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="job_id" id="edit_job_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="title" id="edit_title" required>
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="location" id="edit_location">
                    </div>
                    <div class="form-group">
                        <label>Range Gaji</label>
                        <input type="text" name="salary_range" id="edit_salary_range">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" required>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label>Deskripsi</label>
                        <textarea name="description" id="edit_description" rows="3" required></textarea>
                    </div>
                    <div class="form-group full">
                        <label>Persyaratan</label>
                        <textarea name="requirements" id="edit_requirements" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Popup Modal -->
    <div id="editPopupModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePopupModal()">&times;</span>
            <h3>Edit Popup Gambar</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_popup">
                <input type="hidden" name="popup_id" id="edit_popup_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Judul Popup</label>
                        <input type="text" name="popup_title" id="edit_popup_title" required>
                    </div>
                    <div class="form-group">
                        <label>Orientasi</label>
                        <select name="orientation" id="edit_popup_orientation" required>
                            <option value="vertical">Vertikal (Portrait)</option>
                            <option value="horizontal">Horizontal (Landscape)</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label>Ganti Gambar (kosongkan jika tidak ingin mengganti)</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="popup_image" id="edit_popup_image" accept="image/*">
                            <label for="edit_popup_image" class="file-input-display">
                                <i class="fas fa-cloud-upload-alt"></i><br>
                                Klik untuk memilih gambar baru (opsional)
                            </label>
                        </div>
                        <div class="file-info">
                            Format: JPG, PNG, GIF | Maksimal: 5MB
                        </div>
                    </div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" onclick="closePopupModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
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

        function openEdit(jobId, data) {
            document.getElementById('edit_job_id').value = jobId;
            document.getElementById('edit_title').value = data.title || '';
            document.getElementById('edit_location').value = data.location || '';
            document.getElementById('edit_salary_range').value = data.salary_range || '';
            document.getElementById('edit_status').value = data.status || 'open';
            document.getElementById('edit_description').value = data.description || '';
            document.getElementById('edit_requirements').value = data.requirements || '';
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function openEditPopup(popupId, data) {
            document.getElementById('edit_popup_id').value = popupId;
            document.getElementById('edit_popup_title').value = data.title || '';
            document.getElementById('edit_popup_orientation').value = data.orientation || 'vertical';
            document.getElementById('editPopupModal').style.display = 'block';
        }

        function closePopupModal() {
            document.getElementById('editPopupModal').style.display = 'none';
        }

        window.onclick = function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal();
                closePopupModal();
            }
        }

        // File input display functionality
        document.addEventListener('DOMContentLoaded', function() {
            const fileInputs = document.querySelectorAll('input[type="file"]');
            
            fileInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    const label = document.querySelector('label[for="' + this.id + '"]');
                    if (this.files && this.files[0]) {
                        const fileName = this.files[0].name;
                        const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);
                        label.innerHTML = '<i class="fas fa-file-image"></i><br>' + fileName + '<br><small>(' + fileSize + ' MB)</small>';
                    }
                });
            });
        });
    </script>
</body>

</html>