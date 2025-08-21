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

		.gallery-grid img {
			width: 100%;
			height: 220px;
			object-fit: cover;
			border-radius: 8px;
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
					<?php foreach ($photos as $p): ?>
						<img src="<?php echo htmlspecialchars($p); ?>" alt="Foto">
					<?php endforeach; ?>
				<?php else: ?>
					<p class="muted">Belum ada foto.</p>
				<?php endif; ?>
			</div>
		</div>
	</main>
</body>

</html>