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
    <link rel="stylesheet" href="assets/css/layanan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="page-container">
        <main class="page-content">
            <!-- Hero Section -->
            <section class="hero-section">
                <div class="container">
                    <h1>Layanan Kami</h1>
                </div>
            </section>

            <!-- Services Section -->
            <section class="services-section">
                <div class="container">

                    <!-- Category Tabs -->
                    <div class="category-tabs">
                        <button class="tab-button active" data-tab="foto-dan-lidar">Foto Udara dan Lidar</button>
                        <button class="tab-button" data-tab="survey">Survey</button>
                        <button class="tab-button" data-tab="tematik">Tematik</button>
                        <button class="tab-button" data-tab="training">Training</button>
                        <button class="tab-button" data-tab="software">Software Development</button>
                    </div>

                    <!-- Content Area -->
                    <div class="content-area">
                        <div id="foto-dan-lidar" class="tab-content active">
                            <div class="service-items">
                                <div class="service-item">
                                    <div class="service-image">
                                        <img src="assets/Lidar.png" alt="" style="width: 100%; height:100%;">
                                    </div>
                                    <div class="service-content">
                                        <h3>Pengambilan & Pengolahan Data LIDAR</h3>
                                        <ul class="service-features">
                                            <li>Data Raw LIDAR</li>
                                            <li>Pengolahan dan Pengklasifikasian Point Clouds LIDAR</li>
                                            <li>Digital Surface Model (DSM)</li>
                                            <li>Digital Terrain Model (DTM)</li>
                                            <li>LIDAR Intensity Images</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="service-item">
                                    <div class="service-content">
                                        <h3>Pengambilan & Pengolahan Foto Udara Digital</h3>
                                        <ul class="service-features">
                                            <li>Triangulasi Udara</li>
                                            <li>Stereomodel</li>
                                            <li>Mosaik Orthophoto</li>
                                        </ul>
                                    </div>
                                    <div class="service-image">
                                        <img src="assets/Data-Lidar.png" alt="" style="width: 100%; height:100%;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="survey" class="tab-content">
                            <div class="service-items">
                                <div class="service-item">
                                    <div class="service-image">
                                        <img src="assets/LandSurvey.png" alt="" style="width: 100%; height:100%;">
                                    </div>
                                    <div class="service-content">
                                        <h3>Survey – Hydrografi dan Terestrial dengan GPS dan 3D Mobile System</h3>
                                        <ul class="service-features">
                                            <li>Survey karakteristik perairan, danau dan sungai</li>
                                            <li>Survey Topografi</li>
                                            <li>Survey Toponimi Wilayah</li>
                                            <li>Road Survey</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tematik" class="tab-content">
                            <div class="service-items">
                                <div class="service-item">
                                    <div class="service-image">
                                        <img src="assets/tematik1.png" alt="" style="width: 100%; height:100%;">
                                    </div>
                                    <div class="service-content">
                                        <h3>Sasaran dari kegiatan Pemetaan Penutup Lahan adalah :</h3>
                                        <p>1. lnformasi Geospasial Tematik Penutup Lahan skala 1 : 50.000 dalam format NLP dan seamless (Region, provinsi, kabupaten). <br>2. Buku Deskripsi Analisis Pembaruan Peta Penutup Lahan <br>3. Metadata Pembaruan Peta Penutup Lahan</p>
                                    </div>
                                </div>

                                <div class="service-item">
                                    <div class="service-content">
                                        <h3>Layout Peta Penutup Lahan Provinsi</h3>
                                        <p>1. Hasil digitasi data penutup lahan dilakukan interpolasi 3D.
                                            <br>2. Data yang digunakan untuk proses penutup lahan yaitu data DSM (Digital Surface Model) dan DTM (Digital Terrain Model)
                                            <br>3. Setelah dilakukan proses interpolasi akan menghasilkan data ketinggian sesuai vertek di data penutup lahan 2D
                                            <br>4. Setelah data sudah terisi semua nilai x,y dan z maka melakukan analisis 3D menggunakan extension 3D analyst dengan metode Interpolate shape.
                                            <br>5. Konversi data vektor 2D ke data 3D dengan metode pengambilan ketinggian (Z) dari data DEMNAS dan DTM.
                                        </p>
                                    </div>
                                    <div class="service-image">
                                        <img src="assets/tematik2.png" alt="" style="width: 100%; height:100%">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="training" class="tab-content">
                            <div class="service-items">
                                <div class="service-item">
                                    <div class="service-image">
                                        <img src="assets/Dispotrud.jpeg" alt="" style="width: 100%; height:100%">
                                    </div>
                                    <div class="service-content">
                                        <h3>Training Dispotrud</h3>
                                        <ul class="service-features">
                                            <li>Menggunakan Mobile Application untuk collecting data lapangan</li>
                                            <li>Pembuatan Database Dengan Menggunakan Arcgis Pro(Proses Digitasi, Editing dan Attribut dan pembuatan Geodatabase)</li>
                                            <li>Visualisasi 3D Menggunakan Arcgis Pro</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="software" class="tab-content">
                            <div class="service-items">
                                <div class="service-item">
                                    <div class="service-image">
                                        <img src="assets/ddsaplication.jpeg" alt="" style="width: 100%; height:100%">
                                    </div>
                                    <div class="service-content">
                                        <h3>DDS Application</h3>
                                    </div>
                                </div>

                                <div class="service-item">
                                    <div class="service-content">
                                        <h3>SIPETA Application</h3>
                                    </div>
                                    <div class="service-image">
                                        <img src="assets/sipeta.jpeg" alt="" style="width: 100%; height:100%">
                                    </div>
                                </div>

                                <div class="service-item">
                                    <div class="service-image">
                                        <img src="assets/webgis.jpeg" alt="" style="width: 100%; height:100%">
                                    </div>
                                    <div class="service-content">
                                        <h3>WebGIS & Mobile Apps</h3>
                                    </div>
                                </div>
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
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked button
                button.classList.add('active');

                // Show corresponding content
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
            } else {
                navbar.style.background = '#fff';
                navbar.style.backdropFilter = 'none';
            }
        });
    </script>
</body>

</html>