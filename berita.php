<?php
session_start();
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
						<article class="news-card fade-in-up">
							<img src="assets/slider1.jpeg" alt="Outing & Family Gathering">
							<div class="news-card-body">
								<div class="news-card-title">Kegiatan Outing dan Family Gathering</div>
								<div class="news-card-meta">Internal • Kebersamaan</div>
								<p class="news-card-desc">Family day sebagai bentuk apresiasi perusahaan untuk mempererat hubungan antar karyawan dan keluarga sekaligus menyegarkan kembali semangat bekerja.</p>
								<div class="card-actions">
									<span class="badge">Kegiatan</span>
									<a href="#" class="btn-secondary">Lihat Galeri</a>
								</div>
							</div>
						</article>

						<article class="news-card fade-in-up">
							<img src="assets/slider2.png" alt="Halal Bi Halal Virtual">
							<div class="news-card-body">
								<div class="news-card-title">Halal Bi Halal Virtual saat Pandemi</div>
								<div class="news-card-meta">Internal • Silaturahmi</div>
								<p class="news-card-desc">Momen saling bermaafan secara virtual dengan tausiyah bermakna demi menjaga kebersamaan di masa pandemi.</p>
								<div class="card-actions">
									<span class="badge">Kegiatan</span>
									<a href="#" class="btn-secondary">Lihat Galeri</a>
								</div>
							</div>
						</article>

						<article class="news-card fade-in-up">
							<img src="assets/remotesesing.jpg" alt="Pembagian Sembako">
							<div class="news-card-body">
								<div class="news-card-title">Pembagian Sembako</div>
								<div class="news-card-meta">Support • Kepedulian</div>
								<p class="news-card-desc">Dukungan perusahaan untuk karyawan dan keluarga melalui program pembagian sembako serta voucher belanja.</p>
								<div class="card-actions">
									<span class="badge">Kegiatan</span>
									<a href="#" class="btn-secondary">Lihat Galeri</a>
								</div>
							</div>
						</article>

						<article class="news-card fade-in-up">
							<img src="assets/lidar1.jpg" alt="Berbagi Ramadhan & Santunan">
							<div class="news-card-body">
								<div class="news-card-title">Berbagi Ramadhan & Santunan Anak Yatim</div>
								<div class="news-card-meta">CSR • Ramadhan</div>
								<p class="news-card-desc">Kegiatan rutin membagikan makanan berbuka untuk anak yatim dan dukungan untuk masjid di lingkungan karyawan.</p>
								<div class="card-actions">
									<span class="badge">Kegiatan</span>
									<a href="#" class="btn-secondary">Lihat Galeri</a>
								</div>
							</div>
						</article>

						<article class="news-card fade-in-up">
							<img src="assets/topograpsurvey.jpg" alt="Program Idul Adha">
							<div class="news-card-body">
								<div class="news-card-title">Program Idul Adha</div>
								<div class="news-card-meta">Keagamaan • Tahunan</div>
								<p class="news-card-desc">Program kurban bergilir bagi karyawan sebagai wujud kebersamaan dan rasa syukur.</p>
								<div class="card-actions">
									<span class="badge">Kegiatan</span>
									<a href="#" class="btn-secondary">Lihat Galeri</a>
								</div>
							</div>
						</article>

						<article class="news-card fade-in-up">
							<img src="assets/hidrograpsurvey1.jpg" alt="Olahraga Karyawan">
							<div class="news-card-body">
								<div class="news-card-title">Olahraga Karyawan</div>
								<div class="news-card-meta">Wellness • Rutin</div>
								<p class="news-card-desc">Men Sana in Corpore Sano. Program olahraga untuk menjaga kebugaran dan meningkatkan produktivitas.</p>
								<div class="card-actions">
									<span class="badge">Kegiatan</span>
									<a href="#" class="btn-secondary">Lihat Galeri</a>
								</div>
							</div>
						</article>
					</div>
				</section>

				<!-- Webinar -->
				<section id="panel-webinar" class="tab-panel" aria-labelledby="webinar" role="tabpanel">
					<h2 class="section-title">Webinar</h2>
					<div class="webinar-grid">
						<div class="webinar-card fade-in-up" data-webinar="1">
							<div class="webinar-title">Webinar Waindo Series #1 — GIS Enterprise & Dashboard, CSRT, Airborne LiDAR</div>
							<p class="webinar-desc">Bahasan implementasi GIS enterprise, operasi dashboard, teknologi CSRT, serta pemanfaatan LiDAR.</p>
							<div class="webinar-form">
								<input type="email" placeholder="Email — masukkan alamat email" aria-label="Email Webinar 1">
								<button class="btn-secondary js-check-email">Cek Email Kembali</button>
								<button class="btn-primary js-download" disabled>Download Sertifikat</button>
							</div>
							<div class="alert alert-danger js-alert" style="display:none;">Maaf email anda tidak tersedia !!</div>
						</div>

						<div class="webinar-card fade-in-up" data-webinar="2">
							<div class="webinar-title">Webinar Waindo Series #2 — Pembuatan Peta 3D Menggunakan ArcGIS PRO</div>
							<p class="webinar-desc">Mendalami workflow pembuatan peta 3D dari data spasial menggunakan ArcGIS Pro.</p>
							<div class="webinar-form">
								<input type="email" placeholder="Email — masukkan alamat email" aria-label="Email Webinar 2">
								<button class="btn-secondary js-check-email">Cek Email Kembali</button>
								<button class="btn-primary js-download" disabled>Download Sertifikat</button>
							</div>
							<div class="alert alert-danger js-alert" style="display:none;">Maaf email anda tidak tersedia !!</div>
						</div>

						<div class="webinar-card fade-in-up" data-webinar="3">
							<div class="webinar-title">Webinar Waindo Series #3 — Low Cost GNSS for Surveying & Monitoring</div>
							<p class="webinar-desc">Update teknologi GNSS biaya rendah dan penerapannya untuk survei dan monitoring.</p>
							<div class="webinar-form">
								<input type="email" placeholder="Email — masukkan alamat email" aria-label="Email Webinar 3">
								<button class="btn-secondary js-check-email">Cek Email Kembali</button>
								<button class="btn-primary js-download" disabled>Download Sertifikat</button>
							</div>
							<div class="alert alert-danger js-alert" style="display:none;">Maaf email anda tidak tersedia !!</div>
						</div>
					</div>
				</section>

				<!-- Live Streaming -->
				<section id="panel-live" class="tab-panel" aria-labelledby="live" role="tabpanel">
					<h2 class="section-title">Live Streaming</h2>
					<div class="live-wrapper">
						<div class="live-card fade-in-up">
							<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Live 1" allowfullscreen></iframe>
							<div class="live-body">
								<div class="news-card-title">Live Talk: Transformasi Digital Geospasial</div>
							</div>
						</div>
						<div class="live-card fade-in-up">
							<iframe src="https://www.youtube.com/embed/oHg5SJYRHA0" title="Live 2" allowfullscreen></iframe>
							<div class="live-body">
								<div class="news-card-title">Live Demo: Workflow LiDAR ke Peta 3D</div>
							</div>
						</div>
					</div>
				</section>

				<!-- Galeri -->
				<section id="panel-galeri" class="tab-panel" aria-labelledby="galeri" role="tabpanel">
					<h2 class="section-title">Galeri Foto</h2>
					<div class="gallery-grid">
						<img src="assets/Geograpic-Information-System.jpg" alt="GIS Enterprise">
						<img src="assets/lidar1.jpg" alt="LiDAR">
						<img src="assets/topograpsurvey.jpg" alt="Topographic Survey">
						<img src="assets/hidrograpsurvey1.jpg" alt="Hidrography Survey">
						<img src="assets/slider1.jpeg" alt="Family Day">
						<img src="assets/slider2.png" alt="Virtual Event">
						<img src="assets/remotesesing.jpg" alt="Remote Sensing">
						<img src="assets/Fotogrametri.jpg" alt="Fotogrametri">
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

				checkBtn.addEventListener('click', () => {
					const email = (input.value || '').trim();
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

				downloadBtn.addEventListener('click', () => {
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