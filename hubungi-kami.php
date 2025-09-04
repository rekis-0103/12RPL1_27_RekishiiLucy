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
    <link rel="stylesheet" href="assets/css/hubungi-kami.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="page-container">
        <main class="page-content">
            <section class="page-header">
                <h1>Hubungi Kami</h1>
            </section>

            <section class="contact-section">
                <div class="container">
                    <div class="contact-grid">
                        <div class="contact-info">
                            <h2>Informasi Kontak</h2>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <h3>Alamat</h3>
                                    <p>Kompleks Perkantoran Pejaten Raya #7-8<br>Jl. Pejaten Raya No.2<br>Jakarta Selatan 12510</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone-alt"></i>
                                <div>
                                    <h3>Telepon</h3>
                                    <p>021 7986816<br>021 7986405</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-fax"></i>
                                <div>
                                    <h3>Fax</h3>
                                    <p>021 7995539</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <h3>Email</h3>
                                    <p>marketing@waindo.co.id</p>
                                </div>
                            </div>
                        </div>

                        <div class="map-container">
                            <h2>Lokasi Kami</h2>
                            <div class="map-wrapper">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.883109557217!2d106.82791254902115!3d-6.279094963186734!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f2159d15deb5%3A0x9f93bdef44698226!2sWaindo+Specterra+Indonesia.+PT!5e0!3m2!1sen!2sid!4v1497328108388" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
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
</body>

</html>