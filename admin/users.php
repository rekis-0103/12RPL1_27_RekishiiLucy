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
function logActivity($conn, $admin_user_id, $action, $details = '')
{
    $admin_user_id = (int)$admin_user_id;
    $action = mysqli_real_escape_string($conn, $action);
    $details = mysqli_real_escape_string($conn, $details);
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '::1';

    $log_query = "INSERT INTO log_aktivitas (user_id, action) VALUES ($admin_user_id, '$action')";
    mysqli_query($conn, $log_query);
}

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
                    logActivity($conn, $user_id, "Gagal menambah user: Username/email sudah ada", "Username: $username, Email: $email");
                } else {
                    $insert_query = "INSERT INTO users (username, password, email, full_name, role) VALUES ('$username', '$password', '$email', '$full_name', '$role')";
                    if (mysqli_query($conn, $insert_query)) {
                        $success = "User berhasil ditambahkan!";
                        logActivity($conn, $user_id, "Menambah user baru", "Username: $username, Email: $email, Role: $role, Nama: $full_name");
                    } else {
                        $error = "Gagal menambahkan user!";
                        logActivity($conn, $user_id, "Gagal menambah user: Database error", "Username: $username, Email: $email");
                    }
                }
                break;

            case 'edit_user':
                $edit_user_id = (int)$_POST['user_id'];
                $edit_username = mysqli_real_escape_string($conn, $_POST['username']);
                $edit_email = mysqli_real_escape_string($conn, $_POST['email']);
                $edit_full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
                $edit_role = mysqli_real_escape_string($conn, $_POST['role']);
                $edit_status = mysqli_real_escape_string($conn, $_POST['status']);

                // Get current user data for logging
                $get_current_query = "SELECT username, email, full_name, role, status FROM users WHERE user_id = $edit_user_id";
                $get_current_result = mysqli_query($conn, $get_current_query);
                $current_data = mysqli_fetch_assoc($get_current_result);

                // Check if username or email already exists (excluding current user)
                $check_duplicate_query = "SELECT user_id FROM users WHERE (username = '$edit_username' OR email = '$edit_email') AND user_id != $edit_user_id";
                $check_duplicate_result = mysqli_query($conn, $check_duplicate_query);

                if (mysqli_num_rows($check_duplicate_result) > 0) {
                    $error = "Username atau email sudah digunakan oleh user lain!";
                    logActivity($conn, $user_id, "Gagal mengedit user: Username/email sudah ada", "Target user ID: $edit_user_id");
                } else {
                    // Check if trying to edit another admin (except self)
                    if ($current_data['role'] == 'admin' && $_SESSION['user_id'] != $edit_user_id && $edit_role != 'admin') {
                        $error = "Tidak dapat mengubah role admin lain!";
                        logActivity($conn, $user_id, "Gagal mengedit user: Mencoba mengubah admin lain", "Target user: {$current_data['username']}");
                    } else {
                        $update_query = "UPDATE users SET 
                                        username = '$edit_username',
                                        email = '$edit_email', 
                                        full_name = '$edit_full_name',
                                        role = '$edit_role',
                                        status = '$edit_status'
                                        WHERE user_id = $edit_user_id";

                        if (mysqli_query($conn, $update_query)) {
                            $success = "Data user berhasil diupdate!";

                            // Log detailed changes
                            $changes = [];
                            if ($current_data['username'] != $edit_username) $changes[] = "Username: {$current_data['username']} → $edit_username";
                            if ($current_data['email'] != $edit_email) $changes[] = "Email: {$current_data['email']} → $edit_email";
                            if ($current_data['full_name'] != $edit_full_name) $changes[] = "Nama: {$current_data['full_name']} → $edit_full_name";
                            if ($current_data['role'] != $edit_role) $changes[] = "Role: {$current_data['role']} → $edit_role";
                            if ($current_data['status'] != $edit_status) $changes[] = "Status: {$current_data['status']} → $edit_status";

                            $change_details = implode(", ", $changes);
                            logActivity($conn, $user_id, "Mengedit data user", "User: $edit_username, Perubahan: $change_details");
                        } else {
                            $error = "Gagal mengupdate data user!";
                            logActivity($conn, $user_id, "Gagal mengedit user: Database error", "Target user ID: $edit_user_id");
                        }
                    }
                }
                break;

            case 'reset_password':
                $reset_user_id = (int)$_POST['user_id'];
                $new_password = md5($_POST['new_password']);

                // Get user info for logging
                $get_user_query = "SELECT username, full_name, role FROM users WHERE user_id = $reset_user_id";
                $get_user_result = mysqli_query($conn, $get_user_query);
                $user_data = mysqli_fetch_assoc($get_user_result);

                // Check if trying to reset admin password (except self)
                if ($user_data['role'] == 'admin' && $_SESSION['user_id'] != $reset_user_id) {
                    $error = "Tidak dapat mereset password admin lain!";
                    logActivity($conn, $user_id, "Gagal reset password: Mencoba reset admin lain", "Target user: {$user_data['username']}");
                } else {
                    $reset_query = "UPDATE users SET password = '$new_password' WHERE user_id = $reset_user_id";
                    if (mysqli_query($conn, $reset_query)) {
                        $success = "Password berhasil direset!";
                        logActivity($conn, $user_id, "Reset password user", "User: {$user_data['username']} ({$user_data['full_name']})");
                    } else {
                        $error = "Gagal mereset password!";
                        logActivity($conn, $user_id, "Gagal reset password: Database error", "User: {$user_data['username']}");
                    }
                }
                break;

            case 'delete_user':
                $delete_user_id = (int)$_POST['user_id'];

                // Get user info for logging
                $get_user_query = "SELECT username, full_name, role FROM users WHERE user_id = $delete_user_id";
                $get_user_result = mysqli_query($conn, $get_user_query);
                $user_data = mysqli_fetch_assoc($get_user_result);
                $target_username = $user_data['username'];
                $target_fullname = $user_data['full_name'];
                $target_role = $user_data['role'];

                // Prevent admin from deleting other admins
                if ($user_data['role'] == 'admin' && $_SESSION['user_id'] != $delete_user_id) {
                    $error = "Tidak dapat menghapus admin lain!";
                    logActivity($conn, $user_id, "Gagal menghapus user: Mencoba menghapus admin lain", "Target user: $target_username ($target_fullname)");
                } else {
                    $delete_query = "UPDATE users SET hapus = 1 WHERE user_id = $delete_user_id";
                    if (mysqli_query($conn, $delete_query)) {
                        $success = "User berhasil dihapus!";
                        logActivity($conn, $user_id, "Menghapus user", "User: $target_username ($target_fullname), Role: $target_role");
                    } else {
                        $error = "Gagal menghapus user!";
                        logActivity($conn, $user_id, "Gagal menghapus user: Database error", "User: $target_username");
                    }
                }
                break;
        }
    }
}

// Get filter parameters
$role_filter = isset($_GET['role']) ? mysqli_real_escape_string($conn, $_GET['role']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Build query with filters
$where_clause = "WHERE hapus = 0";
if ($role_filter) {
    $where_clause .= " AND role = '$role_filter'";
}
if ($status_filter) {
    $where_clause .= " AND status = '$status_filter'";
}

$users_query = "SELECT * FROM users $where_clause ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin,
    SUM(CASE WHEN role = 'hrd' THEN 1 ELSE 0 END) as hrd,
    SUM(CASE WHEN role = 'konten' THEN 1 ELSE 0 END) as konten,
    SUM(CASE WHEN role = 'pelamar' THEN 1 ELSE 0 END) as pelamar
    FROM users WHERE hapus = 0";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
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
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

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

            <!-- Add User Form -->
            <div class="card">
                <form method="POST" class="add-user-form">
                    <h3><i class="fas fa-plus"></i> Tambah User Baru</h3>
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
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <div class="password-field">
                                <input type="password" id="password" name="password" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Tambah User
                    </button>
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
                            <select name="status" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="active" <?php echo ($status_filter == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                <option value="inactive" <?php echo ($status_filter == 'inactive') ? 'selected' : ''; ?>>Tidak Aktif</option>
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
                                                    <button class="btn btn-sm btn-primary" onclick="editUser(<?php echo $user['user_id']; ?>)" title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" onclick="resetPassword(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" title="Reset Password">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($user['role'] != 'admin' || $_SESSION['user_id'] == $user['user_id']): ?>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" title="Hapus User">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <i class="fas fa-inbox"></i>
                                        Tidak ada data user
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3><i class="fas fa-edit"></i> Edit User</h3>
            <form method="POST">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="user_id" id="edit_user_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_username">Username *</label>
                        <input type="text" id="edit_username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email *</label>
                        <input type="email" id="edit_email" name="email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_full_name">Nama Lengkap *</label>
                        <input type="text" id="edit_full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_role">Role *</label>
                        <select id="edit_role" name="role" required>
                            <option value="pelamar">Pelamar</option>
                            <option value="hrd">HRD</option>
                            <option value="konten">Konten</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_status">Status *</label>
                        <select id="edit_status" name="status" required>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3><i class="fas fa-key"></i> Reset Password</h3>
            <p>Reset password untuk user <strong id="reset_username"></strong></p>
            <form method="POST">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="user_id" id="reset_user_id">
                <div class="form-group">
                    <label for="new_password">Password Baru *</label>
                    <div class="password-field">
                        <input type="password" id="new_password" name="new_password" required minlength="6">
                        <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small>Minimal 6 karakter</small>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('resetPasswordModal')">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key"></i>
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3><i class="fas fa-trash"></i> Konfirmasi Hapus</h3>
            <p>Apakah Anda yakin ingin menghapus user <strong id="delete_username"></strong>?</p>
            <div class="warning-message">
                <i class="fas fa-exclamation-triangle"></i>
                Data yang sudah dihapus tidak dapat dikembalikan!
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" id="delete_user_id">
                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Hapus
                    </button>
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

        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleButton = passwordInput.nextElementSibling.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.classList.remove('fa-eye');
                toggleButton.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleButton.classList.remove('fa-eye-slash');
                toggleButton.classList.add('fa-eye');
            }
        }

        // Modal functions
        function editUser(userId) {
            // Get user data via AJAX
            fetch('get_user.php?id=' + userId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit_user_id').value = data.user.user_id;
                        document.getElementById('edit_username').value = data.user.username;
                        document.getElementById('edit_email').value = data.user.email;
                        document.getElementById('edit_full_name').value = data.user.full_name;
                        document.getElementById('edit_role').value = data.user.role;
                        document.getElementById('edit_status').value = data.user.status;
                        document.getElementById('editUserModal').style.display = 'block';
                    } else {
                        alert('Gagal mengambil data user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        }

        function resetPassword(userId, username) {
            document.getElementById('reset_user_id').value = userId;
            document.getElementById('reset_username').textContent = username;
            document.getElementById('resetPasswordModal').style.display = 'block';
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

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>

</html>