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

function esc($conn, $s) { return mysqli_real_escape_string($conn, $s); }
function logActivity($conn, $actor_user_id, $action) {
	$actor_user_id = (int)$actor_user_id;
	$action = mysqli_real_escape_string($conn, $action);
	$ip = $_SERVER['REMOTE_ADDR'] ?? '::1';
	mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action, ip_address) VALUES ($actor_user_id, '$action', '$ip')");
}

// Ensure upload directories exist
$dirs = array(
	'../uploads/kegiatan',
	'../uploads/webinar',
	'../uploads/live',
	'../uploads/galeri'
);
foreach ($dirs as $d) { if (!is_dir($d)) { @mkdir($d, 0777, true); } }

// Handle actions
$notice_error = null; $notice_success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
	$action = $_POST['action'];

	if ($action === 'add_kegiatan') {
		$judul = esc($conn, $_POST['judul'] ?? '');
		$deskripsi = esc($conn, $_POST['deskripsi'] ?? '');
		if ($judul === '') { $notice_error = 'Judul Kegiatan wajib diisi'; }
		else {
			if (mysqli_query($conn, "INSERT INTO kegiatan (judul, deskripsi) VALUES ('$judul', '$deskripsi')")) {
				$kegiatan_id = mysqli_insert_id($conn);
				// multiple photos support
				if (!empty($_FILES['foto']['name'][0])) {
					for ($i=0; $i<count($_FILES['foto']['name']); $i++) {
						if (!is_uploaded_file($_FILES['foto']['tmp_name'][$i])) continue;
						$fn = time().'_'.preg_replace('/[^A-Za-z0-9_\.-]/','_', $_FILES['foto']['name'][$i]);
						$dest = "../uploads/kegiatan/$fn";
						if (@move_uploaded_file($_FILES['foto']['tmp_name'][$i], $dest)) {
							$rel = 'uploads/kegiatan/'.$fn;
							mysqli_query($conn, "INSERT INTO kegiatan_foto (kegiatan_id, foto) VALUES ($kegiatan_id, '".esc($conn,$rel)."')");
						}
					}
				}
				logActivity($conn, $user_id, "Konten: tambah kegiatan #$kegiatan_id ($judul)");
				$notice_success = 'Kegiatan berhasil ditambahkan';
			} else { $notice_error = 'Gagal menambah kegiatan'; }
		}
	}

	if ($action === 'delete_kegiatan' && isset($_POST['id'])) {
		$id = (int)$_POST['id'];
		$res = mysqli_query($conn, "SELECT foto FROM kegiatan_foto WHERE kegiatan_id=$id");
		if ($res) { while ($r = mysqli_fetch_assoc($res)) { $p = '../'.ltrim($r['foto'],'/'); if (is_file($p)) { @unlink($p); } } }
		mysqli_query($conn, "DELETE FROM kegiatan_foto WHERE kegiatan_id=$id");
		if (mysqli_query($conn, "DELETE FROM kegiatan WHERE kegiatan_id=$id")) { logActivity($conn, $user_id, "Konten: hapus kegiatan #$id"); $notice_success = 'Kegiatan dihapus'; } else { $notice_error = 'Gagal menghapus kegiatan'; }
	}

	if ($action === 'add_webinar') {
		$judul = esc($conn, $_POST['judul'] ?? '');
		$gambar_path = null;
		if (isset($_FILES['gambar']) && is_uploaded_file($_FILES['gambar']['tmp_name'])) {
			$fn = time().'_'.preg_replace('/[^A-Za-z0-9_\.-]/','_', $_FILES['gambar']['name']);
			$dest = "../uploads/webinar/$fn";
			if (@move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) { $gambar_path = 'uploads/webinar/'.$fn; }
		}
		if ($judul === '') { $notice_error = 'Judul Webinar wajib diisi'; }
		else {
			$q = "INSERT INTO webinar (judul, gambar) VALUES ('$judul', ".($gambar_path?"'".esc($conn,$gambar_path)."'":"NULL").")";
			if (mysqli_query($conn, $q)) { $wid = mysqli_insert_id($conn); logActivity($conn, $user_id, "Konten: tambah webinar #$wid ($judul)"); $notice_success = 'Webinar berhasil ditambahkan'; } else { $notice_error = 'Gagal menambah webinar'; }
		}
	}

	if ($action === 'delete_webinar' && isset($_POST['id'])) {
		$id = (int)$_POST['id'];
		$res = mysqli_query($conn, "SELECT gambar FROM webinar WHERE webinar_id=$id");
		if ($res) { $r = mysqli_fetch_assoc($res); if (!empty($r['gambar'])) { $p = '../'.ltrim($r['gambar'],'/'); if (is_file($p)) { @unlink($p); } } }
		if (mysqli_query($conn, "DELETE FROM webinar WHERE webinar_id=$id")) { logActivity($conn, $user_id, "Konten: hapus webinar #$id"); $notice_success = 'Webinar dihapus'; } else { $notice_error = 'Gagal menghapus webinar'; }
	}

	if ($action === 'add_live') {
		$judul = esc($conn, $_POST['judul'] ?? '');
		$tipe = $_POST['tipe'] === 'mp4' ? 'mp4' : 'youtube';
		$url = esc($conn, $_POST['url'] ?? '');
		if ($tipe === 'mp4' && isset($_FILES['file_mp4']) && is_uploaded_file($_FILES['file_mp4']['tmp_name'])) {
			$fn = time().'_'.preg_replace('/[^A-Za-z0-9_\.-]/','_', $_FILES['file_mp4']['name']);
			$dest = "../uploads/live/$fn";
			if (@move_uploaded_file($_FILES['file_mp4']['tmp_name'], $dest)) { $url = esc($conn, 'uploads/live/'.$fn); }
		}
		if ($judul === '' || $url === '') { $notice_error = 'Judul dan URL/File wajib diisi'; }
		else {
			$q = "INSERT INTO live_streaming (judul, tipe, url) VALUES ('$judul', '$tipe', '$url')";
			if (mysqli_query($conn, $q)) { $lid = mysqli_insert_id($conn); logActivity($conn, $user_id, "Konten: tambah live_streaming #$lid ($tipe)"); $notice_success = 'Live streaming berhasil ditambahkan'; } else { $notice_error = 'Gagal menambah live streaming'; }
		}
	}

	if ($action === 'delete_live' && isset($_POST['id'])) {
		$id = (int)$_POST['id'];
		$res = mysqli_query($conn, "SELECT tipe, url FROM live_streaming WHERE streaming_id=$id");
		if ($res) { $r = mysqli_fetch_assoc($res); if ($r && $r['tipe']==='mp4') { $p = '../'.ltrim($r['url'],'/'); if (is_file($p)) { @unlink($p); } } }
		if (mysqli_query($conn, "DELETE FROM live_streaming WHERE streaming_id=$id")) { logActivity($conn, $user_id, "Konten: hapus live_streaming #$id"); $notice_success = 'Live streaming dihapus'; } else { $notice_error = 'Gagal menghapus live streaming'; }
	}

	if ($action === 'add_galeri') {
		$judul = esc($conn, $_POST['judul'] ?? '');
		if ($judul === '') { $notice_error = 'Judul Galeri wajib diisi'; }
		else {
			if (mysqli_query($conn, "INSERT INTO galeri (judul) VALUES ('$judul')")) {
				$galeri_id = mysqli_insert_id($conn);
				logActivity($conn, $user_id, "Konten: tambah galeri #$galeri_id ($judul)");
				if (!empty($_FILES['foto']['name'][0])) {
					for ($i=0; $i<count($_FILES['foto']['name']); $i++) {
						if (!is_uploaded_file($_FILES['foto']['tmp_name'][$i])) continue;
						$fn = time().'_'.preg_replace('/[^A-Za-z0-9_\.-]/','_', $_FILES['foto']['name'][$i]);
						$dest = "../uploads/galeri/$fn";
						if (@move_uploaded_file($_FILES['foto']['tmp_name'][$i], $dest)) {
							$rel = 'uploads/galeri/'.$fn;
							mysqli_query($conn, "INSERT INTO galeri_foto (galeri_id, foto) VALUES ($galeri_id, '".esc($conn,$rel)."')");
						}
					}
				}
				$notice_success = 'Galeri berhasil ditambahkan';
			} else { $notice_error = 'Gagal menambah galeri'; }
		}
	}

	if ($action === 'delete_galeri' && isset($_POST['id'])) {
		$id = (int)$_POST['id'];
		$res = mysqli_query($conn, "SELECT foto FROM galeri_foto WHERE galeri_id=$id");
		if ($res) { while ($r = mysqli_fetch_assoc($res)) { $p = '../'.ltrim($r['foto'],'/'); if (is_file($p)) { @unlink($p); } } }
		mysqli_query($conn, "DELETE FROM galeri_foto WHERE galeri_id=$id");
		if (mysqli_query($conn, "DELETE FROM galeri WHERE galeri_id=$id")) { logActivity($conn, $user_id, "Konten: hapus galeri #$id"); $notice_success = 'Galeri dihapus'; } else { $notice_error = 'Gagal menghapus galeri'; }
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
                <li><a href="berita.php" class="active"><i class="fas fa-newspaper"></i> Kelola Berita</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Kelola Berita</h1>
                <p>Tambah dan kelola Kegiatan, Webinar, Live Streaming, dan Galeri Foto</p>
            </div>

            <?php if ($notice_error): ?>
                <div class="alert alert-danger"><?php echo $notice_error; ?></div>
            <?php endif; ?>
            <?php if ($notice_success): ?>
                <div class="alert alert-success"><?php echo $notice_success; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header"><h3><i class="fas fa-calendar"></i> Kegiatan</h3></div>
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
                        <thead><tr><th>Judul</th><th>Dibuat</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php $kg = mysqli_query($conn, "SELECT * FROM kegiatan ORDER BY created_at DESC"); ?>
                            <?php if ($kg && mysqli_num_rows($kg)>0): while ($row=mysqli_fetch_assoc($kg)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <form method="POST" class="inline" onsubmit="return confirm('Hapus kegiatan ini?')">
                                        <input type="hidden" name="action" value="delete_kegiatan">
                                        <input type="hidden" name="id" value="<?php echo (int)$row['kegiatan_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="3" class="text-center">Belum ada kegiatan</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3><i class="fas fa-chalkboard-teacher"></i> Webinar</h3></div>
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
                        <thead><tr><th>Judul</th><th>Gambar</th><th>Dibuat</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php $wb = mysqli_query($conn, "SELECT * FROM webinar ORDER BY created_at DESC"); ?>
                            <?php if ($wb && mysqli_num_rows($wb)>0): while ($row=mysqli_fetch_assoc($wb)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                <td><?php if (!empty($row['gambar'])): ?><img src="../<?php echo htmlspecialchars($row['gambar']); ?>" alt="thumb" style="height:40px"><?php else: ?>-<?php endif; ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
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

            <div class="card">
                <div class="card-header"><h3><i class="fas fa-video"></i> Live Streaming</h3></div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="action-form">
                        <input type="hidden" name="action" value="add_live">
                        <div class="form-group"><label>Judul</label><input type="text" name="judul" required></div>
                        <div class="form-group"><label>Tipe</label><select name="tipe"><option value="youtube">YouTube (embed)</option><option value="mp4">MP4 (upload)</option></select></div>
                        <div class="form-group"><label>URL (YouTube)</label><input type="text" name="url" placeholder="https://www.youtube.com/embed/..." ></div>
                        <div class="form-group"><label>File MP4 (untuk MP4)</label><input type="file" name="file_mp4" accept="video/mp4"></div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
                    </form>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Judul</th><th>Tipe</th><th>URL/File</th><th>Dibuat</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php $lv = mysqli_query($conn, "SELECT * FROM live_streaming ORDER BY created_at DESC"); ?>
                            <?php if ($lv && mysqli_num_rows($lv)>0): while ($row=mysqli_fetch_assoc($lv)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                <td><?php echo htmlspecialchars($row['tipe']); ?></td>
                                <td style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; "><?php echo htmlspecialchars($row['url']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <form method="POST" class="inline" onsubmit="return confirm('Hapus live streaming ini?')">
                                        <input type="hidden" name="action" value="delete_live">
                                        <input type="hidden" name="id" value="<?php echo (int)$row['streaming_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center">Belum ada live streaming</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3><i class="fas fa-images"></i> Galeri Foto</h3></div>
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
                        <thead><tr><th>Judul</th><th>Jumlah Foto</th><th>Dibuat</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php $gl = mysqli_query($conn, "SELECT g.*, (SELECT COUNT(*) FROM galeri_foto gf WHERE gf.galeri_id=g.galeri_id) AS jml FROM galeri g ORDER BY created_at DESC"); ?>
                            <?php if ($gl && mysqli_num_rows($gl)>0): while ($row=mysqli_fetch_assoc($gl)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                <td><?php echo (int)$row['jml']; ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <form method="POST" class="inline" onsubmit="return confirm('Hapus galeri ini? Semua foto akan terhapus.')">
                                        <input type="hidden" name="action" value="delete_galeri">
                                        <input type="hidden" name="id" value="<?php echo (int)$row['galeri_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center">Belum ada galeri</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
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
    </script>
</body>

</html>
