<?php
session_start();
require_once 'connect/koneksi.php';

function logActivity($conn, $userId, $action)
{
    $uid = $userId ? (int)$userId : 'NULL';
    $actionEsc = mysqli_real_escape_string($conn, $action);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '::1';
    $ipEsc = mysqli_real_escape_string($conn, $ip);
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, action) VALUES ($uid, '$actionEsc')");
}

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? (int)$_SESSION['user_id'] : null;
$userRole = $isLoggedIn ? $_SESSION['role'] : null;

// Get filter parameter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query based on filter
$whereClause = "WHERE hapus=0";
if ($status_filter === 'open') {
    $whereClause .= " AND status='open'";
} elseif ($status_filter === 'closed') {
    $whereClause .= " AND status='closed'";
}

// Fetch jobs based on filter
$jobs = mysqli_query($conn, "SELECT job_id, title, description, location, salary_range, posted_at, status FROM lowongan $whereClause ORDER BY status ASC, posted_at DESC");

// Fetch active popup image
$active_popup = mysqli_query($conn, "SELECT * FROM popup_images WHERE is_active = 1 LIMIT 1");
$popup_data = $active_popup && mysqli_num_rows($active_popup) > 0 ? mysqli_fetch_assoc($active_popup) : null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Waindo Specterra</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/pages.css">
    <link rel="stylesheet" href="assets/css/bergabung.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Popup Modal -->
    <?php if ($popup_data && !isset($_GET['no_popup'])): ?>
        <div class="popup-overlay" id="imagePopup">
            <div class="popup-container <?php echo $popup_data['orientation']; ?>" id="popupContainer">
                <button class="popup-close" onclick="closePopup()" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
                <img src="uploads/popups/<?php echo htmlspecialchars($popup_data['image_filename']); ?>"
                    alt="<?php echo htmlspecialchars($popup_data['title']); ?>"
                    class="popup-image"
                    onload="imageLoaded()"
                    onerror="imageError()">
            </div>
        </div>
    <?php endif; ?>

    <div class="page-container">
        <main class="page-content">
            <div class="container">
                <h1>Lowongan Kerja</h1>
                <p>Temukan lowongan yang sesuai dengan keahlian dan minat Anda.</p>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-briefcase"></i> Daftar Lowongan Pekerjaan</h3>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="filter-section">
                            <div class="filter-group">
                                <label for="status-filter">
                                    <i class="fas fa-filter"></i> Filter Status:
                                </label>
                                <select id="status-filter" onchange="filterJobs(this.value)">
                                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Semua Status</option>
                                    <option value="open" <?php echo $status_filter === 'open' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Ditutup</option>
                                </select>
                            </div>
                        </div>

                        <?php if ($jobs && mysqli_num_rows($jobs) > 0): ?>
                            <?php while ($job = mysqli_fetch_assoc($jobs)): ?>
                                <div class="job-item <?php echo $job['status'] === 'closed' ? 'job-closed' : ''; ?>">
                                    <div class="job-header">
                                        <h4 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h4>
                                        <div class="job-status">
                                            <?php if ($job['status'] === 'open'): ?>
                                                <span class="status-badge status-open">
                                                    <i class="fas fa-check-circle"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge status-closed">
                                                    <i class="fas fa-times-circle"></i> Ditutup
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="job-meta">
                                        Lokasi: <?php echo htmlspecialchars($job['location'] ?: '-'); ?> |
                                        Gaji: <?php echo htmlspecialchars($job['salary_range'] ?: '-'); ?> |
                                        Diposting: <?php echo date('d M Y', strtotime($job['posted_at'])); ?>
                                    </div>
                                    <div class="job-desc-preview">
                                        <?php
                                        $description = strip_tags($job['description']);
                                        echo htmlspecialchars(strlen($description) > 200 ? substr($description, 0, 200) . '...' : $description);
                                        ?>
                                    </div>

                                    <div class="job-actions">
                                        <a href="detail-lowongan-public.php?id=<?php echo $job['job_id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="no-jobs">
                                <i class="fas fa-briefcase"></i>
                                <p>
                                    <?php if ($status_filter === 'open'): ?>
                                        Belum ada lowongan aktif tersedia.
                                    <?php elseif ($status_filter === 'closed'): ?>
                                        Belum ada lowongan yang ditutup.
                                    <?php else: ?>
                                        Tidak ada lowongan tersedia saat ini.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
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
    <script>
    // ===== FILTER JOBS =====
    function filterJobs(status) {
        const currentUrl = new URL(window.location);
        if (status === 'all') {
            currentUrl.searchParams.delete('status');
        } else {
            currentUrl.searchParams.set('status', status);
        }
        // Tambahkan no_popup supaya popup tidak muncul saat filter
        currentUrl.searchParams.set('no_popup', '1');
        window.location.href = currentUrl.toString();
    }

    // ===== POPUP FUNCTIONALITY =====
    <?php if (!empty($popup_data) && !isset($_GET['no_popup'])): ?>
        let popupShown = false;
        let currentIndex = 0;
        const images = document.querySelectorAll('.popup-slideshow .popup-image');

        // Show popup setelah page load
        window.addEventListener('load', function () {
            if (!popupShown) {
                setTimeout(function () {
                    showPopup();
                }, 1500);
            }
        });

        function showPopup() {
            const popup = document.getElementById('imagePopup');
            const container = document.getElementById('popupContainer');
            if (!popup) return;
            container.classList.add('loading');
            popup.classList.add('show');
            popupShown = true;
            startSlideshow();
        }

        function closePopup() {
            const popup = document.getElementById('imagePopup');
            if (!popup) return;
            popup.classList.remove('show');
        }

        function imageLoaded() {
            const container = document.getElementById('popupContainer');
            if (container) container.classList.remove('loading');
        }

        function imageError() {
            const container = document.getElementById('popupContainer');
            if (container) {
                container.classList.remove('loading');
                container.innerHTML = `
                    <div style="padding: 20px; background: white; border-radius: 12px; text-align: center;">
                        <p>Gagal memuat gambar</p>
                        <button onclick="closePopup()" class="btn btn-primary">Tutup</button>
                    </div>`;
            }
        }

        // Slideshow otomatis
        function startSlideshow() {
            if (images.length === 0) return;
            showImage(currentIndex);
            setInterval(() => {
                currentIndex = (currentIndex + 1) % images.length;
                showImage(currentIndex);
            }, 4000); // ganti gambar tiap 4 detik
        }

        function showImage(index) {
            images.forEach((img, i) => {
                img.style.display = (i === index) ? 'block' : 'none';
            });
        }

        // Tutup popup kalau klik overlay
        document.addEventListener('click', function (e) {
            const popup = document.getElementById('imagePopup');
            if (popup && e.target === popup) {
                closePopup();
            }
        });

        // Tutup popup dengan tombol Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closePopup();
            }
        });

        // Cegah klik kanan di gambar popup
        document.addEventListener('DOMContentLoaded', function () {
            const popupImages = document.querySelectorAll('.popup-image');
            popupImages.forEach(img => {
                img.addEventListener('contextmenu', function (e) {
                    e.preventDefault();
                });
            });
        });
    <?php endif; ?>
</script>

</body>

</html>
