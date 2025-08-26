<?php
session_start();
require_once 'connect/koneksi.php';

// Query untuk mengambil 3 berita terbaru dari semua kategori
$query_berita_terbaru = "
    SELECT * FROM (
        SELECT 
            'kegiatan' as kategori,
            kegiatan_id as id,
            judul,
            COALESCE(deskripsi, '') as deskripsi,
            created_at,
            '' as gambar
        FROM kegiatan
        UNION ALL
        SELECT 
            'webinar' as kategori,
            webinar_id as id,
            judul,
            '' as deskripsi,
            created_at,
            COALESCE(gambar, '') as gambar
        FROM webinar
        UNION ALL
        SELECT 
            'live' as kategori,
            streaming_id as id,
            judul,
            '' as deskripsi,
            created_at,
            '' as gambar
        FROM live_streaming
        UNION ALL
        SELECT 
            'galeri' as kategori,
            galeri_id as id,
            judul,
            '' as deskripsi,
            created_at,
            '' as gambar
        FROM galeri
    ) as combined_news
    ORDER BY created_at DESC
    LIMIT 3
";

$berita_terbaru = mysqli_query($conn, $query_berita_terbaru);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Waindo Specterra</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <section class="hero-slider">
            <div class="slide active">
                <img src="assets/slider1.jpeg" alt="Business Innovation">
                <div class="slide-content">
                    <h1>PT Waindo Specterra</h1>
                </div>
            </div>

            <div class="slide">
                <img src="assets/slider2.png" alt="Technology Solutions">
                <div class="slide-content">
                    <h1>Total Solution for Digital Information</h1>
                </div>
            </div>

            <a class="prev">&#10094;</a>
            <a class="next">&#10095;</a>

            <div class="slider-controls">
                <span class="slider-dot active"></span>
                <span class="slider-dot"></span>
            </div>
        </section>

        <div class="visi">
            <div>
                <h2>VISI</h2>
                <p>Menjadi Perusahaan Informasi Geospasial yang memiliki keunggulan <br>
                kompetitif dan pelayanan yang berkualitas di pasar global.</p>
            </div>
            <img src="assets/visi.jpg" alt="">
        </div>
        <div class="misi">
            <img src="assets/misi.jpg" alt="">
            <div>
                <h2>MISI</h2>
                <ol>
                    <li><p>Memberikan pelayanan jasa yang prefesional, objektif, <br> efesien, efektif dan bertanggung jawab.</p></li>
                    <li><p>Memberikan solusi informasi geospasial yang didukung oleh <br> teknologi informasi dan digital terkini.</p></li>
                    <li><p>Meningkatkan kompetensi karyawan untuk mendukung <br> tercapainya kepuasan pelanggan.</p></li>
                </ol>
            </div>
        </div>
        
        <!-- Berita Terbaru Section -->
        <div class="berita-terbaru">
            <div class="container">
                <div class="section-header">
                    <h2>Berita & Informasi Terbaru</h2>
                    <p>Dapatkan informasi terbaru seputar kegiatan, webinar, live streaming, dan galeri foto kami</p>
                </div>
                
                <div class="berita-grid">
                    <?php if ($berita_terbaru && mysqli_num_rows($berita_terbaru) > 0): ?>
                        <?php while ($berita = mysqli_fetch_assoc($berita_terbaru)): ?>
                            <div class="berita-card fade-in-up">
                                <div class="berita-image">
                                    <?php if (!empty($berita['gambar'])): ?>
                                        <img src="<?php echo htmlspecialchars($berita['gambar']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>">
                                    <?php else: ?>
                                        <div class="placeholder-image">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="kategori-badge">
                                        <?php 
                                        $kategori_label = '';
                                        $kategori_icon = '';
                                        switch($berita['kategori']) {
                                            case 'kegiatan':
                                                $kategori_label = 'Kegiatan';
                                                $kategori_icon = 'fas fa-calendar';
                                                break;
                                            case 'webinar':
                                                $kategori_label = 'Webinar';
                                                $kategori_icon = 'fas fa-chalkboard-teacher';
                                                break;
                                            case 'live':
                                                $kategori_label = 'Live Streaming';
                                                $kategori_icon = 'fas fa-video';
                                                break;
                                            case 'galeri':
                                                $kategori_label = 'Galeri';
                                                $kategori_icon = 'fas fa-images';
                                                break;
                                        }
                                        ?>
                                        <i class="<?php echo $kategori_icon; ?>"></i>
                                        <?php echo $kategori_label; ?>
                                    </div>
                                </div>
                                <div class="berita-content">
                                    <h3 class="berita-title"><?php echo htmlspecialchars($berita['judul']); ?></h3>
                                    <?php if (!empty($berita['deskripsi'])): ?>
                                        <p class="berita-desc"><?php echo htmlspecialchars(mb_strimwidth($berita['deskripsi'], 0, 120, '...')); ?></p>
                                    <?php endif; ?>
                                    <div class="berita-meta">
                                        <span class="berita-date">
                                            <i class="fas fa-clock"></i>
                                            <?php echo date('d M Y', strtotime($berita['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-berita">
                            <i class="fas fa-newspaper"></i>
                            <p>Belum ada berita terbaru</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="berita-actions">
                    <a href="berita.php" class="btn-primary">
                        <i class="fas fa-arrow-right"></i>
                        Lihat Semua Berita
                    </a>
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
    <script src="js/beranda.js"></script>
    <script src="js/navbar.js"></script>
</body>

</html>