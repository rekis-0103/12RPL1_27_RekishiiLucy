<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in and has konten role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'konten') {
    header('Location: ../login.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role = $_SESSION['role'];

function esc($conn, $s)
{
    return mysqli_real_escape_string($conn, $s);
}
function logActivity($conn, $actor_user_id, $action)
{
    $actor_user_id = (int)$actor_user_id;
    $action = mysqli_real_escape_string($conn, $action);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '::1';
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($actor_user_id, '$action')");
}

// Ensure upload directories exist
$dirs = array(
    '../uploads/kegiatan',
    '../uploads/webinar',
    '../uploads/live',
    '../uploads/galeri'
);
foreach ($dirs as $d) {
    if (!is_dir($d)) {
        @mkdir($d, 0777, true);
    }
}

// Handle actions
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
                // multiple photos support
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

    if ($action === 'add_galeri') {
        $judul = esc($conn, $_POST['judul'] ?? '');
        if ($judul === '') {
            $notice_error = 'Judul Galeri wajib diisi';
        } else {
            if (mysqli_query($conn, "INSERT INTO galeri (judul) VALUES ('$judul')")) {
                $galeri_id = mysqli_insert_id($conn);
                logActivity($conn, $user_id, "Konten: tambah galeri #$galeri_id ($judul)");
                if (!empty($_FILES['foto']['name'][0])) {
                    for ($i = 0; $i < count($_FILES['foto']['name']); $i++) {
                        if (!is_uploaded_file($_FILES['foto']['tmp_name'][$i])) continue;
                        $fn = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['foto']['name'][$i]);
                        $dest = "../uploads/galeri/$fn";
                        if (@move_uploaded_file($_FILES['foto']['tmp_name'][$i], $dest)) {
                            $rel = 'uploads/galeri/' . $fn;
                            mysqli_query($conn, "INSERT INTO galeri_foto (galeri_id, foto) VALUES ($galeri_id, '" . esc($conn, $rel) . "')");
                        }
                    }
                }
                $notice_success = 'Galeri berhasil ditambahkan';
            } else {
                $notice_error = 'Gagal menambah galeri';
            }
        }
    }

    if ($action === 'delete_galeri' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $res = mysqli_query($conn, "SELECT foto FROM galeri_foto WHERE galeri_id=$id");
        if ($res) {
            while ($r = mysqli_fetch_assoc($res)) {
                $p = '../' . ltrim($r['foto'], '/');
                if (is_file($p)) {
                    @unlink($p);
                }
            }
        }
        mysqli_query($conn, "DELETE FROM galeri_foto WHERE galeri_id=$id");
        if (mysqli_query($conn, "DELETE FROM galeri WHERE galeri_id=$id")) {
            logActivity($conn, $user_id, "Konten: hapus galeri #$id");
            $notice_success = 'Galeri dihapus';
        } else {
            $notice_error = 'Gagal menghapus galeri';
        }
    }

    // Edit functionality
    if ($action === 'edit_kegiatan' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $judul = esc($conn, $_POST['judul'] ?? '');
        $deskripsi = esc($conn, $_POST['deskripsi'] ?? '');
        if ($judul === '') {
            $notice_error = 'Judul Kegiatan wajib diisi';
        } else {
            if (mysqli_query($conn, "UPDATE kegiatan SET judul='$judul', deskripsi='$deskripsi' WHERE kegiatan_id=$id")) {
                // Handle new photos if uploaded
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

    if ($action === 'edit_webinar' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $judul = esc($conn, $_POST['judul'] ?? '');
        $gambar_path = null;
        if (isset($_FILES['gambar']) && is_uploaded_file($_FILES['gambar']['tmp_name'])) {
            $fn = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['gambar']['name']);
            $dest = "../uploads/webinar/$fn";
            if (@move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
                $gambar_path = 'uploads/webinar/' . $fn;
                // Delete old image if exists
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
                // Delete old file if exists
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

    if ($action === 'edit_galeri' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $judul = esc($conn, $_POST['judul'] ?? '');
        if ($judul === '') {
            $notice_error = 'Judul Galeri wajib diisi';
        } else {
            if (mysqli_query($conn, "UPDATE galeri SET judul='$judul' WHERE galeri_id=$id")) {
                logActivity($conn, $user_id, "Konten: edit galeri #$id ($judul)");
                // Handle new photos if uploaded
                if (!empty($_FILES['foto']['name'][0])) {
                    for ($i = 0; $i < count($_FILES['foto']['name']); $i++) {
                        if (!is_uploaded_file($_FILES['foto']['tmp_name'][$i])) continue;
                        $fn = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['foto']['name'][$i]);
                        $dest = "../uploads/galeri/$fn";
                        if (@move_uploaded_file($_FILES['foto']['tmp_name'][$i], $dest)) {
                            $rel = 'uploads/galeri/' . $fn;
                            mysqli_query($conn, "INSERT INTO galeri_foto (galeri_id, foto) VALUES ($id, '" . esc($conn, $rel) . "')");
                        }
                    }
                }
                $notice_success = 'Galeri berhasil diperbarui';
            } else {
                $notice_error = 'Gagal memperbarui galeri';
            }
        }
    }

    // Delete individual photos
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

    if ($action === 'delete_galeri_foto' && isset($_POST['foto_id'])) {
        $foto_id = (int)$_POST['foto_id'];
        $res = mysqli_query($conn, "SELECT foto FROM galeri_foto WHERE foto_id=$foto_id");
        if ($res) {
            $r = mysqli_fetch_assoc($res);
            if ($r) {
                $p = '../' . ltrim($r['foto'], '/');
                if (is_file($p)) {
                    @unlink($p);
                }
            }
        }
        if (mysqli_query($conn, "DELETE FROM galeri_foto WHERE foto_id=$foto_id")) {
            $notice_success = 'Foto galeri dihapus';
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
                <li><a href="berita.php" class="active"><i class="fas fa-newspaper"></i> Kelola Konten</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Kelola Konten</h1>
                <p>Tambah dan kelola Kegiatan, Webinar, Live Streaming, dan Galeri Foto</p>
            </div>

            <?php if ($notice_error): ?>
                <div class="alert alert-danger"><?php echo $notice_error; ?></div>
            <?php endif; ?>
            <?php if ($notice_success): ?>
                <div class="alert alert-success"><?php echo $notice_success; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar"></i> Kegiatan</h3>
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
                                            <button type="button" class="btn btn-primary btn-sm" onclick="showEditForm('kegiatan', <?php echo (int)$row['kegiatan_id']; ?>, '<?php echo htmlspecialchars(addslashes($row['judul'])); ?>', '<?php echo htmlspecialchars(addslashes($row['deskripsi'])); ?>')">
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

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chalkboard-teacher"></i> Webinar</h3>
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
                                            <button type="button" class="btn btn-primary btn-sm" onclick="showEditForm('webinar', <?php echo (int)$row['webinar_id']; ?>, '<?php echo htmlspecialchars(addslashes($row['judul'])); ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form method="POST" class="inline" onsubmit="return confirm('Hapus webinar ini?')">
                                                <input type="hidden" name="action" value="delete_webinar">
                                                <input type="hidden" name="id" value="<?php echo (int)$row['webinar_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada webinar</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-video"></i> Live Streaming</h3>
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

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-images"></i> Galeri Foto</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="action-form">
                        <input type="hidden" name="action" value="add_galeri">
                        <div class="form-group"><label>Judul</label><input type="text" name="judul" required></div>
                        <div class="form-group"><label>Foto</label><input type="file" name="foto[]" accept="image/*" multiple required></div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
                    </form>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Foto</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $gl = mysqli_query($conn, "SELECT g.*, (SELECT COUNT(*) FROM galeri_foto gf WHERE gf.galeri_id=g.galeri_id) AS jml FROM galeri g ORDER BY created_at DESC"); ?>
                            <?php if ($gl && mysqli_num_rows($gl) > 0): while ($row = mysqli_fetch_assoc($gl)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                        <td>
                                            <?php
                                            $fotos = mysqli_query($conn, "SELECT * FROM galeri_foto WHERE galeri_id=" . (int)$row['galeri_id']);
                                            if ($fotos && mysqli_num_rows($fotos) > 0):
                                                while ($foto = mysqli_fetch_assoc($fotos)): ?>
                                                    <div class="photo-item">
                                                        <img src="../<?php echo htmlspecialchars($foto['foto']); ?>" alt="Foto Galeri" style="height:40px; margin:2px;">
                                                        <form method="POST" class="inline" onsubmit="return confirm('Hapus foto ini?')">
                                                            <input type="hidden" name="action" value="delete_galeri_foto">
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
                                            <button type="button" class="btn btn-primary btn-sm" onclick="showEditForm('galeri', <?php echo (int)$row['galeri_id']; ?>, '<?php echo htmlspecialchars(addslashes($row['judul'])); ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form method="POST" class="inline" onsubmit="return confirm('Hapus galeri ini? Semua foto akan terhapus.')">
                                                <input type="hidden" name="action" value="delete_galeri">
                                                <input type="hidden" name="id" value="<?php echo (int)$row['galeri_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada galeri</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Edit Konten</h2>

            <!-- Edit Kegiatan Form -->
            <form id="editKegiatanForm" method="POST" enctype="multipart/form-data" style="display: none;">
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

            <!-- Edit Webinar Form -->
            <form id="editWebinarForm" method="POST" enctype="multipart/form-data" style="display: none;">
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

            <!-- Edit Live Streaming Form -->
            <form id="editLiveForm" method="POST" enctype="multipart/form-data" style="display: none;">
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

            <!-- Edit Galeri Form -->
            <form id="editGaleriForm" method="POST" enctype="multipart/form-data" style="display: none;">
                <input type="hidden" name="action" value="edit_galeri">
                <input type="hidden" name="id" id="editGaleriId">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" id="editGaleriJudul" required>
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
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-toggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
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