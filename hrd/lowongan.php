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

// Handle actions: add, edit, toggle, delete
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
    }
}

// Fetch jobs
$list = mysqli_query($conn, "SELECT l.*, u.full_name AS poster FROM lowongan l LEFT JOIN users u ON l.posted_by = u.user_id WHERE l.hapus = 0 ORDER BY l.posted_at DESC");
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
                                    <td colspan="7" class="text-center">Belum ada lowongan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

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

        // Tutup sidebar kalau klik di luar
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-toggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                    mobileToggle.style.display = "block"; // tampilkan kembali tombol
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
        window.onclick = function(e) {
            if (e.target.classList.contains('modal')) closeModal();
        }
    </script>
</body>

</html>