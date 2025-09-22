<?php
session_start();
require_once 'connect/koneksi.php';

// Get categories with product count
$categories_query = "
    SELECT 
        pc.*,
        COUNT(p.product_id) as product_count
    FROM product_categories pc
    LEFT JOIN products p ON pc.category_id = p.category_id AND p.status = 'active'
    GROUP BY pc.category_id
    ORDER BY pc.category_name
";
$categories_result = mysqli_query($conn, $categories_query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Waindo Specterra</title>
        
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/pages.css">
    <link rel="stylesheet" href="assets/css/produk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="produk-page">
    <?php include 'includes/navbar.php'; ?>

    <div class="page-container">
        <header class="page-header">
            <div class="container">
                <h1 class="fade-in-up">Produk Kami</h1>
            </div>
        </header>

        <main class="page-content">
            <section class="products-section">
                <div class="container">
                    <div class="products-intro fade-in-up">
                        <h2>Kategori Produk</h2>
                    </div>

                    <div class="product-categories">
                        <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                            <div class="category-card fade-in-up">
                                <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                                <p><small><?php echo $category['product_count']; ?> produk tersedia</small></p>
                                <button class="category-btn" data-category="<?php echo $category['category_key']; ?>">Lihat Produk</button>
                            </div>
                        <?php endwhile; ?>

                        <!-- Category Products Display Area -->
                        <div id="category-products-display" class="category-products-display">
                            <button class="close-category-btn" onclick="closeCategoryDisplay()">&times;</button>
                            <div class="category-header">
                                <h3 id="category-title">Kategori Produk</h3>
                                <p id="category-description">Deskripsi kategori</p>
                            </div>
                            <div id="products-grid" class="products-grid">
                                <!-- Products will be dynamically loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>PT Waindo Specterra</h3>
                    <p>Total Solution for Digital Information</p>
                </div>
                <div class="footer-section">
                    <h4>Kontak</h4>
                    <p>Alamat : Kompleks Perkantoran Pejaten Raya #7-8 Jl. Pejaten Raya No.2 Jakarta Selatan 12510</p>
                    <p>Telepon : 021 7986816; 7986405</p>
                    <p>Fax : 021 7995539</p>
                    <p>Email : marketing@waindo.co.id</p>
                </div>
                <div class="footer-section">
                    <h4>Social</h4>
                    <p><a href="https://www.instagram.com/waindo_specterra?igshid=fysfd3j6l41n"><i class="fa-brands fa-instagram"></i> @waindo_specterra</a></p>
                    <p><a href="https://x.com/WSpecterra?s=08"><i class="fa-brands fa-twitter"></i> @WSpecterra</a></p>
                    <p><a href="https://www.instagram.com/waindo_specterra?igshid=fysfd3j6l41n"><i class="fa-brands fa-facebook"></i> @waindo_specterra</a></p>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/produk-db.js"></script>
    <script src="js/common.js"></script>
</body>

</html>