<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - PT Waindo Specterra</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/tentang-kami.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="page-container">
        <div class="page-header">
            <div class="container">
                <h1>Tentang Kami</h1>
            </div>
        </div>

        <section class="about-content">
            <div class="container">
                <div class="about-grid">
                    <div class="about-text">
                        <img src="assets/logo.png" alt="" style="width: 65%;">
                        <p>PT Waindo SpecTerra merupakan perusahaan nasional bergerak di bidang jasa konsultan
                            dalam memberikan solusi pengelolaan sumberdaya alam baik darat maupun laut yang
                            didukung oleh teknologi digital. Waindo SpecTerra didirikan pada tahun 1996 dengan komitmen
                            tinggi terhadap kualitas layanan</p>
                        <p>Waindo SpecTerra didukung oleh 150 staf dari berbagai keahlian dengan jenjang pendidikan
                            dari Universitas ternama dalam negeri serta memiliki sertifikat kompetensi keahlian sesuai
                            dengan bidang yang dikuasai. PT Waindo SpecTerra memiliki pengalaman kerja selama lebih
                            dari 25 tahun memiliki fasilitas studio pengolahan data geospasial dengan hardware dan
                            software terbaru. PT Waindo SpecTerra berlokasi di Pusat Pemerintahan dan Bisnis yaitu di
                            Jakarta</p>
                        <p>Dengan keahlian dan kapasitas yang dimiliki, PT Waindo SpecTera telah melayani dan
                            bekerjasama dengan berbagai lembaga baik nasional maupun internasional antara lain
                            Badan Informasi Geospasial, Badan Pertanahan Nasional, BP REDD+, Kementerian Pertanian,
                            Kementerian Kehutanan, Kementerian Pekerjaan Umum, Kementerian Perhubungan, Badan
                            Pengkajian dan Penerapan Teknologi, Dinas Tata Ruang DKI Jakarta, Bappeda Kabupaten Lubuk
                            Linggau, Millenium Challenge Account (MCA), USAID, UNDP, TOTAL EP Indonesie, PT Pertamina EP,
                            Perusahaan Gas Negara, Perhutani, PT Salim Ivomas Pratama Tbk, PT SMART, Tbk, PT Adaro
                            Indonesia, Universitas Gadjah Mada, Universitas Indonesia, Institut Pertanian Bogor, Universitas
                            Diponegoro dan lainnya.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="team">
            <div id="teamModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h3 id="modalName"></h3>
                    <p id="modalRole"></p>
                    <p id="modalDescription"></p>
                </div>
            </div>
            <div class="container">
                <h2>Tim Kami</h2>
                <div class="team-grid">
                    <div class="team-member"
                        data-description="Gunawan Jaya adalah pendiri sekaligus Direktur Utama PT Waindo Specterra dengan pengalaman lebih dari 25 tahun di bidang geospasial.">
                        <img src="assets/GunawanJaya.jpg" alt="Gunawan Jaya">
                        <h3>Gunawan Jaya</h3>
                        <p>Direktur Utama</p>
                    </div>
                    <div class="team-member"
                        data-description="Andreas Suradji lahir Semarang, 3 Maret 1951 yang juga merupakan Direktur Research & Development PT Waindo SpecTerra, Lulus pada Tahun 1980 dari S1 Teknik Geodesi, Universitas Gadjah Mada, memiliki pengalaman dan keahlian selama 37 tahun dalam bidang Geodesi, Geografi dan Fotogrammetry. Memiliki sertifikat keahlian Sebagai Ahli Utama Geodesi dari Lembaga Pengembangan Jasa Konstruksi (LPJK) dan terlibat dalam organisasi Ikatan Surveyor Indonesia (ISI) dan berpengalman dalam mengikuti pelatihan dan workshop baik didalam maupun di luar negeri. Selain aktivitas sebagai Direktur Research & Development juga teribat sebagai team leader dalam beberapa proyek, dengan spesialisasi pekerjaan di bidang Fotogrammetry (Foto Udara, LIDAR & UAV), dengan keahlian dan pengalaman yang dimiliki membantu PT Waindo SpecTerra dalam pengembangan & inovasi baik secara teknologi maupun pengolahan data.">
                        <img src="assets/p_radji.jpeg" alt="Andreas Suparjdi">
                        <h3>Andreas Suparjdi</h3>
                        <p>Fotogrametri Specialist</p>
                    </div>
                    <div class="team-member"
                        data-description="Pria yang akrab disapa Hanny ini Lahir di Jeddah, pada tanggal 25 Mei 1980. Ia meraih gelar sarjana Fakultas Teknik Informatika di Universitas Bina Nusantara tahun 2003.
Karena banyak mengikuti berbagai kegiatan dan keahliannya tentang IT dan pengetahuan tentang banyak software dan dipercaya menjadi Team Leader dan menjabat sebagai Manager menjadikan PT Waindo SpecTerra dapat di percaya membangun aplikasi di pemerintahan dan menjadikannya sebagai Software Development Spesialist.">
                        <img src="assets/hanny.jpeg" alt="Mahfuz Djamaluddin">
                        <h3>Mahfuz Djamaluddin</h3>
                        <p>Software Development Specialist</p>
                    </div>
                    <div class="team-member"
                        data-description="Faik terlahir di Kebumen tanggal 6 Agustus 1973, Meraih gelar Sarjana di fakultas Geografi Universitas Gadjah Mada tahun 1999.
Pengalamannya sebagai GIS Product Spesialist menjadikannya sangat mengenal kondisi perkembangan software dan mempertahankan waindo sebagai patner ESRI.">
                        <img src="assets/faik.jpeg" alt="Faik Sofyan">
                        <h3>Faik Sofyan</h3>
                        <p>GIS Product Specialist</p>
                    </div>
                    <div class="team-member"
                        data-description="Yust biasa panggilan akrabnya, adalah pria kelahiran Kebumen tanggal 11 Juli 1974, Gelar Sarjana Teknik diperoleh dari Jurusan Teknik Geodesi Fakultas Teknik, UGM, Pada tahun 1998. Selama karirnya banyak pekerjaan yang di tangani Beliau berkaitan dengan keilmuan dan kemampuan yang dimiliki. Jabatan Struktural yang pernah beliau pegang antara lain: Chief Survey (BSG Group), IT&GIS Manager (Ictindo MS), Manager Pemetaan Tematik (Waindo Group) dan Sejak Tahun 2018 di PT Waindo SpecTerra Indonesia saat ini menduduki posisi sebagai salah satu manager/koordinator. Kemampuannya telah disertifikasi dalam kompetensi keahlian di Bidang Sistem Informasi Geografis (SIG). Beberapa posisi baik sebagai Team Leader maupun Project Manager dan GIS Specialist, dan Teknis lainnya di beberapa kegiatan proyek baik sektor Pemerintah, BUMN maupun Swasta menjadikan Beliau semakin berpengalaman dalam Kegiatan Pekerjaan Jasa Konsultansi. Untuk Sertifikasi Keahlian Beliau antara lain: MPM Certified (My Learn IT Indonesia, Brainbench.com (PMBOK)), sejak Agustus 2001. Ahli Utama GIS (LSP ISI) - sejak tahun 2014. Ahli Utama Geodesi (SKA ISI), sejak tahun 2017. SKB/Surveyor Kadastral Berlisensi sejak 2017. Pengalaman Organisasi antara lain: BEM FT-UGM, Yogyakarta 1994-1995; Perwakilan Dewan IMGI dari Teknik Geodesi UGM,1995; KaSie Kerohanian Islam KMTG FT-UGM,1994-1995; Ketua Alumni SMANGB di UGM, Yogyakarta, 1995-1996. Ketua DKM Nururrahman K-PAS 15-RJB PanMas, DePoK. 2012-2015. Sebagai Sarjana Teknik, Beliau memiliki pengalaman di beberapa kegiatan pekerjaan/proyek antara lain: BKT di Kementerian PU, GIS IKM Kementerian Perindustrian dan Perdagangan, e-Procurement Kementerian Komunikasi dan Informasi, GIS Untuk Infomedia Nusantara, GIS MapInfo Siemens Indonesia, Mike 21-Etabs-etc for ITS, MapInfoMapX BacHealtCare, MapInfo MapExtreme GSS PT Astra International, MapInfo MapExtreme GIM PT Federal International Finance, Pembangunan e-Government Kabupaten Paser Kalimantan Timur, Pembangunan Aplikasi Gratifikasi dan Pengaduan Masyarakat KPK, LMDP/Land Registration Kementerian Koordinator Perencanaaan Pembangunan Nasional/BAPPENAS, Seismic Survey EMEPIL Jombang,GIS Data Conversion Total EPI-Balikpapan, RBI-25K BIG, RBI-5K BIG, SKB BPN Sanggau, Pembangunan Aplikasi webGIS SIG-RA Ditjen Penataan Agraria Kementerian ATRBPN, GISDUKCAPIL ArcGIS Kementerian Dalam Negeri, Pembangunan Aplikasi Pentabit Kementerian ATRBPN, Pembuatan Aplikasi RTR Builder Ditjen Tata Ruang Kementerian ATRBPN.">
                        <img src="assets/yus.jpg" alt="R Yustiono">
                        <h3>R. Yustiono</h3>
                        <p>GIS Specialist</p>
                    </div>
                    <div class="team-member"
                        data-description="Luwin kelahiran Bekasi tanggal 5 April 1978, selama menempuh pendidikan sarjana Fakultas Kehutanan di IPB tahun 2003 dan keahliannya sebagai team leader di berbagai.
Pekerjaan tematik membuat sebutan thematic spesialist cocok untuk gambaran karirnya dan pernah mempunyai karya buku yaitu Pemetaan Karakteristik Perairan Dangkal Teluk Tomini. Jakarta. 2013 dan Evaluasi Jaringan Jalan di Hutan Pendidikan Gunung Walat. Bogor. 2003 .">
                        <img src="assets/luwin.jpeg" alt="Luwin Eska Darwini">
                        <h3>Luwin Eska Darwini</h3>
                        <p>Thematic Specialist</p>
                    </div>
                    <div class="team-member"
                        data-description="Rian dilahirkan di Samarinda tanggal 15 Juni 1987 dan menempuh pendidikan Sarjana Teknik Geodesi di Universitas Gadjah Mada tahun 2009.
Keahliannya dalam bidang survey dan pekerjaan Lidar sudah banyak yang dapat selesaikan dengan baik dan membuat karirnya di posisikan sebagai manager fotogrametri di waindo dan banyak yang menyebutnya sebagaiu Fotogrametri Spesialist.">
                        <img src="assets/rian.jpeg" alt="Perdana Rian Juniarta">
                        <h3>Perdana Rian Juniarta</h3>
                        <p>Fotogrametri Specialist</p>
                    </div>
                </div>
            </div>
        </section>
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
        document.querySelectorAll('.team-member').forEach(member => {
            member.addEventListener('click', function() {
                const name = this.querySelector('h3').textContent;
                const role = this.querySelector('p').textContent;
                const description = this.getAttribute('data-description');

                document.getElementById('modalName').textContent = name;
                document.getElementById('modalRole').textContent = role;
                document.getElementById('modalDescription').textContent = description;

                document.getElementById('teamModal').style.display = 'block';
            });
        });

        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('teamModal').style.display = 'none';
        });

        window.onclick = function(event) {
            if (event.target == document.getElementById('teamModal')) {
                document.getElementById('teamModal').style.display = 'none';
            }
        };
    </script>
</body>

</html>