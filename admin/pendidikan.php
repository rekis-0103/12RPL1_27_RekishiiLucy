<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role = $_SESSION['role'];

// Function to log activities
function logActivity($conn, $admin_user_id, $action)
{
    $admin_user_id = (int)$admin_user_id;
    $action = mysqli_real_escape_string($conn, $action);
    
    $log_query = "INSERT INTO log_aktivitas (user_id, action) VALUES ($admin_user_id, '$action')";
    mysqli_query($conn, $log_query);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'add_level') {
        $nama_jenjang = mysqli_real_escape_string($conn, $_POST['nama_jenjang']);
        $kode_jenjang = mysqli_real_escape_string($conn, $_POST['kode_jenjang']);
        $punya_jurusan = isset($_POST['punya_jurusan']) ? 1 : 0;
        
        $query = "INSERT INTO jenjang_pendidikan (nama_jenjang, kode_jenjang, punya_jurusan) VALUES ('$nama_jenjang', '$kode_jenjang', $punya_jurusan)";
        
        if (mysqli_query($conn, $query)) {
            $jurusan_text = $punya_jurusan ? 'dengan jurusan' : 'tanpa jurusan';
            logActivity($conn, $user_id, "Admin: tambah jenjang pendidikan '$nama_jenjang' ($kode_jenjang) $jurusan_text");
            echo json_encode(['success' => true, 'message' => 'Jenjang pendidikan berhasil ditambahkan']);
        } else {
            logActivity($conn, $user_id, "Admin: gagal tambah jenjang pendidikan '$nama_jenjang' - Database error");
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan jenjang pendidikan']);
        }
        exit();
    }
    
    if ($_POST['action'] === 'edit_level') {
        $id_jenjang = intval($_POST['id_jenjang']);
        $nama_jenjang = mysqli_real_escape_string($conn, $_POST['nama_jenjang']);
        $kode_jenjang = mysqli_real_escape_string($conn, $_POST['kode_jenjang']);
        $punya_jurusan = isset($_POST['punya_jurusan']) ? 1 : 0;
        
        // Get old data for logging
        $old_query = "SELECT nama_jenjang, kode_jenjang, punya_jurusan FROM jenjang_pendidikan WHERE id_jenjang = $id_jenjang";
        $old_result = mysqli_query($conn, $old_query);
        $old_data = mysqli_fetch_assoc($old_result);
        
        $query = "UPDATE jenjang_pendidikan SET nama_jenjang = '$nama_jenjang', kode_jenjang = '$kode_jenjang', punya_jurusan = $punya_jurusan WHERE id_jenjang = $id_jenjang";
        
        if (mysqli_query($conn, $query)) {
            $changes = [];
            if ($old_data['nama_jenjang'] != $nama_jenjang) $changes[] = "Nama: {$old_data['nama_jenjang']} → $nama_jenjang";
            if ($old_data['kode_jenjang'] != $kode_jenjang) $changes[] = "Kode: {$old_data['kode_jenjang']} → $kode_jenjang";
            if ($old_data['punya_jurusan'] != $punya_jurusan) {
                $old_status = $old_data['punya_jurusan'] ? 'dengan jurusan' : 'tanpa jurusan';
                $new_status = $punya_jurusan ? 'dengan jurusan' : 'tanpa jurusan';
                $changes[] = "$old_status → $new_status";
            }
            $change_text = !empty($changes) ? implode(", ", $changes) : "Tidak ada perubahan";
            
            logActivity($conn, $user_id, "Admin: edit jenjang pendidikan '$nama_jenjang' - $change_text");
            echo json_encode(['success' => true, 'message' => 'Jenjang pendidikan berhasil diupdate']);
        } else {
            logActivity($conn, $user_id, "Admin: gagal edit jenjang pendidikan ID #$id_jenjang - Database error");
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate jenjang pendidikan']);
        }
        exit();
    }
    
    if ($_POST['action'] === 'get_level') {
        $id_jenjang = intval($_POST['id_jenjang']);
        $query = "SELECT * FROM jenjang_pendidikan WHERE id_jenjang = $id_jenjang";
        $result = mysqli_query($conn, $query);
        
        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        exit();
    }
    
    if ($_POST['action'] === 'add_major') {
        $id_jenjang = intval($_POST['id_jenjang']);
        $nama_jurusan = mysqli_real_escape_string($conn, $_POST['nama_jurusan']);
        
        // Get jenjang name for logging
        $jenjang_query = "SELECT nama_jenjang, kode_jenjang FROM jenjang_pendidikan WHERE id_jenjang = $id_jenjang";
        $jenjang_result = mysqli_query($conn, $jenjang_query);
        $jenjang_data = mysqli_fetch_assoc($jenjang_result);
        
        $query = "INSERT INTO jurusan_pendidikan (id_jenjang, nama_jurusan) VALUES ($id_jenjang, '$nama_jurusan')";
        
        if (mysqli_query($conn, $query)) {
            logActivity($conn, $user_id, "Admin: tambah jurusan '$nama_jurusan' untuk jenjang {$jenjang_data['nama_jenjang']} ({$jenjang_data['kode_jenjang']})");
            echo json_encode(['success' => true, 'message' => 'Jurusan berhasil ditambahkan']);
        } else {
            logActivity($conn, $user_id, "Admin: gagal tambah jurusan '$nama_jurusan' - Database error");
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan jurusan']);
        }
        exit();
    }
    
    if ($_POST['action'] === 'edit_major') {
        $id_jurusan = intval($_POST['id_jurusan']);
        $id_jenjang = intval($_POST['id_jenjang']);
        $nama_jurusan = mysqli_real_escape_string($conn, $_POST['nama_jurusan']);
        
        // Get old data for logging
        $old_query = "SELECT jp.nama_jurusan, jp.id_jenjang, jnj.nama_jenjang as old_jenjang, jnj.kode_jenjang as old_kode 
                      FROM jurusan_pendidikan jp 
                      JOIN jenjang_pendidikan jnj ON jp.id_jenjang = jnj.id_jenjang 
                      WHERE jp.id_jurusan = $id_jurusan";
        $old_result = mysqli_query($conn, $old_query);
        $old_data = mysqli_fetch_assoc($old_result);
        
        // Get new jenjang name
        $new_jenjang_query = "SELECT nama_jenjang, kode_jenjang FROM jenjang_pendidikan WHERE id_jenjang = $id_jenjang";
        $new_jenjang_result = mysqli_query($conn, $new_jenjang_query);
        $new_jenjang = mysqli_fetch_assoc($new_jenjang_result);
        
        $query = "UPDATE jurusan_pendidikan SET id_jenjang = $id_jenjang, nama_jurusan = '$nama_jurusan' WHERE id_jurusan = $id_jurusan";
        
        if (mysqli_query($conn, $query)) {
            $changes = [];
            if ($old_data['nama_jurusan'] != $nama_jurusan) $changes[] = "Nama: {$old_data['nama_jurusan']} → $nama_jurusan";
            if ($old_data['id_jenjang'] != $id_jenjang) $changes[] = "Jenjang: {$old_data['old_jenjang']} → {$new_jenjang['nama_jenjang']}";
            $change_text = !empty($changes) ? implode(", ", $changes) : "Tidak ada perubahan";
            
            logActivity($conn, $user_id, "Admin: edit jurusan '$nama_jurusan' - $change_text");
            echo json_encode(['success' => true, 'message' => 'Jurusan berhasil diupdate']);
        } else {
            logActivity($conn, $user_id, "Admin: gagal edit jurusan ID #$id_jurusan - Database error");
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate jurusan']);
        }
        exit();
    }
    
    if ($_POST['action'] === 'get_major') {
        $id_jurusan = intval($_POST['id_jurusan']);
        $query = "SELECT * FROM jurusan_pendidikan WHERE id_jurusan = $id_jurusan";
        $result = mysqli_query($conn, $query);
        
        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        exit();
    }
    
    if ($_POST['action'] === 'delete_level') {
        $id_jenjang = intval($_POST['id_jenjang']);
        
        // Get jenjang name for logging
        $get_query = "SELECT nama_jenjang, kode_jenjang FROM jenjang_pendidikan WHERE id_jenjang = $id_jenjang";
        $get_result = mysqli_query($conn, $get_query);
        $jenjang_data = mysqli_fetch_assoc($get_result);
        
        $query = "UPDATE jenjang_pendidikan SET status = 0 WHERE id_jenjang = $id_jenjang";
        
        if (mysqli_query($conn, $query)) {
            logActivity($conn, $user_id, "Admin: hapus jenjang pendidikan '{$jenjang_data['nama_jenjang']}' ({$jenjang_data['kode_jenjang']})");
            echo json_encode(['success' => true, 'message' => 'Jenjang pendidikan berhasil dihapus']);
        } else {
            logActivity($conn, $user_id, "Admin: gagal hapus jenjang pendidikan ID #$id_jenjang - Database error");
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus jenjang pendidikan']);
        }
        exit();
    }
    
    if ($_POST['action'] === 'delete_major') {
        $id_jurusan = intval($_POST['id_jurusan']);
        
        // Get jurusan name for logging
        $get_query = "SELECT jp.nama_jurusan, jnj.nama_jenjang, jnj.kode_jenjang 
                      FROM jurusan_pendidikan jp 
                      JOIN jenjang_pendidikan jnj ON jp.id_jenjang = jnj.id_jenjang 
                      WHERE jp.id_jurusan = $id_jurusan";
        $get_result = mysqli_query($conn, $get_query);
        $jurusan_data = mysqli_fetch_assoc($get_result);
        
        $query = "UPDATE jurusan_pendidikan SET status = 0 WHERE id_jurusan = $id_jurusan";
        
        if (mysqli_query($conn, $query)) {
            logActivity($conn, $user_id, "Admin: hapus jurusan '{$jurusan_data['nama_jurusan']}' dari jenjang {$jurusan_data['nama_jenjang']} ({$jurusan_data['kode_jenjang']})");
            echo json_encode(['success' => true, 'message' => 'Jurusan berhasil dihapus']);
        } else {
            logActivity($conn, $user_id, "Admin: gagal hapus jurusan ID #$id_jurusan - Database error");
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus jurusan']);
        }
        exit();
    }
}

// Get all education levels
$levels_query = "SELECT * FROM jenjang_pendidikan WHERE status = 1 ORDER BY id_jenjang";
$levels_result = mysqli_query($conn, $levels_query);

// Get all majors with their levels
$majors_query = "SELECT jp.*, jnj.nama_jenjang, jnj.kode_jenjang 
                 FROM jurusan_pendidikan jp 
                 JOIN jenjang_pendidikan jnj ON jp.id_jenjang = jnj.id_jenjang 
                 WHERE jp.status = 1 
                 ORDER BY jnj.id_jenjang, jp.nama_jurusan";
$majors_result = mysqli_query($conn, $majors_query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pendidikan - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/pendidikan.css">
</head>

<body>
    <!-- Tombol hamburger -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Kelola Pendidikan</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Kelola User</a></li>
                <li><a href="logs.php"><i class="fas fa-history"></i> Log Aktivitas</a></li>
                <li><a href="pendidikan.php" class="active"><i class="fas fa-graduation-cap"></i> Pendidikan</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="main-content">
            <div class="content-header">
                <h1><i class="fas fa-graduation-cap"></i> Kelola Data Pendidikan</h1>
                <p>Kelola jenjang pendidikan dan jurusan untuk pelamar</p>
            </div>

            <!-- Education Levels Section -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-layer-group"></i> Jenjang Pendidikan</h2>
                    <button class="btn btn-primary" onclick="showAddLevelModal()">
                        <i class="fas fa-plus"></i> Tambah Jenjang
                    </button>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Jenjang</th>
                                <th>Punya Jurusan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($level = mysqli_fetch_assoc($levels_result)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><span class="badge"><?php echo htmlspecialchars($level['kode_jenjang']); ?></span></td>
                                <td><?php echo htmlspecialchars($level['nama_jenjang']); ?></td>
                                <td>
                                    <?php if ($level['punya_jurusan']): ?>
                                        <span class="status-badge active">Ya</span>
                                    <?php else: ?>
                                        <span class="status-badge inactive">Tidak</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn-action btn-warning" onclick="editLevel(<?php echo $level['id_jenjang']; ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-danger" onclick="deleteLevel(<?php echo $level['id_jenjang']; ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Education Majors Section -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-book"></i> Jurusan Pendidikan</h2>
                    <button class="btn btn-primary" onclick="showAddMajorModal()">
                        <i class="fas fa-plus"></i> Tambah Jurusan
                    </button>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenjang</th>
                                <th>Nama Jurusan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            mysqli_data_seek($majors_result, 0);
                            while ($major = mysqli_fetch_assoc($majors_result)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><span class="badge"><?php echo htmlspecialchars($major['kode_jenjang']); ?></span></td>
                                <td><?php echo htmlspecialchars($major['nama_jurusan']); ?></td>
                                <td>
                                    <button class="btn-action btn-warning" onclick="editMajor(<?php echo $major['id_jurusan']; ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-danger" onclick="deleteMajor(<?php echo $major['id_jurusan']; ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Level -->
    <div id="addLevelModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tambah Jenjang Pendidikan</h3>
                <span class="close" onclick="closeModal('addLevelModal')">&times;</span>
            </div>
            <form id="addLevelForm" onsubmit="submitAddLevel(event)">
                <div class="form-group">
                    <label for="nama_jenjang">Nama Jenjang:</label>
                    <input type="text" id="nama_jenjang" name="nama_jenjang" required>
                </div>
                <div class="form-group">
                    <label for="kode_jenjang">Kode Jenjang:</label>
                    <input type="text" id="kode_jenjang" name="kode_jenjang" required>
                </div>
                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" id="punya_jurusan" name="punya_jurusan">
                        Memiliki Jurusan
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addLevelModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Level -->
    <div id="editLevelModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Jenjang Pendidikan</h3>
                <span class="close" onclick="closeModal('editLevelModal')">&times;</span>
            </div>
            <form id="editLevelForm" onsubmit="submitEditLevel(event)">
                <input type="hidden" id="edit_id_jenjang" name="id_jenjang">
                <div class="form-group">
                    <label for="edit_nama_jenjang">Nama Jenjang:</label>
                    <input type="text" id="edit_nama_jenjang" name="nama_jenjang" required>
                </div>
                <div class="form-group">
                    <label for="edit_kode_jenjang">Kode Jenjang:</label>
                    <input type="text" id="edit_kode_jenjang" name="kode_jenjang" required>
                </div>
                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" id="edit_punya_jurusan" name="punya_jurusan">
                        Memiliki Jurusan
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editLevelModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Add Major -->
    <div id="addMajorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tambah Jurusan</h3>
                <span class="close" onclick="closeModal('addMajorModal')">&times;</span>
            </div>
            <form id="addMajorForm" onsubmit="submitAddMajor(event)">
                <div class="form-group">
                    <label for="id_jenjang">Jenjang Pendidikan:</label>
                    <select id="id_jenjang" name="id_jenjang" required>
                        <option value="">-- Pilih Jenjang --</option>
                        <?php 
                        mysqli_data_seek($levels_result, 0);
                        while ($level = mysqli_fetch_assoc($levels_result)): 
                            if ($level['punya_jurusan']):
                        ?>
                        <option value="<?php echo $level['id_jenjang']; ?>">
                            <?php echo htmlspecialchars($level['nama_jenjang']); ?>
                        </option>
                        <?php 
                            endif;
                        endwhile; 
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nama_jurusan">Nama Jurusan:</label>
                    <input type="text" id="nama_jurusan" name="nama_jurusan" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addMajorModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Major -->
    <div id="editMajorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Jurusan</h3>
                <span class="close" onclick="closeModal('editMajorModal')">&times;</span>
            </div>
            <form id="editMajorForm" onsubmit="submitEditMajor(event)">
                <input type="hidden" id="edit_id_jurusan" name="id_jurusan">
                <div class="form-group">
                    <label for="edit_id_jenjang">Jenjang Pendidikan:</label>
                    <select id="edit_id_jenjang" name="id_jenjang" required>
                        <option value="">-- Pilih Jenjang --</option>
                        <?php 
                        mysqli_data_seek($levels_result, 0);
                        while ($level = mysqli_fetch_assoc($levels_result)): 
                            if ($level['punya_jurusan']):
                        ?>
                        <option value="<?php echo $level['id_jenjang']; ?>">
                            <?php echo htmlspecialchars($level['nama_jenjang']); ?>
                        </option>
                        <?php 
                            endif;
                        endwhile; 
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_nama_jurusan">Nama Jurusan:</label>
                    <input type="text" id="edit_nama_jurusan" name="nama_jurusan" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editMajorModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Functions
        function showAddLevelModal() {
            document.getElementById('addLevelModal').style.display = 'block';
        }

        function showAddMajorModal() {
            document.getElementById('addMajorModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            // Reset form
            const form = document.querySelector(`#${modalId} form`);
            if (form) form.reset();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Submit Add Level
        async function submitAddLevel(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            formData.append('action', 'add_level');
            
            try {
                const response = await fetch('pendidikan.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    closeModal('addLevelModal');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan saat menambahkan data', 'error');
            }
        }

        // Submit Add Major
        async function submitAddMajor(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            formData.append('action', 'add_major');
            
            try {
                const response = await fetch('pendidikan.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    closeModal('addMajorModal');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan saat menambahkan data', 'error');
            }
        }

        // Edit Level
        async function editLevel(idJenjang) {
            try {
                const formData = new FormData();
                formData.append('action', 'get_level');
                formData.append('id_jenjang', idJenjang);
                
                const response = await fetch('pendidikan.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const data = result.data;
                    document.getElementById('edit_id_jenjang').value = data.id_jenjang;
                    document.getElementById('edit_nama_jenjang').value = data.nama_jenjang;
                    document.getElementById('edit_kode_jenjang').value = data.kode_jenjang;
                    document.getElementById('edit_punya_jurusan').checked = data.punya_jurusan == 1;
                    
                    document.getElementById('editLevelModal').style.display = 'block';
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan saat mengambil data', 'error');
            }
        }

        // Submit Edit Level
        async function submitEditLevel(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            formData.append('action', 'edit_level');
            
            try {
                const response = await fetch('pendidikan.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    closeModal('editLevelModal');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan saat mengupdate data', 'error');
            }
        }

        // Edit Major
        async function editMajor(idJurusan) {
            try {
                const formData = new FormData();
                formData.append('action', 'get_major');
                formData.append('id_jurusan', idJurusan);
                
                const response = await fetch('pendidikan.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const data = result.data;
                    document.getElementById('edit_id_jurusan').value = data.id_jurusan;
                    document.getElementById('edit_id_jenjang').value = data.id_jenjang;
                    document.getElementById('edit_nama_jurusan').value = data.nama_jurusan;
                    
                    document.getElementById('editMajorModal').style.display = 'block';
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan saat mengambil data', 'error');
            }
        }

        // Submit Edit Major
        async function submitEditMajor(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            formData.append('action', 'edit_major');
            
            try {
                const response = await fetch('pendidikan.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    closeModal('editMajorModal');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan saat mengupdate data', 'error');
            }
        }

        // Delete Level
        async function deleteLevel(idJenjang) {
            if (!confirm('Apakah Anda yakin ingin menghapus jenjang pendidikan ini?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'delete_level');
            formData.append('id_jenjang', idJenjang);
            
            try {
                const response = await fetch('pendidikan.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan saat menghapus data', 'error');
            }
        }

        // Delete Major
        async function deleteMajor(idJurusan) {
            if (!confirm('Apakah Anda yakin ingin menghapus jurusan ini?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'delete_major');
            formData.append('id_jurusan', idJurusan);
            
            try {
                const response = await fetch('pendidikan.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan saat menghapus data', 'error');
            }
        }

        // Show Alert
        function showAlert(message, type) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create new alert
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            `;
            
            // Insert alert at the top of main content
            const mainContent = document.querySelector('.main-content');
            mainContent.insertBefore(alert, mainContent.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alert.style.animation = 'fadeOut 0.3s';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }

        // Sidebar toggle function
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

        // Close sidebar when clicking outside
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