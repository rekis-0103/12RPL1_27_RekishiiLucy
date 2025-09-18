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

// Fetch active popup images (allow multiple active)
$active_popup_rs = mysqli_query($conn, "SELECT * FROM popup_images WHERE is_active = 1 ORDER BY created_at DESC");
$popup_list = [];
if ($active_popup_rs && mysqli_num_rows($active_popup_rs) > 0) {
    while ($row = mysqli_fetch_assoc($active_popup_rs)) {
        $popup_list[] = $row;
    }
}
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

    <!-- Popup Modal (supports multiple active images as slider) -->
    <?php if (!empty($popup_list) && !isset($_GET['no_popup'])): ?>
        <div class="popup-overlay" id="imagePopup">
            <?php
                $uniqueOrientations = array_unique(array_map(function($p){return $p['orientation'];}, $popup_list));
                $containerMode = count($uniqueOrientations) === 1 ? $popup_list[0]['orientation'] : 'mixed';
            ?>
            <div class="popup-container <?php echo $containerMode; ?>" id="popupContainer">
                <div class="popup-slider" id="popupSlider">
                    <?php foreach ($popup_list as $idx => $p): ?>
                        <div class="popup-slide" data-index="<?php echo $idx; ?>">
                            <img src="uploads/popups/<?php echo htmlspecialchars($p['image_filename']); ?>"
                                alt="<?php echo htmlspecialchars($p['title']); ?>"
                                class="popup-image"
                                onload="imageLoaded()"
                                onerror="imageError()">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="popup-controls">
                    <?php if (count($popup_list) > 1): ?>
                        <button class="popup-ctrl-btn" id="popupPrev" aria-label="Sebelumnya"><i class="fas fa-chevron-left"></i></button>
                        <div class="popup-dots" id="popupDots">
                            <?php foreach ($popup_list as $idx => $p): ?>
                                <button class="popup-dot" onclick="goToSlide(<?php echo $idx; ?>)" aria-label="Slide <?php echo $idx+1; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <button class="popup-ctrl-btn" id="popupNext" aria-label="Berikutnya"><i class="fas fa-chevron-right"></i></button>
                    <?php endif; ?>
                    <button class="popup-ctrl-btn close" id="popupClose" aria-label="Tutup"><i class="fas fa-times"></i></button>
                </div>
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

    // ===== POPUP FUNCTIONALITY (Multiple with slider) =====
    <?php if (!empty($popup_list) && !isset($_GET['no_popup'])): ?>
        let popupShown = false;
        let currentIndex = 0;
        let autoTimer = null;

        // Show popup setelah page load
        window.addEventListener('load', function () {
            if (!popupShown) {
                setTimeout(function () {
                    showPopup();
                }, 800);
            }
        });

        function showPopup() {
            const popup = document.getElementById('imagePopup');
            const container = document.getElementById('popupContainer');
            if (!popup) return;
            container.classList.add('loading');
            popup.classList.add('show');
            popupShown = true;
            initSlider();
        }

        function closePopup() {
            const popup = document.getElementById('imagePopup');
            if (!popup) return;
            popup.classList.remove('show');
            stopAuto();
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
                        <button onclick=\"closePopup()\" class=\"btn btn-primary\">Tutup</button>
                    </div>`;
            }
        }

        function initSlider() {
            const slides = document.querySelectorAll('#popupSlider .popup-slide');
            const dots = document.querySelectorAll('#popupDots .popup-dot');
            if (slides.length === 0) return;
            goToSlide(0);
            startAuto();
            // Prevent context menu on images
            document.querySelectorAll('.popup-image').forEach(img => {
                img.addEventListener('contextmenu', e => e.preventDefault());
            });
            // Wire grouped controls
            const prevBtn = document.getElementById('popupPrev');
            const nextBtn = document.getElementById('popupNext');
            const closeBtn = document.getElementById('popupClose');
            if (prevBtn) prevBtn.onclick = () => { stopAuto(); prevSlide(); };
            if (nextBtn) nextBtn.onclick = () => { stopAuto(); nextSlide(); };
            if (closeBtn) closeBtn.onclick = () => closePopup();
        }

        function updateUI(index) {
            const slides = document.querySelectorAll('#popupSlider .popup-slide');
            const dots = document.querySelectorAll('#popupDots .popup-dot');
            slides.forEach((s, i) => {
                s.style.display = (i === index) ? 'block' : 'none';
            });
            dots.forEach((d, i) => {
                d.classList.toggle('active', i === index);
            });
        }

        function goToSlide(index) {
            const slides = document.querySelectorAll('#popupSlider .popup-slide');
            if (slides.length === 0) return;
            currentIndex = (index + slides.length) % slides.length;
            updateUI(currentIndex);
            restartAuto();
        }

        function nextSlide() { goToSlide(currentIndex + 1); }
        function prevSlide() { goToSlide(currentIndex - 1); }

        function startAuto() {
            stopAuto();
            autoTimer = setInterval(() => {
                nextSlide();
            }, 10000);
        }
        function stopAuto() {
            if (autoTimer) {
                clearInterval(autoTimer);
                autoTimer = null;
            }
        }
        function restartAuto() { startAuto(); }

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
    <?php endif; ?>
</script>

</body>

</html>
