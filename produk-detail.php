<!--<?php
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
	<link rel="stylesheet" href="assets/css/produk.css">
</head>
<body>
	<nav class="navbar">
		<div class="nav-container">
			<div class="nav-logo">
				<a href="index.php"><img src="assets/logo.png" alt="" style="width: 30vh"></a>
			</div>
			<ul class="nav-menu">
				<li><a href="index.php">Beranda</a></li>
				<li><a href="tentang-kami.php">Tentang Kami</a></li>
				<li><a href="produk.php" class="active">Produk</a></li>
				<li><a href="layanan.php">Layanan</a></li>
				<li><a href="mitra-kerja.php">Mitra Kerja</a></li>
				<li><a href="berita.php">Berita</a></li>
				<li><a href="hubungi-kami.php">Hubungi Kami</a></li>
				<li><a href="bergabung.php">Bergabung</a></li>
				<li><a href="login.php" class="login-btn">Login</a></li>
			</ul>
			<div class="hamburger">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
	</nav>

	<header class="page-header">
		<div class="container">
			<h1 class="fade-in-up"><?= htmlspecialchars($title) ?></h1>
			<p class="fade-in-up">Detail informasi produk terkait kategori dan spesifikasi.</p>
		</div>
	</header>

	<main>
		<section class="products-section">
			<div class="container">
				<div class="category-products-display active" style="display:block; opacity:1; transform:none;">
					<div class="category-header">
						<h3><?= htmlspecialchars($title) ?></h3>
						<p>Halaman detail untuk produk "<?= htmlspecialchars($title) ?>". Konten detail bisa diisi dari database nanti.</p>
					</div>
					<div class="products-grid">
						<div class="individual-product">
							<div class="product-image">
								<i class="fas fa-box-open"></i>
							</div>
							<div class="product-info">
								<h4 class="product-name"><?= htmlspecialchars($title) ?></h4>
								<p>Deskripsi produk placeholder. Tambahkan fitur, spesifikasi, dan gambar sesuai kebutuhan.</p>
								<a href="produk.php" class="product-detail-btn">Kembali ke Produk</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</main>

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

	<script src="js/common.js"></script>
</body>
</html>
-->