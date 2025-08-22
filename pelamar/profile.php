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

function logActivity($conn, $actor_user_id, $action) {
    $actor_user_id = (int)$actor_user_id;
    $action = mysqli_real_escape_string($conn, $action);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '::1';
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($actor_user_id, '$action')");
}

// Fetch current profile
$profile_q = mysqli_query($conn, "SELECT username, email, full_name FROM users WHERE user_id=$user_id AND hapus=0 LIMIT 1");
$profile = $profile_q ? mysqli_fetch_assoc($profile_q) : null;

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $new_full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Unique email check
    $chk = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$new_email' AND user_id<>$user_id AND hapus=0 LIMIT 1");
    if ($chk && mysqli_num_rows($chk)>0) {
        $error = 'Email sudah digunakan.';
        logActivity($conn, $user_id, 'Gagal update profil (email sudah digunakan)');
    } else {
        $set_pass = '';
        if ($new_password !== '') {
            $hash = md5($new_password); // follow existing pattern
            $set_pass = ", password='$hash'";
        }
        $upd = mysqli_query($conn, "UPDATE users SET full_name='$new_full_name', email='$new_email'$set_pass WHERE user_id=$user_id");
        if ($upd) {
            $success = 'Profil berhasil diperbarui';
            $_SESSION['full_name'] = $new_full_name;
            // refresh loaded profile
            $profile = ['username'=>$username,'email'=>$new_email,'full_name'=>$new_full_name];
            logActivity($conn, $user_id, 'Update profil');
        } else {
            $error = 'Gagal memperbarui profil';
            logActivity($conn, $user_id, 'Gagal update profil (database error)');
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
</head>

<body>
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
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user"></i> Edit Profil</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" value="<?php echo htmlspecialchars($profile['username'] ?? $username); ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Password Baru (opsional)</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
    </script>
</body>

</html>