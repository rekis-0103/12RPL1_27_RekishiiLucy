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

$dirs = array('../uploads/kegiatan');
foreach ($dirs as $d) {
    if (!is_dir($d)) {
        @mkdir($d, 0777, true);
    }
}

$notice_error = null;
$notice_success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add_kegiatan') {
        $judul = esc($conn, $_POST['judul'] ?? '');
        $deskripsi = esc($conn, $_POST['deskripsi'] ?? '');
        if ($judul === '') {
            $notice_error = 'Judul Kegiatan wajib diisi';
        } else {
            if (mysqli_query($conn, "INSERT INTO kegiatan (judul, deskripsi) VALUES ('$judul', '$deskripsi')")) {
                $kegiatan_id = mysqli_insert_id($conn);
                if (!empty($_FILES['foto']['name'][0])) {
                    for ($i = 0; $i < count($_FILES['foto']['name']); $i++) {
                        if (!is_uploaded_file($_FILES['foto']['tmp_name'][$i])) continue;
                        $fn = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['foto']['name'][$i]);
                        $dest = "../uploads/kegiatan/$fn";
                        if (@move_uploaded_file($_FILES['foto']['tmp_name'][$i], $dest)) {
                            $rel = 'uploads/kegiatan/' . $fn;
                            mysqli_query($conn, "INSERT INTO kegiatan_foto (kegiatan_id, foto) VALUES ($kegiatan_id, '" . esc($conn, $rel) . "')");
                        }
                    }
                }
                logActivity($conn, $user_id, "Konten: tambah kegiatan #$kegiatan_id ($judul)");
                $notice_success = 'Kegiatan berhasil ditambahkan';
            } else {
                $notice_error = 'Gagal menambah kegiatan';
            }
        }
    }

    if ($action === 'delete_kegiatan' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $res = mysqli_query($conn, "SELECT foto FROM kegiatan_foto WHERE kegiatan_id=$id");
        if ($res) {
            while ($r = mysqli_fetch_assoc($res)) {
                $p = '../' . ltrim($r['foto'], '/');
                if (is_file($p)) {
                    @unlink($p);
                }
            }
        }
        mysqli_query($conn, "DELETE FROM kegiatan_foto WHERE kegiatan_id=$id");
        if (mysqli_query($conn, "DELETE FROM kegiatan WHERE kegiatan_id=$id")) {
            logActivity($conn, $user_id, "Konten: hapus kegiatan #$id");
            $notice_success = 'Kegiatan dihapus';
        } else {
            $notice_error = 'Gagal menghapus kegiatan';
        }
    }

    if ($action === 'edit_kegiatan' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $judul = esc($conn, $_POST['judul'] ?? '');
        $deskripsi = esc($conn, $_POST['deskripsi'] ?? '');
        if ($judul === '') {
            $notice_error = 'Judul Kegiatan wajib diisi';
        } else {
            if (mysqli_query($conn, "UPDATE kegiatan SET judul='$judul', deskripsi='$deskripsi' WHERE kegiatan_id=$id")) {
                if (!empty($_FILES['foto']['name'][0])) {
                    for ($i = 0; $i < count($_FILES['foto']['name']); $i++) {
                        if (!is_uploaded_file($_FILES['foto']['tmp_name'][$i])) continue;
                        $fn = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['foto']['name'][$i]);
                        $dest = "../uploads/kegiatan/$fn";
                        if (@move_uploaded_file($_FILES['foto']['tmp_name'][$i], $dest)) {
                            $rel = 'uploads/kegiatan/' . $fn;
                            mysqli_query($conn, "INSERT INTO kegiatan_foto (kegiatan_id, foto) VALUES ($id, '" . esc($conn, $rel) . "')");
                        }
                    }
                }
                logActivity($conn, $user_id, "Konten: edit kegiatan #$id ($judul)");
                $notice_success = 'Kegiatan berhasil diperbarui';
            } else {
                $notice_error = 'Gagal memperbarui kegiatan';
            }
        }
    }

    if ($action === 'delete_kegiatan_foto' && isset($_POST['foto_id'])) {
        $foto_id = (int)$_POST['foto_id'];
        $res = mysqli_query($conn, "SELECT foto FROM kegiatan_foto WHERE foto_id=$foto_id");
        if ($res) {
            $r = mysqli_fetch_assoc($res);
            if ($r) {
                $p = '../' . ltrim($r['foto'], '/');
                if (is_file($p)) {
                    @unlink($p);
                }
            }
        }
        if (mysqli_query($conn, "DELETE FROM kegiatan_foto WHERE foto_id=$foto_id")) {
            $notice_success = 'Foto kegiatan dihapus';
        } else {
            $notice_error = 'Gagal menghapus foto';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - PT Waindo Specterra</title>
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
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Kelola Berita</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="kegiatan.php" class="active"><i class="fas fa-newspaper"></i> Kelola Berita</a></li>
                <li><a href="produk-manager.php"><i class="fas fa-box"></i> Kelola Produk</a></li>
                <li><a href="services-manager.php"><i class="fas fa-cogs"></i> Kelola Layanan</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Kelola Berita - Kegiatan</h1>
                <div class="tabs">
                    <a href="kegiatan.php" class="active">Kegiatan</a>
                    <a href="webinar.php">Webinar</a>
                    <a href="live.php">Live</a>
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
                    <h3><i class="fas fa-calendar"></i> Tambah Kegiatan</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="action-form">
                        <input type="hidden" name="action" value="add_kegiatan">
                        <div class="form-group"><label>Judul</label><input type="text" name="judul" required></div>
                        <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="2"></textarea></div>
                        <div class="form-group"><label>Foto (boleh lebih dari satu)</label><input type="file" name="foto[]" accept="image/*" multiple></div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
                    </form>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Foto</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $kg = mysqli_query($conn, "SELECT * FROM kegiatan ORDER BY created_at DESC"); ?>
                            <?php if ($kg && mysqli_num_rows($kg) > 0): while ($row = mysqli_fetch_assoc($kg)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                        <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                        <td>
                                            <?php
                                            $fotos = mysqli_query($conn, "SELECT * FROM kegiatan_foto WHERE kegiatan_id=" . (int)$row['kegiatan_id']);
                                            if ($fotos && mysqli_num_rows($fotos) > 0):
                                                while ($foto = mysqli_fetch_assoc($fotos)): ?>
                                                    <div class="photo-item">
                                                        <img src="../<?php echo htmlspecialchars($foto['foto']); ?>" alt="Foto Kegiatan" style="height:40px; margin:2px;">
                                                        <form method="POST" class="inline" onsubmit="return confirm('Hapus foto ini?')">
                                                            <input type="hidden" name="action" value="delete_kegiatan_foto">
                                                            <input type="hidden" name="foto_id" value="<?php echo (int)$foto['foto_id']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-times"></i></button>
                                                        </form>
                                                    </div>
                                            <?php endwhile;
                                            else: echo "-";
                                            endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm"
                                                onclick="showEditForm('kegiatan', <?php echo (int)$row['kegiatan_id']; ?>, '<?php echo htmlspecialchars(addslashes($row['judul'])); ?>', '<?php echo htmlspecialchars(addslashes($row['deskripsi'])); ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form method="POST" class="inline" onsubmit="return confirm('Hapus kegiatan ini?')">
                                                <input type="hidden" name="action" value="delete_kegiatan">
                                                <input type="hidden" name="id" value="<?php echo (int)$row['kegiatan_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada kegiatan</td>
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
            <h2 id="modalTitle">Edit Kegiatan</h2>
            <form id="editKegiatanForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_kegiatan">
                <input type="hidden" name="id" id="editKegiatanId">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" id="editKegiatanJudul" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="editKegiatanDeskripsi" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Tambah Foto Baru (opsional)</label>
                    <input type="file" name="foto[]" accept="image/*" multiple>
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
        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.mobile-toggle');
            sidebar.classList.toggle('active');
            toggleBtn.style.display = sidebar.classList.contains('active') ? "none" : "block";
        }

        // Tutup sidebar kalau klik di luar
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-toggle');
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                    mobileToggle.style.display = "block";
                }
            }
        });

        // Modal Edit
        const modal = document.getElementById('editModal');
        const span = document.getElementsByClassName('close')[0];

        function showEditForm(type, id, judul, deskripsi = '', url = '', tipe = '') {
            if (!modal) return;

            // Hide semua form
            ['editKegiatanForm', 'editWebinarForm', 'editLiveForm', 'editGaleriForm'].forEach(fid => {
                const f = document.getElementById(fid);
                if (f) f.style.display = 'none';
            });

            // Tampilkan form sesuai type
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

            // Buka modal
            modal.style.display = 'block';
            modal.style.opacity = '0';
            setTimeout(() => modal.style.opacity = '1', 10);
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

        if (span) span.onclick = closeModal;
        window.onclick = e => {
            if (e.target === modal) closeModal();
        };
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && modal.style.display === 'block') closeModal();
        });

        // Modal Gambar
        function openImageModal(imageSrc, imageAlt) {
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
                </div>`;
                document.body.appendChild(imageModal);

                // event close
                imageModal.addEventListener('click', e => {
                    if (e.target === imageModal || e.target.className === 'close') closeImageModal();
                });
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') closeImageModal();
                });
            }

            const modalImg = document.getElementById('modalImage');
            const imageInfo = document.getElementById('imageInfo');
            const loading = imageModal.querySelector('.image-loading');

            loading.style.display = 'block';
            modalImg.style.display = 'none';
            imageModal.style.display = 'block';
            document.body.style.overflow = 'hidden';

            modalImg.onload = () => {
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

        // Tambahkan handler ke semua gambar
        function addImageHandlers() {
            const imgs = document.querySelectorAll('.photo-item img, table tbody tr td img[src*="uploads/webinar/"]');
            imgs.forEach(img => {
                if (!img.dataset.clickable) {
                    img.dataset.clickable = 'true';
                    img.addEventListener('click', e => {
                        e.stopPropagation();
                        openImageModal(img.src, img.alt || 'Gambar');
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', addImageHandlers);

        function refreshImageHandlers() {
            addImageHandlers();
        }
    </script>
</body>

</html>