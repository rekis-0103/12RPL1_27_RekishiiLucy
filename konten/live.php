<?php
session_start();
require_once '../connect/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'konten') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

function esc($conn, $s)
{
    return mysqli_real_escape_string($conn, $s);
}
function logActivity($conn, $actor_user_id, $action)
{
    $actor_user_id = (int)$actor_user_id;
    $action = mysqli_real_escape_string($conn, $action);
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($actor_user_id, '$action')");
}

$dirs = array('../uploads/live');
foreach ($dirs as $d) {
    if (!is_dir($d)) {
        @mkdir($d, 0777, true);
    }
}

$notice_error = null;
$notice_success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add_live') {
        $judul = esc($conn, $_POST['judul'] ?? '');
        $tipe = $_POST['tipe'] === 'mp4' ? 'mp4' : 'youtube';
        $url = esc($conn, $_POST['url'] ?? '');
        if ($tipe === 'mp4' && isset($_FILES['file_mp4']) && is_uploaded_file($_FILES['file_mp4']['tmp_name'])) {
            $fn = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['file_mp4']['name']);
            $dest = "../uploads/live/$fn";
            if (@move_uploaded_file($_FILES['file_mp4']['tmp_name'], $dest)) {
                $url = esc($conn, 'uploads/live/' . $fn);
            }
        }
        if ($judul === '' || $url === '') {
            $notice_error = 'Judul dan URL/File wajib diisi';
        } else {
            $q = "INSERT INTO live_streaming (judul, tipe, url) VALUES ('$judul', '$tipe', '$url')";
            if (mysqli_query($conn, $q)) {
                $lid = mysqli_insert_id($conn);
                logActivity($conn, $user_id, "Konten: tambah live_streaming #$lid ($tipe)");
                $notice_success = 'Live streaming berhasil ditambahkan';
            } else {
                $notice_error = 'Gagal menambah live streaming';
            }
        }
    }

    if ($action === 'delete_live' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $res = mysqli_query($conn, "SELECT tipe, url FROM live_streaming WHERE streaming_id=$id");
        if ($res) {
            $r = mysqli_fetch_assoc($res);
            if ($r && $r['tipe'] === 'mp4') {
                $p = '../' . ltrim($r['url'], '/');
                if (is_file($p)) {
                    @unlink($p);
                }
            }
        }
        if (mysqli_query($conn, "DELETE FROM live_streaming WHERE streaming_id=$id")) {
            logActivity($conn, $user_id, "Konten: hapus live_streaming #$id");
            $notice_success = 'Live streaming dihapus';
        } else {
            $notice_error = 'Gagal menghapus live streaming';
        }
    }

    if ($action === 'edit_live' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $judul = esc($conn, $_POST['judul'] ?? '');
        $tipe = $_POST['tipe'] === 'mp4' ? 'mp4' : 'youtube';
        $url = esc($conn, $_POST['url'] ?? '');
        if ($tipe === 'mp4' && isset($_FILES['file_mp4']) && is_uploaded_file($_FILES['file_mp4']['tmp_name'])) {
            $fn = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['file_mp4']['name']);
            $dest = "../uploads/live/$fn";
            if (@move_uploaded_file($_FILES['file_mp4']['tmp_name'], $dest)) {
                $url = esc($conn, 'uploads/live/' . $fn);
                $res = mysqli_query($conn, "SELECT tipe, url FROM live_streaming WHERE streaming_id=$id");
                if ($res) {
                    $r = mysqli_fetch_assoc($res);
                    if ($r && $r['tipe'] === 'mp4') {
                        $p = '../' . ltrim($r['url'], '/');
                        if (is_file($p)) {
                            @unlink($p);
                        }
                    }
                }
            }
        }
        if ($judul === '' || $url === '') {
            $notice_error = 'Judul dan URL/File wajib diisi';
        } else {
            $q = "UPDATE live_streaming SET judul='$judul', tipe='$tipe', url='$url' WHERE streaming_id=$id";
            if (mysqli_query($conn, $q)) {
                logActivity($conn, $user_id, "Konten: edit live_streaming #$id ($tipe)");
                $notice_success = 'Live streaming berhasil diperbarui';
            } else {
                $notice_error = 'Gagal memperbarui live streaming';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Live Streaming - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/berita.css">
    <style>
        .tabs {
            display: flex;
            gap: 8px;
            margin: 10px 0;
            flex-wrap: wrap;
        }

        .tabs a {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-decoration: none;
        }

        .tabs a.active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
    </style>
</head>

<body>
    <button class="mobile-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Kelola Berita</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="live.php" class="active"><i class="fas fa-newspaper"></i> Kelola Konten</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Kelola Live Streaming</h1>
                <div class="tabs">
                    <a href="kegiatan.php">Kegiatan</a>
                    <a href="webinar.php">Webinar</a>
                    <a href="live.php" class="active">Live</a>
                    <a href="galeri.php">Galeri</a>
                </div>
            </div>

            <?php if ($notice_error): ?>
                <div class="alert alert-danger"><?php echo $notice_error; ?></div>
            <?php endif; ?>
            <?php if ($notice_success): ?>
                <div class="alert alert-success"><?php echo $notice_success; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-video"></i> Tambah Live</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="action-form">
                        <input type="hidden" name="action" value="add_live">
                        <div class="form-group"><label>Judul</label><input type="text" name="judul" required></div>
                        <div class="form-group"><label>Tipe</label><select name="tipe">
                                <option value="youtube">YouTube (embed)</option>
                                <option value="mp4">MP4 (upload)</option>
                            </select></div>
                        <div class="form-group"><label>URL (YouTube)</label><input type="text" name="url" placeholder="https://www.youtube.com/embed/..."></div>
                        <div class="form-group"><label>File MP4 (untuk MP4)</label><input type="file" name="file_mp4" accept="video/mp4"></div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
                    </form>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>URL/File</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $lv = mysqli_query($conn, "SELECT * FROM live_streaming ORDER BY created_at DESC"); ?>
                            <?php if ($lv && mysqli_num_rows($lv) > 0): while ($row = mysqli_fetch_assoc($lv)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipe']); ?></td>
                                        <td style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; "><?php echo htmlspecialchars($row['url']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="showEditForm('live', <?php echo (int)$row['streaming_id']; ?>, '<?php echo htmlspecialchars(addslashes($row['judul'])); ?>', '<?php echo htmlspecialchars(addslashes($row['url'])); ?>', '<?php echo htmlspecialchars($row['tipe']); ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form method="POST" class="inline" onsubmit="return confirm('Hapus live streaming ini?')">
                                                <input type="hidden" name="action" value="delete_live">
                                                <input type="hidden" name="id" value="<?php echo (int)$row['streaming_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada live streaming</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Edit Live Streaming</h2>
            <form id="editLiveForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_live">
                <input type="hidden" name="id" id="editLiveId">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" id="editLiveJudul" required>
                </div>
                <div class="form-group">
                    <label>Tipe</label>
                    <select name="tipe" id="editLiveTipe">
                        <option value="youtube">YouTube (embed)</option>
                        <option value="mp4">MP4 (upload)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>URL (YouTube)</label>
                    <input type="text" name="url" id="editLiveUrl" placeholder="https://www.youtube.com/embed/...">
                </div>
                <div class="form-group">
                    <label>File MP4 Baru (untuk MP4)</label>
                    <input type="file" name="file_mp4" accept="video/mp4">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/navbar.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.mobile-toggle');

            sidebar.classList.toggle('active');

            // Sembunyikan tombol ketika sidebar muncul
            if (sidebar.classList.contains('active')) {
                toggleBtn.style.display = "none";
            } else {
                toggleBtn.style.display = "block";
            }
        }

        // Tutup sidebar kalau klik di luar
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-toggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                    mobileToggle.style.display = "block"; // tampilkan kembali tombol
                }
            }
        });

        // Modal functionality
        const modal = document.getElementById('editModal');
        const span = document.getElementsByClassName('close')[0];

        function showEditForm(type, id, judul, deskripsi = '', url = '', tipe = '') {
            console.log('Opening modal for:', type, id, judul); // Debug log

            // Ensure modal exists
            if (!modal) {
                console.error('Modal element not found!');
                return;
            }

            // Hide all forms first
            const forms = ['editKegiatanForm', 'editWebinarForm', 'editLiveForm', 'editGaleriForm'];
            forms.forEach(formId => {
                const form = document.getElementById(formId);
                if (form) form.style.display = 'none';
            });

            // Show appropriate form and set values
            if (type === 'kegiatan') {
                document.getElementById('modalTitle').textContent = 'Edit Kegiatan';
                document.getElementById('editKegiatanForm').style.display = 'block';
                document.getElementById('editKegiatanId').value = id;
                document.getElementById('editKegiatanJudul').value = judul;
                document.getElementById('editKegiatanDeskripsi').value = deskripsi;
            } else if (type === 'webinar') {
                document.getElementById('modalTitle').textContent = 'Edit Webinar';
                document.getElementById('editWebinarForm').style.display = 'block';
                document.getElementById('editWebinarId').value = id;
                document.getElementById('editWebinarJudul').value = judul;
            } else if (type === 'live') {
                document.getElementById('modalTitle').textContent = 'Edit Live Streaming';
                document.getElementById('editLiveForm').style.display = 'block';
                document.getElementById('editLiveId').value = id;
                document.getElementById('editLiveJudul').value = judul;
                document.getElementById('editLiveUrl').value = url;
                document.getElementById('editLiveTipe').value = tipe;
            } else if (type === 'galeri') {
                document.getElementById('modalTitle').textContent = 'Edit Galeri';
                document.getElementById('editGaleriForm').style.display = 'block';
                document.getElementById('editGaleriId').value = id;
                document.getElementById('editGaleriJudul').value = judul;
            }

            // Show modal with animation
            modal.style.display = 'block';
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.opacity = '1';
            }, 10);

            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            if (!modal) return;

            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }

        // Close modal when clicking on X or outside modal
        if (span) {
            span.onclick = closeModal;
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modal.style.display === 'block') {
                closeModal();
            }
        });

        // Prevent modal close when clicking inside modal content
        modal.addEventListener('click', function(event) {
            event.stopPropagation();
        });

        function openImageModal(imageSrc, imageAlt) {
            // Create modal if it doesn't exist
            let imageModal = document.getElementById('imageModal');
            if (!imageModal) {
                imageModal = document.createElement('div');
                imageModal.id = 'imageModal';
                imageModal.className = 'image-modal';
                imageModal.innerHTML = `
                    <div class="image-modal-content">
                        <span class="close">&times;</span>
                        <div class="image-loading"></div>
                        <img id="modalImage" alt="">
                        <div class="image-info" id="imageInfo"></div>
                    </div>
                `;
                document.body.appendChild(imageModal);

                // Add event listeners
                imageModal.addEventListener('click', function(e) {
                    if (e.target === imageModal || e.target.className === 'close') {
                        closeImageModal();
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && imageModal.style.display === 'block') {
                        closeImageModal();
                    }
                });
            }

            const modalImg = document.getElementById('modalImage');
            const imageInfo = document.getElementById('imageInfo');
            const loading = imageModal.querySelector('.image-loading');

            // Show loading spinner
            loading.style.display = 'block';
            modalImg.style.display = 'none';
            imageModal.style.display = 'block';
            document.body.style.overflow = 'hidden';

            // Load image
            modalImg.onload = function() {
                loading.style.display = 'none';
                modalImg.style.display = 'block';
            };

            modalImg.src = imageSrc;
            modalImg.alt = imageAlt;
            imageInfo.textContent = imageAlt || 'Gambar';
        }

        function closeImageModal() {
            const imageModal = document.getElementById('imageModal');
            if (imageModal) {
                imageModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Fungsi untuk menambahkan event handler pada gambar webinar
        function addWebinarImageHandlers() {
            const webinarImages = document.querySelectorAll('table tbody tr td img[src*="uploads/webinar/"]');
            webinarImages.forEach(function(img) {
                if (!img.classList.contains('clickable-image')) {
                    img.classList.add('clickable-image');
                    img.addEventListener('click', function(e) {
                        e.stopPropagation();
                        openImageModal(this.src, this.alt || 'Gambar Webinar');
                    });
                }
            });
        }

        // Panggil fungsi setelah DOM selesai dimuat
        document.addEventListener('DOMContentLoaded', function() {
            addWebinarImageHandlers();

            // Juga panggil untuk gambar lainnya jika diperlukan
            const images = document.querySelectorAll('.photo-item img');
            images.forEach(function(img) {
                if (!img.dataset.clickable) {
                    img.dataset.clickable = 'true';
                    img.addEventListener('click', function(e) {
                        e.stopPropagation();
                        openImageModal(this.src, this.alt || 'Gambar');
                    });
                }
            });
        });

        // Panggil ulang setelah ada perubahan AJAX/dynamic content
        function refreshImageHandlers() {
            addWebinarImageHandlers();

            const images = document.querySelectorAll('.photo-item img');
            images.forEach(function(img) {
                if (!img.dataset.clickable) {
                    img.dataset.clickable = 'true';
                    img.addEventListener('click', function(e) {
                        e.stopPropagation();
                        openImageModal(this.src, this.alt || 'Gambar');
                    });
                }
            });
        }
    </script>
</body>

</html>