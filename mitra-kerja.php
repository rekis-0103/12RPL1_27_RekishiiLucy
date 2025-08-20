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
    <link rel="stylesheet" href="assets/css/mitra-kerja.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="page-container">
        <main class="page-content">
            <div class="container">
                <h1>Mitra Kerja</h1>

                <div class="buttons">
                    <button onclick="showCategory('pemerintahan')">Pemerintahan</button>
                    <button onclick="showCategory('non')">Non Pemerintahan</button>
                </div>

                <div id="card-container" class="card-container">
                </div>
            </div>
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
    <script src="js/navbar.js"></script>
    <script>
        const data = {
    pemerintahan: [
        { nama: "Kementrian ATR/BPN", gambar: "assets/atr.png" },
        { nama: "BIG", gambar: "assets/BIG.png" },
        { nama: "BMKG", gambar: "assets/BMKG.png" },
        { nama: "KLHK", gambar: "assets/klhk.png" },
        { nama: "Kementrian Kesehatan", gambar: "assets/kemenkes.png" },
        { nama: "PU", gambar: "assets/PU.png" },
        { nama: "Kementrian Pertanian", gambar: "assets/Kementerian-Pertanian.png" },
        { nama: "Kementrian Perhubungan", gambar: "assets/kemenhub.png" },
        { nama: "Dept. Tenaga kerja dan transmigrasi", gambar: "assets/Depnakertrans.png" },
        { nama: "Perhutani", gambar: "assets/perhutani.png" },
        { nama: "LAPAN", gambar: "assets/lapan.png" },
        { nama: "Badan Restorasi Gambut", gambar: "assets/brg.jpeg" },
        { nama: "BNPB", gambar: "assets/BNPB.png" },
        { nama: "KEMENDIKBUD", gambar: "assets/Kemdikbud.jpg" }
    ],
    non: [
        { nama: "Chevron", gambar: "assets/chevron.jpg" },
        { nama: "PGN SAKA", gambar: "assets/pgnsaka.png" },
        { nama: "BNP2TKI", gambar: "assets/bnp2tki.png" },
        { nama: "PLN", gambar: "assets/PLN.jpg" },
        { nama: "PALYJA", gambar: "assets/PALYJA.png" },
        { nama: "Pertamina Hulu Mahakam", gambar: "assets/phm.png" },
        { nama: "Sinarmas", gambar: "assets/sinarmas.jpg" },
        { nama: "Arutmin", gambar: "assets/arutmin.jpg" },
        { nama: "Green Eagle Group", gambar: "assets/Green-Eagle-Group.png" },
        { nama: "Bukit Asam", gambar: "assets/bukitasam.png" },
        { nama: "KPK", gambar: "assets/KPK.png" },
        { nama: "Indra Karya", gambar: "assets/indrakarya.jpeg" },
        { nama: "GIZ", gambar: "assets/giz.jpeg" },
        { nama: "UNESCO", gambar: "assets/unesco.jpeg" }
    ]
};

function showCategory(kategori) {
    const container = document.getElementById('card-container');

    // Animasi keluar
    const currentCards = container.querySelectorAll('.card');
    currentCards.forEach(card => {
        card.classList.add('fade-out');
    });

    setTimeout(() => {
        container.innerHTML = '';
        
        data[kategori].forEach(item => {
            const card = document.createElement('div');
            card.classList.add('card');

            card.innerHTML = `
                <img src="${item.gambar}" alt="${item.nama}">
                <h3>${item.nama}</h3>
            `;

            container.appendChild(card);
        });
    }, 500); 
}

// Tampilkan default kategori
showCategory('pemerintahan');
    </script>
</body>

</html>