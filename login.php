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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Query to check user credentials
    $query = "SELECT * FROM users WHERE username = ? AND hapus = 0 AND status = 'active'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password (assuming it's stored as MD5 hash)
        if (md5($password) === $user['password']) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Log activity
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $log_query = "INSERT INTO log_aktivitas (user_id, action) VALUES (?, 'Login')";
            $log_stmt = mysqli_prepare($conn, $log_query);
            mysqli_stmt_bind_param($log_stmt, "i", $user['user_id']);
            mysqli_stmt_execute($log_stmt);
            
            // Redirect based on role
            switch ($user['role']) {
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
        } else {
            $error_message = 'Password salah!';
        }
    } else {
        $error_message = 'Username tidak ditemukan atau akun tidak aktif!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Waindo Specterra</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="login-container">
        <div class="login-form">
            <h2>Login</h2>
            
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
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="login-btnn">Login</button>
            </form>
            
            <div class="register-link">
                <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
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
