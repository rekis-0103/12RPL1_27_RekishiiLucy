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

$dirs = array('../uploads/webinar');
foreach ($dirs as $d) {
    if (!is_dir($d)) {
        @mkdir($d, 0777, true);
    }
}

$notice_error = null;
$notice_success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add_webinar') {
        $judul = esc($conn, $_POST['judul'] ?? '');
        $gambar_path = null;
        if (isset($_FILES['gambar']) && is_uploaded_file($_FILES['gambar']['tmp_name'])) {
            $fn = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['gambar']['name']);
            $dest = "../uploads/webinar/$fn";
            if (@move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
                $gambar_path = 'uploads/webinar/' . $fn;
            }
        }
        if ($judul === '') {
            $notice_error = 'Judul Webinar wajib diisi';
        } else {
            $q = "INSERT INTO webinar (judul, gambar) VALUES ('$judul', " . ($gambar_path ? "'" . esc($conn, $gambar_path) . "'" : "NULL") . ")";
            if (mysqli_query($conn, $q)) {
                $wid = mysqli_insert_id($conn);
                logActivity($conn, $user_id, "Konten: tambah webinar #$wid ($judul)");
                $notice_success = 'Webinar berhasil ditambahkan';
            } else {
                $notice_error = 'Gagal menambah webinar';
            }
        }
    }

    if ($action === 'delete_webinar' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $res = mysqli_query($conn, "SELECT gambar FROM webinar WHERE webinar_id=$id");
        if ($res) {
            $r = mysqli_fetch_assoc($res);
            if (!empty($r['gambar'])) {
                $p = '../' . ltrim($r['gambar'], '/');
                if (is_file($p)) {
                    @unlink($p);
                }
            }
        }
        if (mysqli_query($conn, "DELETE FROM webinar WHERE webinar_id=$id")) {
            logActivity($conn, $user_id, "Konten: hapus webinar #$id");
            $notice_success = 'Webinar dihapus';
        } else {
            $notice_error = 'Gagal menghapus webinar';
        }
    }

    if ($action === 'edit_webinar' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $judul = esc($conn, $_POST['judul'] ?? '');
        $gambar_path = null;
        if (isset($_FILES['gambar']) && is_uploaded_file($_FILES['gambar']['tmp_name'])) {
            $fn = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['gambar']['name']);
            $dest = "../uploads/webinar/$fn";
            if (@move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
                $gambar_path = 'uploads/webinar/' . $fn;
                $res = mysqli_query($conn, "SELECT gambar FROM webinar WHERE webinar_id=$id");
                if ($res) {
                    $r = mysqli_fetch_assoc($res);
                    if (!empty($r['gambar'])) {
                        $p = '../' . ltrim($r['gambar'], '/');
                        if (is_file($p)) {
                            @unlink($p);
                        }
                    }
                }
            }
        }
        if ($judul === '') {
            $notice_error = 'Judul Webinar wajib diisi';
        } else {
            $q = "UPDATE webinar SET judul='$judul'" . ($gambar_path ? ", gambar='" . esc($conn, $gambar_path) . "'" : "") . " WHERE webinar_id=$id";
            if (mysqli_query($conn, $q)) {
                logActivity($conn, $user_id, "Konten: edit webinar #$id ($judul)");
                $notice_success = 'Webinar berhasil diperbarui';
            } else {
                $notice_error = 'Gagal memperbarui webinar';
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
    <title>Kelola Webinar - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/berita.css">
    <style>
        .tabs { display:flex; gap:8px; margin:10px 0; flex-wrap: wrap; }
        .tabs a { padding:8px 12px; border:1px solid #ddd; border-radius:6px; text-decoration:none; }
        .tabs a.active { background:#007bff; color:#fff; border-color:#007bff; }
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
                <li><a href="webinar.php" class="active"><i class="fas fa-newspaper"></i> Kelola Konten</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Kelola Webinar</h1>
                <div class="tabs">
                    <a href="kegiatan.php">Kegiatan</a>
                    <a href="webinar.php" class="active">Webinar</a>
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
                    <h3><i class="fas fa-chalkboard-teacher"></i> Tambah Webinar</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="action-form">
                        <input type="hidden" name="action" value="add_webinar">
                        <div class="form-group"><label>Judul</label><input type="text" name="judul" required></div>
                        <div class="form-group"><label>Gambar (opsional)</label><input type="file" name="gambar" accept="image/*"></div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
                    </form>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Gambar</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $wb = mysqli_query($conn, "SELECT * FROM webinar ORDER BY created_at DESC"); ?>
                            <?php if ($wb && mysqli_num_rows($wb) > 0): while ($row = mysqli_fetch_assoc($wb)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                    <td><?php if (!empty($row['gambar'])): ?><img src="../<?php echo htmlspecialchars($row['gambar']); ?>" alt="Gambar Webinar" style="height:40px"><?php else: ?>-<?php endif; ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="showEditForm(<?php echo (int)$row['webinar_id']; ?>, '<?php echo htmlspecialchars(addslashes($row['judul'])); ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" class="inline" onsubmit="return confirm('Hapus webinar ini?')">
                                            <input type="hidden" name="action" value="delete_webinar">
                                            <input type="hidden" name="id" value="<?php echo (int)$row['webinar_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="4" class="text-center">Belum ada webinar</td></tr>
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
            <h2 id="modalTitle">Edit Webinar</h2>
            <form id="editWebinarForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_webinar">
                <input type="hidden" name="id" id="editWebinarId">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" id="editWebinarJudul" required>
                </div>
                <div class="form-group">
                    <label>Gambar Baru (opsional)</label>
                    <input type="file" name="gambar" accept="image/*">
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
            toggleBtn.style.display = sidebar.classList.contains('active') ? 'none' : 'block';
        }
        const modal = document.getElementById('editModal');
        const span = document.getElementsByClassName('close')[0];
        function showEditForm(id, judul) {
            document.getElementById('editWebinarId').value = id;
            document.getElementById('editWebinarJudul').value = judul;
            modal.style.display = 'block';
            modal.style.opacity = '0';
            setTimeout(() => { modal.style.opacity = '1'; }, 10);
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }
        if (span) { span.onclick = closeModal; }
        window.onclick = function(event) { if (event.target == modal) { closeModal(); } }
        document.addEventListener('keydown', function(event) { if (event.key === 'Escape' && modal.style.display === 'block') { closeModal(); } });
    </script>
</body>
</html>


