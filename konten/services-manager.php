<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in and has konten role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'konten') {
    header('Location: ../login.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role = $_SESSION['role'];

// Handle form submissions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $features = mysqli_real_escape_string($conn, $_POST['features']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $order_position = (int)$_POST['order_position'];
        
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../assets/services/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
            $image_path = 'assets/services/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../' . $image_path)) {
                // File uploaded successfully
            } else {
                $image_path = '';
            }
        }
        
        $query = "INSERT INTO services (title, description, features, image, category, order_position, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssis", $title, $description, $features, $image_path, $category, $order_position, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $service_id = mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($user_id, 'Konten: tambah layanan #$service_id ($title)')");
            header('Location: services-manager.php?success=add');
            exit();
        }
    }
    
    elseif ($action === 'edit') {
        $service_id = (int)$_POST['service_id'];
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $features = mysqli_real_escape_string($conn, $_POST['features']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $order_position = (int)$_POST['order_position'];
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        // Get current image
        $current_image_query = "SELECT image FROM services WHERE service_id = $service_id";
        $current_image_result = mysqli_query($conn, $current_image_query);
        $current_image = mysqli_fetch_assoc($current_image_result)['image'] ?? '';
        
        $image_path = $current_image;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../assets/services/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
            $image_path = 'assets/services/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../' . $image_path)) {
                // Delete old image if exists
                if ($current_image && file_exists('../' . $current_image)) {
                    unlink('../' . $current_image);
                }
            } else {
                $image_path = $current_image;
            }
        }
        
        $query = "UPDATE services SET title = ?, description = ?, features = ?, image = ?, category = ?, order_position = ?, status = ? WHERE service_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssisi", $title, $description, $features, $image_path, $category, $order_position, $status, $service_id);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($user_id, 'Konten: edit layanan #$service_id ($title)')");
            header('Location: services-manager.php?success=edit');
            exit();
        }
    }
    
    elseif ($action === 'delete') {
        $service_id = (int)$_POST['service_id'];
        
        // Get image path to delete
        $image_query = "SELECT image FROM services WHERE service_id = $service_id";
        $image_result = mysqli_query($conn, $image_query);
        $image_data = mysqli_fetch_assoc($image_result);
        
        if (mysqli_query($conn, "DELETE FROM services WHERE service_id = $service_id")) {
            // Delete image file if exists
            if ($image_data['image'] && file_exists('../' . $image_data['image'])) {
                unlink('../' . $image_data['image']);
            }
            
            mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($user_id, 'Konten: hapus layanan #$service_id')");
            header('Location: services-manager.php?success=delete');
            exit();
        }
    }
}

// Get all services
$services = mysqli_query($conn, "SELECT * FROM services ORDER BY category, order_position ASC");

// Get service for editing
$edit_service = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM services WHERE service_id = $edit_id");
    $edit_service = mysqli_fetch_assoc($edit_result);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Layanan - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #1e293b;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e5e7eb;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2563eb;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-success {
            background: #059669;
            color: white;
        }

        .btn-success:hover {
            background: #047857;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .services-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .services-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .services-table th {
            background: #f8fafc;
            padding: 15px;
            text-align: left;
            color: #1e293b;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
        }

        .services-table td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .services-table tbody tr:hover {
            background: #f8fafc;
        }

        .service-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .category-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .category-foto-dan-lidar {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .category-survey {
            background: #d1fae5;
            color: #059669;
        }

        .category-tematik {
            background: #fef3c7;
            color: #d97706;
        }

        .category-training {
            background: #ede9fe;
            color: #7c3aed;
        }

        .category-software {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #d1fae5;
            color: #059669;
        }

        .status-inactive {
            background: #fee2e2;
            color: #dc2626;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d1fae5;
            color: #059669;
            border: 1px solid #86efac;
        }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .features-help {
            font-size: 0.9rem;
            color: #6b7280;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .two-col {
                grid-template-columns: 1fr;
            }

            .services-table {
                overflow-x: auto;
            }

            .btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Dashboard Konten</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="kegiatan.php"><i class="fas fa-newspaper"></i> Kelola Berita</a></li>
                <li><a href="produk-manager.php"><i class="fas fa-box"></i> Kelola Produk</a></li>
                <li><a href="services-manager.php" class="active"><i class="fas fa-cogs"></i> Kelola Layanan</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Kelola Layanan</h1>
                <p>Tambah, edit, dan hapus layanan perusahaan</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    $success = $_GET['success'];
                    if ($success === 'add') echo 'Layanan berhasil ditambahkan!';
                    elseif ($success === 'edit') echo 'Layanan berhasil diperbarui!';
                    elseif ($success === 'delete') echo 'Layanan berhasil dihapus!';
                    ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <h3><?php echo $edit_service ? 'Edit Layanan' : 'Tambah Layanan Baru'; ?></h3>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $edit_service ? 'edit' : 'add'; ?>">
                    <?php if ($edit_service): ?>
                        <input type="hidden" name="service_id" value="<?php echo $edit_service['service_id']; ?>">
                    <?php endif; ?>

                    <div class="two-col">
                        <div class="form-group">
                            <label for="title">Judul Layanan</label>
                            <input type="text" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($edit_service['title'] ?? ''); ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="category">Kategori</label>
                            <select id="category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="foto-dan-lidar" <?php echo ($edit_service['category'] ?? '') === 'foto-dan-lidar' ? 'selected' : ''; ?>>Foto Udara dan Lidar</option>
                                <option value="survey" <?php echo ($edit_service['category'] ?? '') === 'survey' ? 'selected' : ''; ?>>Survey</option>
                                <option value="tematik" <?php echo ($edit_service['category'] ?? '') === 'tematik' ? 'selected' : ''; ?>>Tematik</option>
                                <option value="training" <?php echo ($edit_service['category'] ?? '') === 'training' ? 'selected' : ''; ?>>Training</option>
                                <option value="software" <?php echo ($edit_service['category'] ?? '') === 'software' ? 'selected' : ''; ?>>Software Development</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($edit_service['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="features">Fitur/Features</label>
                        <textarea id="features" name="features" rows="6"><?php echo htmlspecialchars($edit_service['features'] ?? ''); ?></textarea>
                        <div class="features-help">Pisahkan setiap fitur dengan tanda "|" (pipe). Contoh: Fitur 1|Fitur 2|Fitur 3</div>
                    </div>

                    <div class="two-col">
                        <div class="form-group">
                            <label for="order_position">Urutan</label>
                            <input type="number" id="order_position" name="order_position" 
                                   value="<?php echo $edit_service['order_position'] ?? 0; ?>" 
                                   min="0" required>
                        </div>

                        <?php if ($edit_service): ?>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="active" <?php echo ($edit_service['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Aktif</option>
                                <option value="inactive" <?php echo ($edit_service['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Nonaktif</option>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="image">Gambar</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <?php if ($edit_service && $edit_service['image']): ?>
                            <div style="margin-top: 10px;">
                                <img src="../<?php echo $edit_service['image']; ?>" class="service-image" alt="Current image">
                                <span style="margin-left: 10px; color: #6b7280;">Gambar saat ini</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $edit_service ? 'Update Layanan' : 'Tambah Layanan'; ?>
                        </button>
                        <?php if ($edit_service): ?>
                            <a href="services-manager.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="services-table">
                <table>
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($service = mysqli_fetch_assoc($services)): ?>
                            <tr>
                                <td>
                                    <?php if ($service['image']): ?>
                                        <img src="../<?php echo $service['image']; ?>" class="service-image" alt="Service image">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background: #f3f4f6; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($service['title']); ?></strong>
                                    <?php if ($service['description']): ?>
                                        <div style="color: #6b7280; font-size: 0.9rem; margin-top: 5px;">
                                            <?php echo substr(strip_tags($service['description']), 0, 80) . '...'; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="category-badge category-<?php echo $service['category']; ?>">
                                        <?php 
                                        $categories = [
                                            'foto-dan-lidar' => 'Foto & Lidar',
                                            'survey' => 'Survey',
                                            'tematik' => 'Tematik',
                                            'training' => 'Training',
                                            'software' => 'Software'
                                        ];
                                        echo $categories[$service['category']]; 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $service['status']; ?>">
                                        <?php echo ucfirst($service['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $service['order_position']; ?></td>
                                <td>
                                    <a href="?edit=<?php echo $service['service_id']; ?>" class="btn btn-primary" style="margin-right: 5px; padding: 6px 12px; font-size: 0.8rem;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form style="display: inline;" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus layanan ini?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.8rem;">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
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
    </script>
</body>

</html>