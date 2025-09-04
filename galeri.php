<?php
session_start();
require_once 'connect/koneksi.php';

$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!in_array($type, ['kegiatan', 'galeri'], true) || $id <= 0) {
	header('Location: berita.php');
	exit();
}

$title = 'Galeri Foto';
$photos = [];

if ($type === 'kegiatan') {
	$head = mysqli_query($conn, "SELECT judul FROM kegiatan WHERE kegiatan_id=$id");
	if ($head && mysqli_num_rows($head) === 1) {
		$row = mysqli_fetch_assoc($head);
		$title = 'Galeri: ' . $row['judul'];
	}
	$res = mysqli_query($conn, "SELECT foto FROM kegiatan_foto WHERE kegiatan_id=$id ORDER BY foto_id ASC");
	while ($res && $r = mysqli_fetch_assoc($res)) {
		$photos[] = $r['foto'];
	}
} else {
	$head = mysqli_query($conn, "SELECT judul FROM galeri WHERE galeri_id=$id");
	if ($head && mysqli_num_rows($head) === 1) {
		$row = mysqli_fetch_assoc($head);
		$title = 'Galeri: ' . $row['judul'];
	}
	$res = mysqli_query($conn, "SELECT foto FROM galeri_foto WHERE galeri_id=$id ORDER BY foto_id ASC");
	while ($res && $r = mysqli_fetch_assoc($res)) {
		$photos[] = $r['foto'];
	}
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo htmlspecialchars($title); ?> - PT Waindo Specterra</title>
	<link rel="stylesheet" href="assets/css/common.css">
	<link rel="stylesheet" href="assets/css/berita.css">
	<link rel="stylesheet" href="assets/css/navbar.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<style>
		.gallery-page {
			margin: 24px auto;
			max-width: 1100px;
			padding: 0 16px;
			margin-top: 100px;
		}

		.gallery-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 16px;
		}

		.gallery-grid {
			display: grid;
			grid-template-columns: repeat(4, 1fr);
			gap: 12px;
		}

		.gallery-item {
			cursor: pointer;
			transition: transform 0.2s ease;
		}

		.gallery-item:hover {
			transform: scale(1.05);
		}

		.gallery-grid img {
			width: 100%;
			height: 220px;
			object-fit: cover;
			border-radius: 8px;
			display: block;
		}

		/* Lightbox Styles */
		.lightbox {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.9);
			z-index: 9999;
			align-items: center;
			justify-content: center;
		}

		.lightbox.active {
			display: flex;
		}

		.lightbox-content {
			position: relative;
			max-width: 90%;
			max-height: 90%;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.lightbox img {
			max-width: 100%;
			max-height: 100%;
			object-fit: contain;
			border-radius: 8px;
		}

		.lightbox-close {
			position: absolute;
			top: 20px;
			right: 30px;
			font-size: 40px;
			font-weight: bold;
			color: white;
			cursor: pointer;
			z-index: 10001;
			background: rgba(0, 0, 0, 0.5);
			width: 50px;
			height: 50px;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: background-color 0.3s ease;
		}

		.lightbox-close:hover {
			background: rgba(0, 0, 0, 0.8);
		}

		.lightbox-nav {
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			font-size: 30px;
			font-weight: bold;
			color: white;
			cursor: pointer;
			background: rgba(0, 0, 0, 0.5);
			width: 50px;
			height: 50px;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: background-color 0.3s ease;
			user-select: none;
		}

		.lightbox-nav:hover {
			background: rgba(0, 0, 0, 0.8);
		}

		.lightbox-prev {
			left: 30px;
		}

		.lightbox-next {
			right: 30px;
		}

		.lightbox-counter {
			position: absolute;
			bottom: 20px;
			left: 50%;
			transform: translateX(-50%);
			color: white;
			background: rgba(0, 0, 0, 0.5);
			padding: 10px 20px;
			border-radius: 20px;
			font-size: 14px;
		}

		@media (max-width: 1024px) {
			.gallery-grid {
				grid-template-columns: repeat(3, 1fr);
			}
		}

		@media (max-width: 640px) {
			.gallery-grid {
				grid-template-columns: repeat(2, 1fr);
			}

			.lightbox-close {
				top: 10px;
				right: 15px;
				font-size: 30px;
				width: 40px;
				height: 40px;
			}

			.lightbox-nav {
				font-size: 25px;
				width: 40px;
				height: 40px;
			}

			.lightbox-prev {
				left: 15px;
			}

			.lightbox-next {
				right: 15px;
			}
		}
	</style>
</head>

<body>
	<?php include 'includes/navbar.php'; ?>
	<main>
		<div class="gallery-page">
			<div class="gallery-header">
				<h1><?php echo htmlspecialchars($title); ?></h1>
				<a href="berita.php" class="btn-secondary">Kembali</a>
			</div>
			<div class="gallery-grid">
				<?php if (count($photos) > 0): ?>
					<?php foreach ($photos as $index => $p): ?>
						<div class="gallery-item" onclick="openLightbox(<?php echo $index; ?>)">
							<img src="<?php echo htmlspecialchars($p); ?>" alt="Foto <?php echo $index + 1; ?>">
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<p class="muted">Belum ada foto.</p>
				<?php endif; ?>
			</div>
		</div>

		<!-- Lightbox -->
		<div id="lightbox" class="lightbox" onclick="closeLightbox(event)">
			<span class="lightbox-close" onclick="closeLightbox()">&times;</span>
			<span class="lightbox-nav lightbox-prev" onclick="changeImage(-1)">&#10094;</span>
			<div class="lightbox-content">
				<img id="lightbox-img" src="" alt="Foto">
			</div>
			<span class="lightbox-nav lightbox-next" onclick="changeImage(1)">&#10095;</span>
			<div class="lightbox-counter">
				<span id="current-image">1</span> / <span id="total-images"><?php echo count($photos); ?></span>
			</div>
		</div>
	</main>

	<script>
		const photos = <?php echo json_encode($photos); ?>;
		let currentImageIndex = 0;

		function openLightbox(index) {
			currentImageIndex = index;
			const lightbox = document.getElementById('lightbox');
			const lightboxImg = document.getElementById('lightbox-img');
			const currentImageSpan = document.getElementById('current-image');

			lightboxImg.src = photos[index];
			currentImageSpan.textContent = index + 1;
			lightbox.classList.add('active');

			// Prevent body scroll
			document.body.style.overflow = 'hidden';
		}

		function closeLightbox(event) {
			// Only close if clicking on the overlay, not on the image or navigation
			if (event && event.target !== event.currentTarget) {
				return;
			}

			const lightbox = document.getElementById('lightbox');
			lightbox.classList.remove('active');

			// Restore body scroll
			document.body.style.overflow = 'auto';
		}

		function changeImage(direction) {
			currentImageIndex += direction;

			// Loop around if at the beginning or end
			if (currentImageIndex < 0) {
				currentImageIndex = photos.length - 1;
			} else if (currentImageIndex >= photos.length) {
				currentImageIndex = 0;
			}

			const lightboxImg = document.getElementById('lightbox-img');
			const currentImageSpan = document.getElementById('current-image');

			lightboxImg.src = photos[currentImageIndex];
			currentImageSpan.textContent = currentImageIndex + 1;
		}

		// Keyboard navigation
		document.addEventListener('keydown', function(event) {
			const lightbox = document.getElementById('lightbox');
			if (lightbox.classList.contains('active')) {
				switch (event.key) {
					case 'Escape':
						closeLightbox();
						break;
					case 'ArrowLeft':
						changeImage(-1);
						break;
					case 'ArrowRight':
						changeImage(1);
						break;
				}
			}
		});

		// Prevent context menu on images
		document.addEventListener('contextmenu', function(e) {
			if (e.target.tagName === 'IMG') {
				e.preventDefault();
			}
		});
	</script>
</body>

</html>