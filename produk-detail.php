<?php
$id = isset($_GET['id']) ? preg_replace('/[^a-z0-9\-]/i', '', $_GET['id']) : '';
$title = $id ? ucwords(str_replace('-', ' ', $id)) : 'Detail Produk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($title) ?> - PT Waindo Specterra</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel="stylesheet" href="assets/css/common.css">
	<link rel="stylesheet" href="assets/css/navbar.css">
	<link rel="stylesheet" href="assets/css/pages.css">
	<link rel="stylesheet" href="assets/css/produk.css">
</head>
<body class="produk-detail-page">
	<?php include 'includes/navbar.php'; ?>

	<div class="page-container">
		<header class="page-header">
			<div class="container">
				<h1 class="fade-in-up" id="product-title"><?= htmlspecialchars($title) ?></h1>
				<p class="fade-in-up">Detail informasi produk terkait kategori dan spesifikasi.</p>
			</div>
		</header>

		<main class="page-content">
			<section class="products-section">
				<div class="container">
					<!-- Product not found message -->
					<div id="product-not-found" class="product-not-found" style="display: none;">
						<div class="not-found-content">
							<i class="fas fa-exclamation-triangle"></i>
							<h2>Produk Tidak Ditemukan</h2>
							<p>Maaf, produk yang Anda cari tidak tersedia.</p>
							<a href="produk.php" class="btn-primary">Kembali ke Produk</a>
						</div>
					</div>

					<!-- Product detail container -->
					<div id="product-detail-container" class="product-detail-container">
						<!-- Content will be loaded by JavaScript -->
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
			<div class="footer-bottom">
				<p>&copy; 2024 PT Waindo Specterra. All rights reserved.</p>
			</div>
		</div>
	</footer>

	<script src="js/produk.js"></script>
	<script src="js/produk-detail.js"></script>
	<script src="js/common.js"></script>
</body>
</html>