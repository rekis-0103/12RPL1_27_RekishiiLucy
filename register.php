<?php
session_start();
require_once 'connect/koneksi.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on role
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: admin/dashboard.php');
            break;
        case 'pelamar':
            header('Location: pelamar/dashboard.php');
            break;
        case 'hrd':
            header('Location: hrd/dashboard.php');
            break;
        case 'konten':
            header('Location: konten/dashboard.php');
            break;
        default:
            header('Location: index.php');
    }
    exit();
}

$error_message = '';
$success_message = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Log activity
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $log_query = "INSERT INTO log_aktivitas (user_id, action) VALUES (?, 'membuat User baru')";
    $log_stmt = mysqli_prepare($conn, $log_query);
    mysqli_stmt_bind_param($log_stmt, "i", $user['user_id']);
    mysqli_stmt_execute($log_stmt);

    // Validation
    if (empty($username) || empty($email) || empty($full_name) || empty($password)) {
        $error_message = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Password dan konfirmasi password tidak cocok!';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password minimal 6 karakter!';
    } else {
        // Check if username already exists
        $check_username = "SELECT username FROM users WHERE username = ? AND hapus = 0";
        $stmt = mysqli_prepare($conn, $check_username);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error_message = 'Username sudah digunakan!';
        } else {
            // Check if email already exists
            $check_email = "SELECT email FROM users WHERE email = ? AND hapus = 0";
            $stmt = mysqli_prepare($conn, $check_email);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $error_message = 'Email sudah digunakan!';
            } else {
                // Insert new user
                $hashed_password = md5($password);
                $insert_query = "INSERT INTO users (username, password, email, full_name, role, status) VALUES (?, ?, ?, ?, 'pelamar', 'active')";
                $stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($stmt, "ssss", $username, $hashed_password, $email, $full_name);

                if (mysqli_stmt_execute($stmt)) {
                    $success_message = 'Registrasi berhasil! Silakan login.';
                    // Redirect to login after 2 seconds
                    header("refresh:2;url=login.php");
                } else {
                    $error_message = 'Terjadi kesalahan saat registrasi!';
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
    <title>Register - PT Waindo Specterra</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="register-container">
        <div class="register-form">
            <h2>Register</h2>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="full_name">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <div class="password-container">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="register-btn">Register</button>
            </form>

            <div class="login-link">
                <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
            </div>

            <div class="back-link">
                <a href="index.php"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script src="js/common.js"></script>
    <script src="js/login.js"></script>
</body>

</html>