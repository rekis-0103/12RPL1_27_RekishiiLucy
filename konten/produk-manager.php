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
$full_name = $_SESSION['full_name'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                $category_id = (int)$_POST['category_id'];
                
                $image = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../assets/products/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = 'product_' . uniqid() . '.' . $file_extension;
                    $target_file = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $image = 'assets/products/' . $filename;
                    }
                }
                
                $stmt = mysqli_prepare($conn, "INSERT INTO products (name, description, image, category_id, created_by) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssiii", $name, $description, $image, $category_id, $user_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $log_action = "Konten: tambah produk - $name";
                    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($user_id, '$log_action')");
                    header('Location: produk-manager.php?success=add');
                } else {
                    $error = "Gagal menambah produk: " . mysqli_error($conn);
                }
                break;
                
            case 'edit':
                $product_id = (int)$_POST['product_id'];
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                $category_id = (int)$_POST['category_id'];
                
                // Get current image
                $current_result = mysqli_query($conn, "SELECT image FROM products WHERE product_id = $product_id");
                $current_product = mysqli_fetch_assoc($current_result);
                $image = $current_product['image'];
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../assets/products/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Delete old image
                    if ($image && file_exists('../' . $image)) {
                        unlink('../' . $image);
                    }
                    
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = 'product_' . uniqid() . '.' . $file_extension;
                    $target_file = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $image = 'assets/products/' . $filename;
                    }
                }
                
                $stmt = mysqli_prepare($conn, "UPDATE products SET name = ?, description = ?, image = ?, category_id = ? WHERE product_id = ?");
                mysqli_stmt_bind_param($stmt, "sssii", $name, $description, $image, $category_id, $product_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log activity
                    $log_action = "Konten: edit produk - $name";
                    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($user_id, '$log_action')");
                    header('Location: produk-manager.php?success=edit');
                } else {
                    $error = "Gagal mengubah produk: " . mysqli_error($conn);
                }
                break;
                
            case 'delete':
                $product_id = (int)$_POST['product_id'];
                
                // Get product info for logging
                $product_result = mysqli_query($conn, "SELECT name, image FROM products WHERE product_id = $product_id");
                $product_info = mysqli_fetch_assoc($product_result);
                
                if ($product_info) {
                    // Delete image file
                    if ($product_info['image'] && file_exists('../' . $product_info['image'])) {
                        unlink('../' . $product_info['image']);
                    }
                    
                    // Delete from database
                    if (mysqli_query($conn, "DELETE FROM products WHERE product_id = $product_id")) {
                        // Log activity
                        $log_action = "Konten: hapus produk - " . $product_info['name'];
                        mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($user_id, '$log_action')");
                        header('Location: produk-manager.php?success=delete');
                    } else {
                        $error = "Gagal menghapus produk: " . mysqli_error($conn);
                    }
                }
                break;
        }
    }
}

// Get categories
$categories_result = mysqli_query($conn, "SELECT * FROM product_categories ORDER BY category_name");

// Get products with categories
$products_result = mysqli_query($conn, "
    SELECT p.*, pc.category_name 
    FROM products p 
    JOIN product_categories pc ON p.category_id = pc.category_id 
    ORDER BY p.created_at DESC
");

// Get product for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $edit_id");
    $edit_product = mysqli_fetch_assoc($edit_result);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        .content-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #1e293b;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        .products-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .products-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .products-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #1e293b;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .success-message {
            background: #d1fae5;
            color: #065f46;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #a7f3d0;
        }
        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #fca5a5;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            position: relative;
        }
        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #6b7280;
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
                <li><a href="kegiatan.php"><i class="fas fa-newspaper"></i> Kelola Konten</a></li>
                <li><a href="produk-manager.php" class="active"><i class="fas fa-box"></i> Kelola Produk</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Kelola Produk</h1>
                <p>Tambah, edit, dan kelola produk perusahaan</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">
                    <?php
                    switch ($_GET['success']) {
                        case 'add': echo 'Produk berhasil ditambahkan!'; break;
                        case 'edit': echo 'Produk berhasil diperbarui!'; break;
                        case 'delete': echo 'Produk berhasil dihapus!'; break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Form Add/Edit Product -->
            <div class="content-form">
                <h3><?php echo $edit_product ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h3>
                
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $edit_product ? 'edit' : 'add'; ?>">
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['product_id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Nama Produk</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Kategori</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                <option value="<?php echo $category['category_id']; ?>"
                                        <?php echo ($edit_product && $edit_product['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" required><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Gambar Produk</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <?php if ($edit_product && $edit_product['image']): ?>
                            <br><small>Gambar saat ini: <img src="../<?php echo $edit_product['image']; ?>" class="product-image" alt="Current"></small>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $edit_product ? 'Update Produk' : 'Tambah Produk'; ?>
                    </button>
                    
                    <?php if ($edit_product): ?>
                        <a href="produk-manager.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Products List -->
            <div class="products-table">
                <table>
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($products_result) > 0): ?>
                            <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                                <tr>
                                    <td>
                                        <?php if ($product['image']): ?>
                                            <img src="../<?php echo $product['image']; ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                                <i class="fas fa-image" style="color: #6b7280;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($product['created_at'])); ?></td>
                                    <td>
                                        <a href="produk-manager.php?edit=<?php echo $product['product_id']; ?>" class="btn btn-success">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button onclick="confirmDelete(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: #6b7280;">
                                    Belum ada produk
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h3>Konfirmasi Hapus</h3>
            <p>Apakah Anda yakin ingin menghapus produk "<span id="productName"></span>"?</p>
            <p style="color: #ef4444; font-size: 14px;">Tindakan ini tidak dapat dibatalkan.</p>
            
            <form id="deleteForm" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="product_id" id="deleteProductId">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
                <button type="button" onclick="closeModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </button>
            </form>
        </div>
    </div>

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

        function confirmDelete(productId, productName) {
            document.getElementById('deleteProductId').value = productId;
            document.getElementById('productName').textContent = productName;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Mobile sidebar handling
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