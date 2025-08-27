<?php
session_start();
require_once 'connect/koneksi.php';

$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!in_array($type, ['kegiatan'], true) || $id <= 0) {
	header('Location: berita.php');
	exit();
}

$detail = null;
$photoCount = 0;

// Ambil detail kegiatan
$query = "SELECT * FROM kegiatan WHERE kegiatan_id = $id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) === 1) {
	$detail = mysqli_fetch_assoc($result);
} else {
	header('Location: berita.php');
	exit();
}

// Hitung jumlah foto
$photoQuery = "SELECT COUNT(*) as total FROM kegiatan_foto WHERE kegiatan_id = $id";
$photoResult = mysqli_query($conn, $photoQuery);
if ($photoResult) {
	$photoRow = mysqli_fetch_assoc($photoResult);
	$photoCount = $photoRow['total'];
}

// Ambil satu foto sebagai cover (jika ada)
$coverPhoto = null;
$coverQuery = "SELECT foto FROM kegiatan_foto WHERE kegiatan_id = $id LIMIT 1";
$coverResult = mysqli_query($conn, $coverQuery);
if ($coverResult && mysqli_num_rows($coverResult) > 0) {
	$coverRow = mysqli_fetch_assoc($coverResult);
	$coverPhoto = $coverRow['foto'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo htmlspecialchars($detail['judul']); ?> - PT Waindo Specterra</title>
	<link rel="stylesheet" href="assets/css/common.css">
	<link rel="stylesheet" href="assets/css/berita.css">
	<link rel="stylesheet" href="assets/css/navbar.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<style>
		.detail-page {
			margin: 24px auto;
			max-width: 1100px;
			padding: 0 16px;
			margin-top: 100px;
		}

		.detail-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 30px;
			flex-wrap: wrap;
			gap: 16px;
		}

		.detail-header h1 {
			margin: 0;
			color: #333;
			flex: 1;
			min-width: 200px;
		}

		.btn-back {
			background: #6c757d;
			color: white;
			padding: 10px 20px;
			border-radius: 6px;
			text-decoration: none;
			font-size: 14px;
			transition: background-color 0.3s ease;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.btn-back:hover {
			background: #545b62;
			color: white;
		}

		.detail-content {
			background: white;
			border-radius: 12px;
			padding: 30px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			margin-bottom: 30px;
		}

		.detail-meta {
			display: flex;
			align-items: center;
			gap: 20px;
			margin-bottom: 25px;
			padding-bottom: 20px;
			border-bottom: 1px solid #eee;
			flex-wrap: wrap;
		}

		.meta-item {
			display: flex;
			align-items: center;
			gap: 8px;
			color: #666;
			font-size: 14px;
		}

		.meta-item i {
			color: #007bff;
		}

		.detail-cover {
			margin-bottom: 25px;
			text-align: center;
		}

		.detail-cover img {
			max-width: 100%;
			height: auto;
			max-height: 400px;
			border-radius: 8px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.1);
		}

		.detail-description {
			font-size: 16px;
			line-height: 1.8;
			color: #333;
			text-align: justify;
			margin-bottom: 30px;
		}

		.gallery-section {
			background: #f8f9fa;
			border-radius: 12px;
			padding: 25px;
			text-align: center;
		}

		.gallery-section h3 {
			margin: 0 0 15px 0;
			color: #333;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 10px;
		}

		.gallery-info {
			color: #666;
			margin-bottom: 20px;
			font-size: 15px;
		}

		.btn-gallery {
			background: #007bff;
			color: white;
			padding: 12px 30px;
			border-radius: 8px;
			text-decoration: none;
			font-weight: 500;
			font-size: 16px;
			transition: all 0.3s ease;
			display: inline-flex;
			align-items: center;
			gap: 10px;
			border: none;
			cursor: pointer;
		}

		.btn-gallery:hover {
			background: #0056b3;
			transform: translateY(-1px);
			box-shadow: 0 4px 15px rgba(0,123,255,0.3);
			color: white;
		}

		.btn-gallery:disabled {
			background: #ccc;
			cursor: not-allowed;
			transform: none;
			box-shadow: none;
		}

		.no-photos {
			color: #999;
			font-style: italic;
		}

		@media (max-width: 768px) {
			.detail-page {
				margin-top: 80px;
			}
			
			.detail-content {
				padding: 20px;
			}
			
			.detail-header {
				flex-direction: column;
				align-items: stretch;
			}
			
			.detail-header h1 {
				text-align: center;
				margin-bottom: 10px;
			}
			
			.btn-back {
				align-self: flex-start;
			}
			
			.detail-meta {
				flex-direction: column;
				align-items: flex-start;
				gap: 10px;
			}
			
			.gallery-section {
				padding: 20px;
			}
		}

		@media (max-width: 480px) {
			.detail-content {
				padding: 15px;
			}
			
			.gallery-section {
				padding: 15px;
			}
			
			.btn-gallery {
				width: 100%;
				justify-content: center;
			}
		}
	</style>
</head>

<body>
	<?php include 'includes/navbar.php'; ?>
	<main>
		<div class="detail-page">
			<div class="detail-header">
				<h1><?php echo htmlspecialchars($detail['judul']); ?></h1>
				<a href="berita.php" class="btn-back">
					<i class="fas fa-arrow-left"></i>
					Kembali ke Berita
				</a>
			</div>

			<div class="detail-content">
				<div class="detail-meta">
					<div class="meta-item">
						<i class="fas fa-calendar-alt"></i>
						<span><?php echo date('d F Y', strtotime($detail['created_at'])); ?></span>
					</div>
					<div class="meta-item">
						<i class="fas fa-tag"></i>
						<span>Kegiatan</span>
					</div>
					<?php if ($photoCount > 0): ?>
						<div class="meta-item">
							<i class="fas fa-images"></i>
							<span><?php echo $photoCount; ?> Foto</span>
						</div>
					<?php endif; ?>
				</div>

				<?php if ($coverPhoto): ?>
					<div class="detail-cover">
						<img src="<?php echo htmlspecialchars($coverPhoto); ?>" alt="<?php echo htmlspecialchars($detail['judul']); ?>">
					</div>
				<?php endif; ?>

				<div class="detail-description">
					<?php 
					// Convert line breaks to paragraphs
					$description = htmlspecialchars($detail['deskripsi']);
					$description = nl2br($description);
					echo $description;
					?>
				</div>

				<div class="gallery-section">
					<h3>
						<i class="fas fa-images"></i>
						Galeri Foto Kegiatan
					</h3>
					<?php if ($photoCount > 0): ?>
						<div class="gallery-info">
							Lihat semua <strong><?php echo $photoCount; ?> foto</strong> dari kegiatan ini
						</div>
						<a href="galeri.php?type=kegiatan&id=<?php echo $id; ?>" class="btn-gallery">
							<i class="fas fa-eye"></i>
							Lihat Galeri Lengkap
						</a>
					<?php else: ?>
						<div class="gallery-info no-photos">
							Belum ada foto untuk kegiatan ini
						</div>
						<button class="btn-gallery" disabled>
							<i class="fas fa-images"></i>
							Tidak Ada Foto
						</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</main>

	<footer class="footer">
		<div class="container">
			<div class="footer-content">
				<div class="footer-section">
					<h3>PT Waindo Specterra</h3>
					<p>Total Solution for Digital Information</p>
				</div>
				<div class="footer-section">
					<ul>
						<h4>Kontak</h4>
						<p>Alamat : Kompleks Perkantoran Pejaten Raya #7-8 Jl. Pejaten Raya No.2 Jakarta Selatan 12510</p>
						<p>Telepon : 021 7986816; 7986405</p>
						<p>Fax : 021 7995539</p>
						<p>Email : marketing@waindo.co.id</p>
					</ul>
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

	<script src="js/common.js"></script>
</body>

</html>