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