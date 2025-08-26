<?php
session_start();
require_once 'connect/koneksi.php';

// Fetch dynamic content
$q_kegiatan = "SELECT k.*, (SELECT gf.foto FROM kegiatan_foto gf WHERE gf.kegiatan_id=k.kegiatan_id LIMIT 1) AS cover FROM kegiatan k ORDER BY k.created_at DESC LIMIT 6";
$kegiatan = mysqli_query($conn, $q_kegiatan);

$q_webinar = "SELECT * FROM webinar ORDER BY created_at DESC LIMIT 6";
$webinar = mysqli_query($conn, $q_webinar);

$q_live = "SELECT * FROM live_streaming ORDER BY created_at DESC LIMIT 4";
$live = mysqli_query($conn, $q_live);

$q_galeri = "SELECT g.*, (SELECT gf.foto FROM galeri_foto gf WHERE gf.galeri_id=g.galeri_id LIMIT 1) AS cover FROM galeri g ORDER BY g.created_at DESC LIMIT 8";
$galeri = mysqli_query($conn, $q_galeri);
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PT Waindo Specterra</title>
	<link rel="stylesheet" href="assets/css/common.css">
	<link rel="stylesheet" href="assets/css/berita.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel="stylesheet" href="assets/css/navbar.css">
</head>

<body>

	<?php include 'includes/navbar.php'; ?>
	<main>
		<section class="page-header">
			<div class="container">
				<h1>Berita & Informasi</h1>
			</div>
		</section>
		<div class="container berita-wrapper">
			<div class="tabs" role="tablist" aria-label="Kategori Berita">
				<button class="tab active" data-tab="kegiatan" role="tab" aria-selected="true">Kegiatan</button>
				<button class="tab" data-tab="webinar" role="tab" aria-selected="false">Webinar</button>
				<button class="tab" data-tab="live" role="tab" aria-selected="false">Live Streaming</button>
				<button class="tab" data-tab="galeri" role="tab" aria-selected="false">Galeri Foto</button>
			</div>

			<div class="tab-panels">
				<!-- Kegiatan -->
				<section id="panel-kegiatan" class="tab-panel active" aria-labelledby="kegiatan" role="tabpanel">
					<h2 class="section-title">Kegiatan</h2>
					<div class="news-grid">
						<?php if ($kegiatan && mysqli_num_rows($kegiatan) > 0): ?>
							<?php while ($row = mysqli_fetch_assoc($kegiatan)): ?>
								<article class="news-card fade-in-up">
									<img src="<?php echo htmlspecialchars($row['cover'] ?: 'assets/slider1.jpeg'); ?>" alt="<?php echo htmlspecialchars($row['judul']); ?>">
									<div class="news-card-body">
										<div class="news-card-title"><?php echo htmlspecialchars($row['judul']); ?></div>
										<div class="news-card-meta">Kegiatan • <?php echo date('d M Y', strtotime($row['created_at'])); ?></div>
										<p class="news-card-desc"><?php echo htmlspecialchars(mb_strimwidth((string)$row['deskripsi'], 0, 140, '...')); ?></p>
										<div class="card-actions">
											<span class="badge">Kegiatan</span>
											<a href="galeri.php?type=kegiatan&id=<?php echo (int)$row['kegiatan_id']; ?>" class="btn-secondary">Lihat Galeri</a>
										</div>
									</div>
								</article>
							<?php endwhile;
						else: ?>
							<p class="muted">Belum ada kegiatan.</p>
						<?php endif; ?>
					</div>
				</section>

				<!-- Webinar -->
				<section id="panel-webinar" class="tab-panel" aria-labelledby="webinar" role="tabpanel">
					<h2 class="section-title">Webinar</h2>
					<div class="webinar-grid">
						<?php if ($webinar && mysqli_num_rows($webinar) > 0): ?>
							<?php while ($row = mysqli_fetch_assoc($webinar)): ?>
								<div class="webinar-card fade-in-up">
									<div class="webinar-title"><?php echo htmlspecialchars($row['judul']); ?></div>
									<?php if (!empty($row['gambar'])): ?>
										<p class="webinar-desc"><img src="<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['judul']); ?>" style="max-width:100%; height:auto"></p>
									<?php endif; ?>
									<p class="muted">Diterbitkan: <?php echo date('d M Y', strtotime($row['created_at'])); ?></p>
								</div>
							<?php endwhile;
						else: ?>
							<p class="muted">Belum ada webinar.</p>
						<?php endif; ?>
					</div>
				</section>

				<!-- Live Streaming -->
				<section id="panel-live" class="tab-panel" aria-labelledby="live" role="tabpanel">
					<h2 class="section-title">Live Streaming</h2>
					<div class="live-wrapper">
						<?php if ($live && mysqli_num_rows($live) > 0): ?>
							<?php while ($row = mysqli_fetch_assoc($live)): ?>
								<div class="live-card fade-in-up">
									<?php if ($row['tipe'] === 'mp4'): ?>
										<video controls style="width:100%; height:300px;">
											<source src="<?php echo htmlspecialchars($row['url']); ?>" type="video/mp4">
										</video>
									<?php else: ?>
										<iframe src="<?php echo htmlspecialchars($row['url']); ?>" title="<?php echo htmlspecialchars($row['judul']); ?>" allowfullscreen></iframe>
									<?php endif; ?>
									<div class="live-body">
										<div class="news-card-title"><?php echo htmlspecialchars($row['judul']); ?></div>
									</div>
								</div>
							<?php endwhile;
						else: ?>
							<p class="muted">Belum ada live streaming.</p>
						<?php endif; ?>
					</div>
				</section>

				<!-- Galeri -->
				<section id="panel-galeri" class="tab-panel" aria-labelledby="galeri" role="tabpanel">
					<h2 class="section-title">Galeri Foto</h2>
					<div class="gallery-grid">
						<?php if ($galeri && mysqli_num_rows($galeri) > 0): ?>
							<?php while ($row = mysqli_fetch_assoc($galeri)): ?>
								<article class="gallery-card fade-in-up">
									<img src="<?php echo htmlspecialchars($row['cover'] ?: 'assets/slider1.jpeg'); ?>"
										alt="<?php echo htmlspecialchars($row['judul']); ?>">
									<div class="gallery-card-body">
										<div class="news-card-title"><?php echo htmlspecialchars($row['judul']); ?></div>
										<div class="news-card-meta">Tanggal : <?php echo date('d M Y', strtotime($row['created_at'])); ?></div>
										<div class="card-actions">
											<a href="galeri.php?type=galeri&id=<?php echo (int)$row['galeri_id']; ?>"
												class="btn-secondary">Lihat Semua Foto</a>
										</div>
									</div>
								</article>
							<?php endwhile; ?>
						<?php else: ?>
							<p class="muted">Belum ada galeri.</p>
						<?php endif; ?>
					</div>
				</section>
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
	<script>
		// Tab switching
		(function() {
			const tabButtons = document.querySelectorAll('.tab');
			const panels = {
				kegiatan: document.getElementById('panel-kegiatan'),
				webinar: document.getElementById('panel-webinar'),
				live: document.getElementById('panel-live'),
				galeri: document.getElementById('panel-galeri')
			};

			tabButtons.forEach(btn => {
				btn.addEventListener('click', () => {
					const target = btn.getAttribute('data-tab');

					tabButtons.forEach(b => {
						b.classList.remove('active');
						b.setAttribute('aria-selected', 'false');
					});
					Object.values(panels).forEach(p => p.classList.remove('active'));

					btn.classList.add('active');
					btn.setAttribute('aria-selected', 'true');
					if (panels[target]) panels[target].classList.add('active');
				});
			});
		})();

		// Webinar email check + download (UI only)
		(function() {
			const isValidEmail = window.validateEmail || function(email) {
				const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				return re.test(email);
			};

			document.querySelectorAll('.webinar-card').forEach(card => {
				const input = card.querySelector('input[type="email"]');
				const checkBtn = card.querySelector('.js-check-email');
				const downloadBtn = card.querySelector('.js-download');
				const alertBox = card.querySelector('.js-alert');

				checkBtn && checkBtn.addEventListener('click', () => {
					const email = (input && input.value || '').trim();
					if (!alertBox || !downloadBtn) return;
					alertBox.style.display = 'none';
					downloadBtn.disabled = true;

					if (!isValidEmail(email)) {
						alertBox.textContent = 'Format email tidak valid.';
						alertBox.style.display = 'block';
						return;
					}

					if (email.toLowerCase().endsWith('@waindo.co.id')) {
						downloadBtn.disabled = false;
						if (window.showSuccessMessage) {
							window.showSuccessMessage('Email terverifikasi. Silakan unduh sertifikat.');
						}
					} else {
						alertBox.textContent = 'Maaf email anda tidak tersedia !!';
						alertBox.style.display = 'block';
					}
				});

				downloadBtn && downloadBtn.addEventListener('click', () => {
					if (downloadBtn.disabled) return;
					if (window.showSuccessMessage) {
						window.showSuccessMessage('Mengunduh sertifikat (demo)...');
					}
				});
			});
		})();
	</script>
</body>

</html>