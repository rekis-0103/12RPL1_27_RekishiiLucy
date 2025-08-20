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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_user':
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
                $role = mysqli_real_escape_string($conn, $_POST['role']);
                $password = md5($_POST['password']); // Using MD5 as per existing pattern
                
                $check_query = "SELECT user_id FROM users WHERE username = '$username' OR email = '$email'";
                $check_result = mysqli_query($conn, $check_query);
                
                if (mysqli_num_rows($check_result) > 0) {
                    $error = "Username atau email sudah ada!";
                } else {
                    $insert_query = "INSERT INTO users (username, password, email, full_name, role) VALUES ('$username', '$password', '$email', '$full_name', '$role')";
                    if (mysqli_query($conn, $insert_query)) {
                        $success = "User berhasil ditambahkan!";
                    } else {
                        $error = "Gagal menambahkan user!";
                    }
                }
                break;
                
            case 'update_role':
                $update_user_id = (int)$_POST['user_id'];
                $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);
                
                // Prevent admin from changing other admin roles
                $check_admin_query = "SELECT role FROM users WHERE user_id = $update_user_id";
                $check_admin_result = mysqli_query($conn, $check_admin_query);
                $user_data = mysqli_fetch_assoc($check_admin_result);
                
                if ($user_data['role'] == 'admin' && $_SESSION['user_id'] != $update_user_id) {
                    $error = "Tidak dapat mengubah role admin lain!";
                } else {
                    $update_query = "UPDATE users SET role = '$new_role' WHERE user_id = $update_user_id";
                    if (mysqli_query($conn, $update_query)) {
                        $success = "Role user berhasil diubah!";
                    } else {
                        $error = "Gagal mengubah role user!";
                    }
                }
                break;
                
            case 'delete_user':
                $delete_user_id = (int)$_POST['user_id'];
                
                // Prevent admin from deleting other admins
                $check_admin_query = "SELECT role FROM users WHERE user_id = $delete_user_id";
                $check_admin_result = mysqli_query($conn, $check_admin_query);
                $user_data = mysqli_fetch_assoc($check_admin_result);
                
                if ($user_data['role'] == 'admin' && $_SESSION['user_id'] != $delete_user_id) {
                    $error = "Tidak dapat menghapus admin lain!";
                } else {
                    $delete_query = "UPDATE users SET hapus = 1 WHERE user_id = $delete_user_id";
                    if (mysqli_query($conn, $delete_query)) {
                        $success = "User berhasil dihapus!";
                    } else {
                        $error = "Gagal menghapus user!";
                    }
                }
                break;
        }
    }
}

// Get filter parameters
$role_filter = isset($_GET['role']) ? mysqli_real_escape_string($conn, $_GET['role']) : '';

// Build query with filters
$where_clause = "WHERE hapus = 0";
if ($role_filter) {
    $where_clause .= " AND role = '$role_filter'";
}

$users_query = "SELECT * FROM users $where_clause ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/users.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Kelola User</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php" class="active"><i class="fas fa-users"></i> Kelola User</a></li>
                <li><a href="logs.php"><i class="fas fa-history"></i> Log Aktivitas</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Kelola User</h1>
                <p>Kelola data pengguna sistem</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Add User Form -->
            <div class="card">
                <h3><i class="fas fa-plus"></i> Tambah User Baru</h3>
                <form method="POST" class="add-user-form">
                    <input type="hidden" name="action" value="add_user">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Nama Lengkap *</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role *</label>
                            <select id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="pelamar">Pelamar</option>
                                <option value="hrd">HRD</option>
                                <option value="konten">Konten</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah User</button>
                </form>
            </div>

            <!-- Filter and Users List -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Daftar User</h3>
                    <div class="filter-section">
                        <form method="GET" class="filter-form">
                            <select name="role" onchange="this.form.submit()">
                                <option value="">Semua Role</option>
                                <option value="admin" <?php echo ($role_filter == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="pelamar" <?php echo ($role_filter == 'pelamar') ? 'selected' : ''; ?>>Pelamar</option>
                                <option value="hrd" <?php echo ($role_filter == 'hrd') ? 'selected' : ''; ?>>HRD</option>
                                <option value="konten" <?php echo ($role_filter == 'konten') ? 'selected' : ''; ?>>Konten</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="users-table">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($users_result) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td>
                                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $user['status']; ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($user['role'] != 'admin' || $_SESSION['user_id'] == $user['user_id']): ?>
                                                    <button class="btn btn-sm btn-warning" onclick="editRole(<?php echo $user['user_id']; ?>, '<?php echo $user['role']; ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($user['role'] != 'admin' || $_SESSION['user_id'] == $user['user_id']): ?>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data user</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div id="editRoleModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Edit Role User</h3>
            <form method="POST">
                <input type="hidden" name="action" value="update_role">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="form-group">
                    <label for="new_role">Role Baru</label>
                    <select id="new_role" name="new_role" required>
                        <option value="pelamar">Pelamar</option>
                        <option value="hrd">HRD</option>
                        <option value="konten">Konten</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Role</button>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Konfirmasi Hapus</h3>
            <p>Apakah Anda yakin ingin menghapus user <strong id="delete_username"></strong>?</p>
            <form method="POST">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" id="delete_user_id">
                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../js/navbar.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Modal functions
        function editRole(userId, currentRole) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('new_role').value = currentRole;
            document.getElementById('editRoleModal').style.display = 'block';
        }

        function deleteUser(userId, username) {
            document.getElementById('delete_user_id').value = userId;
            document.getElementById('delete_username').textContent = username;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking on X or outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        document.querySelectorAll('.close').forEach(function(closeBtn) {
            closeBtn.onclick = function() {
                this.closest('.modal').style.display = 'none';
            }
        });
    </script>
</body>

</html>
