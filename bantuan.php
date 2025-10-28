<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/pages.css">
    <link rel="stylesheet" href="assets/css/bantuan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>PT Waindo SpecTerra</title>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="page-container">
        <div class="page-header">
            <div class="container">
                <h1>Panduan Pengguna</h1>
                <p>Pelajari cara menggunakan aplikasi Waindo SpecTerra dengan mudah</p>
            </div>
        </div>

        <div class="page-content">
            <div class="container">

                <!-- App Description -->
                <div class="app-description">
                    <h2>Deskripsi Aplikasi</h2>
                    <p>Aplikasi ini merupakan hasil <b>Re-Development website Company Profile PT Waindo SpecTerra</b>, yang dikembangkan untuk memperbarui tampilan, meningkatkan fungsionalitas, serta menambahkan fitur <b>E-Recruitment</b> guna mendukung proses rekrutmen karyawan secara digital.</p>
                    <p>Re-development dilakukan untuk menggantikan versi lama yang masih bersifat statis menjadi sistem berbasis website dinamis dan interaktif. Selain itu, fitur utama yang dikembangkan dalam versi terbaru ini adalah modul E-Recruitment, yang memungkinkan calon pelamar untuk melamar pekerjaan secara online tanpa perlu mengirim berkas fisik. Melalui fitur ini, HRD dapat mengelola lowongan pekerjaan, menyaring pelamar, memantau status lamaran, serta melakukan komunikasi langsung melalui sistem.</p>
                </div>

                <!-- App Specifications -->
                <div class="app-specs">
                    <h2>Spesifikasi Aplikasi</h2>
                    <div class="specs-grid">
                        <div class="spec-card">
                            <div class="spec-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h3>Tools</h3>
                            <ul>
                                <li>Visual Studio Code</li>
                                <li>Laragon</li>
                            </ul>
                        </div>
                        <div class="spec-card">
                            <div class="spec-icon">
                                <i class="fas fa-code"></i>
                            </div>
                            <h3>Front End</h3>
                            <ul>
                                <li>HTML</li>
                                <li>CSS</li>
                                <li>JavaScript</li>
                            </ul>
                        </div>
                        <div class="spec-card">
                            <div class="spec-icon">
                                <i class="fas fa-server"></i>
                            </div>
                            <h3>Back End</h3>
                            <ul>
                                <li>PHP</li>
                            </ul>
                        </div>
                        <div class="spec-card">
                            <div class="spec-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <h3>Database</h3>
                            <ul>
                                <li>MySQL</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Search Box -->
                <div class="manual-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchManual" placeholder="Cari panduan...">
                </div>

                <!-- Manual Sections -->
                <div class="manual-sections">

                <div class="manual-section" id="navigasi">
                        <div class="section-number">1</div>
                        <div class="section-content">
                            <h2>Menu Navigasi</h2>
                            <p class="section-desc">Menu navigasi yang mengarahkan pengguna untuk berpindah antar halaman</p>

                            <div class="manual-image">
                                <img src="uploads/user-manual/nav.png" alt="Halaman Beranda">
                            </div>
                        </div>
                    </div>
                    <!-- Section 2: Beranda -->
                    <div class="manual-section" id="beranda">
                        <div class="section-number">2</div>
                        <div class="section-content">
                            <h2>Halaman Beranda</h2>
                            <p class="section-desc">Halaman utama dari company profile PT Waindo SpecTerra</p>
                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Slider</h4>
                                        <p>Menampilkan slider gambar utama</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/slider.png" alt="Hero Slider"><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Visi & Misi</h4>
                                        <p>Menampilkan Visi dan Misi dari perusahaan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/visimisi.png" alt="Menu Navigasi">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">c</span>
                                    <div class="feature-content">
                                        <h4>Berita Terbaru</h4>
                                        <p>Bagian yang menampilkan 3 berita dan informasi terbaru</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/beritaterbaru.png" alt="Informasi Perusahaan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Tentang Kami -->
                    <div class="manual-section" id="tentang">
                        <div class="section-number">3</div>
                        <div class="section-content">
                            <h2>Halaman Tentang Kami</h2>
                            <p class="section-desc">Halaman yang berisi deskripsi perusahaan dan tim yang ada pada perusahaan</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Deskripsi perusahaan</h4>
                                        <p>Membaca informasi tentang perusahaan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/deskripsiperusahaan.png" alt="Profil Perusahaan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Tim Kami</h4>
                                        <p>Melihat tim profesional yang bekerja di PT Waindo SpecTerra beserta divisi nya</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/tim.png" alt="Visi & Misi">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">c</span>
                                    <div class="feature-content">
                                        <h4>Deskripsi Tim</h4>
                                        <p>Melihat deskripsi dari setiap orang pada tim kami</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/deskripsitim.png" alt="Tim Kami">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Produk -->
                    <div class="manual-section" id="produk">
                        <div class="section-number">4</div>
                        <div class="section-content">
                            <h2>Halaman Produk</h2>
                            <p class="section-desc">Halaman yang menampilkan seluruh produk yang ditawarkan oleh Waindo SpecTerra.</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Kategori Produk</h4>
                                        <p>Melihat daftar kategori untuk semua produk</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/kategoriproduk.png" alt="Katalog Produk">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>List Produk</h4>
                                        <p>Menampilkan produk beserta gambar dan bisa dilihat detail dari masing-masing produk untuk melihat deskripsi lengkap dari produk tersebut</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/listproduk-dan-deskripsi.png" alt="Detail Produk">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Layanan -->
                    <div class="manual-section" id="layanan">
                        <div class="section-number">5</div>
                        <div class="section-content">
                            <h2>Halaman Layanan</h2>
                            <p class="section-desc">Halaman yang menjelaskan berbagai layanan profesional</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Jenis Layanan</h4>
                                        <p>Melihat berbagai jenis layanan konsultasi dan implementasi yang ditawarkan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/jenislayanan.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>List Layanan</h4>
                                        <p>Melihat layanan yang disediakan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/listlayanan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 6: Mitra Kerja -->
                    <div class="manual-section" id="mitra">
                        <div class="section-number">6</div>
                        <div class="section-content">
                            <h2>Halaman Mitra Kerja</h2>
                            <p class="section-desc">Halaman yang menampilkan daftar mitra Pemerintahan dan BUMN yang telah bekerja sama dengan PT Waindo SpecTerra
                            </p>
                            <div class="manual-image">
                                <img src="uploads/user-manual/Mitra-Kerja.png" alt="Halaman Mitra Kerja">
                            </div>
                        </div>
                    </div>

                    <!-- Section 7: Berita -->
                    <div class="manual-section" id="berita">
                        <div class="section-number">7</div>
                        <div class="section-content">
                            <h2>Halaman Berita</h2>
                            <p class="section-desc">Halaman yang menampilkan berita, webinar, Live Streaming, dan Galeri</p>

                            <div class="manual-image">
                                <img src="uploads/user-manual/berita.png" alt="Halaman Berita">
                            </div>
                        </div>
                    </div>

                    <!-- Section 8: Hubungi Kami -->
                    <div class="manual-section" id="kontak">
                        <div class="section-number">8</div>
                        <div class="section-content">
                            <h2>Halaman Hubungi Kami</h2>
                            <p class="section-desc">Halaman yang menampilkan Alamat, Telepon, Fax, Email dan lokasi perusahaan yang ada pada google maps</p>

                            <div class="manual-image">
                                <img src="uploads/user-manual/hubungi.png" alt="Halaman Hubungi Kami">
                            </div>
                        </div>
                    </div>

                    <!-- Section 9: Lowongan Kerja -->
                    <div class="manual-section" id="lowongan">
                        <div class="section-number">9</div>
                        <div class="section-content">
                            <h2>Halaman Lowongan Kerja</h2>
                            <p class="section-desc">Halaman yang menampilkan daftar lowongan pekerjaan yang ada</p>

                            <div class="manual-image">
                                <img src="uploads/user-manual/lowongan.png" alt="Halaman Lowongan Kerja">
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">10</div>
                        <div class="section-content">
                            <h2>Halaman Login dan Register</h2>
                            <p class="section-desc">Halaman yang menampilkan login dan register pengguna</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Login</h4>
                                        <p>Input username dan password, jika benar maka akan diarahkan ke dashboard sesuai hak akses masing-masing</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/login.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Register</h4>
                                        <p>Input username, email, nama lengkap, password, konfirmasi password untuk mendaftarkan pengguna sebagai pelamar untuk keperluan melamar pekerjaan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/register.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="lowongan">
                        <div class="section-number">11</div>
                        <div class="section-content">
                            <h2>Dropdown dashboard-logout</h2>
                            <p class="section-desc">ketika sudah login, sistem menampilkan dropdown dashboard untuk ke halaman dashboard sesuai hak akses masing masing dan logout untuk keluar dari sesi user tersebut</p>

                            <div class="manual-image">
                                <img src="uploads/user-manual/dashboard-logout.png" alt="Halaman Lowongan Kerja">
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="lowongan">
                        <div class="section-number">12</div>
                        <div class="section-content">
                            <h2>Halaman Dashboard Admin</h2>
                            <p class="section-desc">Halaman yang menampilkan ringkasan total user, lowongan aktif, lamaran masuk, dan aktivitas terbaru</p>

                            <div class="manual-image">
                                <img src="uploads/user-manual/dashboard-admin.png" alt="Halaman Lowongan Kerja">
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">13</div>
                        <div class="section-content">
                            <h2>Halaman Kelola akun</h2>
                            <p class="section-desc">Halaman untuk mengelola data pengguna sistem</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Tambah User</h4>
                                        <p>Input username, nama lengkap, password, email, dan role untuk mendaftarkan pengguna ke dalam sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/tambah-user.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Daftar User</h4>
                                        <p>Menampilkan daftar user yang ada pada sistem, bisa di filter berdasarkan role dan status.</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-user.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">c</span>
                                    <div class="feature-content">
                                        <h4>Edit User</h4>
                                        <p>Edit data dari user yang ada pada sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/edit-user.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">d</span>
                                    <div class="feature-content">
                                        <h4>Reset Password</h4>
                                        <p>Untuk membuat password baru dari user.</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/reset-pass.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">e</span>
                                    <div class="feature-content">
                                        <h4>Hapus Akun</h4>
                                        <p>Untuk menghapus data akun dari user, jika akun sudah di hapus maka tidak akan bisa di kembalikan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/hapus-akun.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">14</div>
                        <div class="section-content">
                            <h2>Halaman Log Aktivitas</h2>
                            <p class="section-desc">Halaman untuk melihat semua aktivitas pengguna terhadap sistem</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Filter Log</h4>
                                        <p>Memfilter log agar lebih mudah untuk mencari aktivitas</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/filter-log.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Daftar Log Aktivitas</h4>
                                        <p>Menampilkan daftar aktivitas pengguna terhadap sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-log.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">c</span>
                                    <div class="feature-content">
                                        <h4>Export Log</h4>
                                        <p>Mendownload file bentuk .csv berdasarkan tabel daftar log aktivitas.</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/export-log.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">15</div>
                        <div class="section-content">
                            <h2>Halaman Dashboard HRD</h2>
                            <p class="section-desc">Halaman yang menampilkan ringkasan lowongan aktif, lamaran masuk, seleksi administrasi, kandidat, dan lamaran terbaru </p>
                            <div class="manual-image">
                                <img src="uploads/user-manual/dashboard-hrd.png" alt="dashboard-hrd">
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">16</div>
                        <div class="section-content">
                            <h2>Halaman Kelola Lowongan</h2>
                            <p class="section-desc">Halaman untuk mengelola pop up gambar lowongan dan mengelola lowongan</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Tambah pop up lowongan</h4>
                                        <p>Menambah pop up agar bisa ditampilkan di halaman lowongan kerja</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/tambah-popup.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Daftar pop up lowongan</h4>
                                        <p>Menampilkan daftar pop up yang tersedia agar bisa di edit, hapus, dan aktif/nonaktif</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-popup.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">c</span>
                                    <div class="feature-content">
                                        <h4>Edit pop up lowongan</h4>
                                        <p>Mengedit data pop up lowongan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/edit-popup.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">d</span>
                                    <div class="feature-content">
                                        <h4>Hapus pop up lowongan</h4>
                                        <p>Menghapus pop up lowongan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/hapus-popup.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">e</span>
                                    <div class="feature-content">
                                        <h4>Tambah Lowongan</h4>
                                        <p>Menambah lowongan dengan input judul, range gaji, deskripsi, persyaratan, lokasi, dan status agar pelamar bisa melamar pekerjaan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/tambah-lowongan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">f</span>
                                    <div class="feature-content">
                                        <h4>Daftar Lowongan</h4>
                                        <p>Menampilkan daftar lowongan yang telah dibuat, ada aksi untuk edit, hapus, dan buka/tutup lowongan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-lowongan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">g</span>
                                    <div class="feature-content">
                                        <h4>Edit Lowongan</h4>
                                        <p>Mengedit lowongan untuk memperbarui data lowongan pekerjaan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/edit-lowongan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">h</span>
                                    <div class="feature-content">
                                        <h4>Hapus Lowongan</h4>
                                        <p>Menghapus lowongan yang sudah ada</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/hapus-lowongan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">17</div>
                        <div class="section-content">
                            <h2>Halaman Kelola Lamaran</h2>
                            <p class="section-desc">Halaman untuk mengelola pelamar agar bisa lanjut ke tahap tes & wawancara atau ditolak</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Daftar Pelamar</h4>
                                        <p>Menampilkan daftar singkat dari pelamar</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-pelamar.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Detail lamaran</h4>
                                        <p>Menampilkan detail dari informasi pelamar</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/detail-pelamar.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">c</span>
                                    <div class="feature-content">
                                        <h4>Daftar Semua Pelamar</h4>
                                        <p>Menampilkan daftar pelamar dari semua status, dengan bisa di filter dan di export pdf</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-semua-pelamar.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">d</span>
                                    <div class="feature-content">
                                        <h4>Export PDF pelamar</h4>
                                        <p>Mendownload file .pdf berdasarkan tabel daftar semua pelamar</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/export-pdf-pelamar.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="manual-section" id="login">
                        <div class="section-number">18</div>
                        <div class="section-content">
                            <h2>Halaman Kelola Kandidat</h2>
                            <p class="section-desc">Halaman untuk mengelola kandidat agar diterima bekerja atau ditolak</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Daftar Kandidat</h4>
                                        <p>Menampilkan daftar singkat dari Kandidat agar bisa diterima bekerja atau ditolak</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-kandidat.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Detail Kandidat</h4>
                                        <p>Menampilkan detail dari informasi kandidat</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/detail-kandidat.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">19</div>
                        <div class="section-content">
                            <h2>Halaman Dashboard Pelamar</h2>
                            <p class="section-desc">Halaman yang menampilkan ringkasan lowongan tersedia, lamaran terkirim, menunggu review, diterima, dan lamaran terbaru</p>
                            <div class="manual-image">
                                <img src="uploads/user-manual/dashboard-pelamar.png" alt="dashboard-hrd">
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">20</div>
                        <div class="section-content">
                            <h2>Halaman Profil</h2>
                            <p class="section-desc">Halaman yang menampilkan profil dan bisa untuk update profil</p>
                            <div class="manual-image">
                                <img src="uploads/user-manual/profil.png" alt="dashboard-hrd">
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">21</div>
                        <div class="section-content">
                            <h2>Halaman Lihat Lowongan</h2>
                            <p class="section-desc">Halaman yang menampilkan daftar lowongan, detail lowongan dan melamar pekerjaan</p>
                            <div class="manual-image">
                                <img src="uploads/user-manual/lihat-lowongan.png" alt="dashboard-hrd">
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">22</div>
                        <div class="section-content">
                            <h2>Halaman Dashboard Konten</h2>
                            <p class="section-desc">Halaman yang menampilkan ringkasan berita, produk, layanan, dan konten terbaru</p>
                            <div class="manual-image">
                                <img src="uploads/user-manual/dashboard-konten.png" alt="dashboard-hrd">
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">23</div>
                        <div class="section-content">
                            <h2>Halaman Kelola Berita</h2>
                            <p class="section-desc">Halaman untuk mengelola berita agar bisa ditampilkan pada halaman berita</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Tambah Kegiatan</h4>
                                        <p>Menambah kegiatan berupa input judul, deskripsi, dan foto yang bisa lebih dari 1. untuk bisa menambah ke dalam kegiatan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/tambah-kegiatan.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Daftar Kegiatan</h4>
                                        <p>Menampilkan daftar dari kegiatan yang ada pada sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-kegiatan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">c</span>
                                    <div class="feature-content">
                                        <h4>Edit Kegiatan</h4>
                                        <p>Mengedit kegiatan untuk bisa diperbarui isi dari kegiatan tersebut</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/edit-kegiatan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">d</span>
                                    <div class="feature-content">
                                        <h4>Hapus Kegiatan</h4>
                                        <p>Menghapus kegiatan dari sistem dengan ada konfirmasi sebelum menghapus kegiatan</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/hapus-kegiatan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">e</span>
                                    <div class="feature-content">
                                        <h4>Tambah Webinar</h4>
                                        <p>Menambah webinar dengan input judul dan gambar untuk bisa menambahkan webinar</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/tambah-webinar.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">f</span>
                                    <div class="feature-content">
                                        <h4>Daftar Webinar</h4>
                                        <p>Menampilkan daftar dari webinar yang ada pada sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-webinar.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">g</span>
                                    <div class="feature-content">
                                        <h4>Edit Webinar</h4>
                                        <p>Mengedit webinar untuk bisa diperbarui isi dari webinar tersebut</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/edit-webinar.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">h</span>
                                    <div class="feature-content">
                                        <h4>Hapus Webinar</h4>
                                        <p>Menghapus webinar dari sistem dengan ada konfirmasi sebelum menghapus webinar</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/hapus-webinar.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">i</span>
                                    <div class="feature-content">
                                        <h4>Tambah Live Streaming</h4>
                                        <p>Menambah live streaming dengan input judul tipe dan url/file untuk bisa menambahkan live streaming</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/tambah-live.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">j</span>
                                    <div class="feature-content">
                                        <h4>Daftar Live Streaming</h4>
                                        <p>Menampilkan daftar dari live streaming yang ada pada sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-live.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">k</span>
                                    <div class="feature-content">
                                        <h4>Edit Live Streaming</h4>
                                        <p>Mengedit live streaming untuk bisa diperbarui isi dari live streaming tersebut</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/edit-live.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">l</span>
                                    <div class="feature-content">
                                        <h4>Hapus Live Streaming</h4>
                                        <p>Menghapus live streaming dari sistem dengan ada konfirmasi sebelum menghapus live streaming</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/hapus-live.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">m</span>
                                    <div class="feature-content">
                                        <h4>Tambah Galeri</h4>
                                        <p>Menambah webinar dengan input judul dan gambar lebih dari satu untuk bisa menambahkan galeri</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/tambah-galeri.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">n</span>
                                    <div class="feature-content">
                                        <h4>Daftar Galeri</h4>
                                        <p>Menampilkan daftar dari galeri yang ada pada sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-galeri.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">o</span>
                                    <div class="feature-content">
                                        <h4>Edit Galeri</h4>
                                        <p>Mengedit galeri untuk bisa diperbarui isi dari galeri tersebut</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/edit-galeri.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">p</span>
                                    <div class="feature-content">
                                        <h4>Hapus Galeri</h4>
                                        <p>Menghapus galeri dari sistem dengan ada konfirmasi sebelum menghapus galeri</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/hapus-galeri.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">24</div>
                        <div class="section-content">
                            <h2>Halaman Kelola Produk</h2>
                            <p class="section-desc">Halaman untuk mengelola produk untuk tambah, edit, dan hapus produk</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Tambah Produk</h4>
                                        <p>Menambahkan produk dengan input nama produk, kategori, deskripsi, dan gambar produk untuk bisa menambahkan produk ke sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/tambah-produk.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Daftar Produk</h4>
                                        <p>Menampilkan daftar dari produk yang ada pada sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-produk.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">c</span>
                                    <div class="feature-content">
                                        <h4>Edit Produk</h4>
                                        <p>Mengedit produk untuk bisa memperbarui isi dari produk tersebut</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/edit-produk.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">d</span>
                                    <div class="feature-content">
                                        <h4>Hapus Produk</h4>
                                        <p>Menghapus produk dari sistem dengan ada konfirmasi sebelum menghapus produk tersebut</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/hapus-produk.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="manual-section" id="login">
                        <div class="section-number">25</div>
                        <div class="section-content">
                            <h2>Halaman Kelola Layanan</h2>
                            <p class="section-desc">Halaman untuk mengelola layanan untuk tambah, edit, dan hapus layanan</p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <span class="feature-label">a</span>
                                    <div class="feature-content">
                                        <h4>Tambah Layanan</h4>
                                        <p>Menambahkan layanan dengan input judul layanan, kategori, deskripsi, fitur, dan gambar untuk bisa menambahkan layanan ke sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/tambah-layanan.png" alt="Jenis Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">b</span>
                                    <div class="feature-content">
                                        <h4>Daftar Layanan</h4>
                                        <p>Menampilkan daftar dari layanan yang ada pada sistem</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/daftar-layanan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">c</span>
                                    <div class="feature-content">
                                        <h4>Edit Layanan</h4>
                                        <p>Mengedit layanan untuk bisa memperbarui isi dari layanan tersebut</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/edit-layanan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-label">d</span>
                                    <div class="feature-content">
                                        <h4>Hapus Layanan</h4>
                                        <p>Menghapus Layanan dari sistem dengan ada konfirmasi sebelum menghapus layanan tersebut</p>
                                        <div class="feature-image">
                                            <img src="uploads/user-manual/hapus-layanan.png" alt="Proses Layanan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
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
    <script>
        // FAQ Toggle
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                const isActive = faqItem.classList.contains('active');

                // Close all FAQ items
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Open clicked item if it wasn't active
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });

        // Search functionality
        document.getElementById('searchManual').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const sections = document.querySelectorAll('.manual-section');

            sections.forEach(section => {
                const text = section.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    section.style.display = 'flex';
                } else {
                    section.style.display = 'none';
                }
            });
        });

        // Smooth scroll for quick links
        document.querySelectorAll('.quick-link-item').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                if (targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>

</html>