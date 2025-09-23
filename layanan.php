<?php
session_start();
require_once 'connect/koneksi.php';

// Get services from database grouped by category
$services_by_category = [];
$categories = ['foto-dan-lidar', 'survey', 'tematik', 'training', 'software'];

foreach ($categories as $category) {
    $query = "SELECT * FROM services WHERE category = '$category' AND status = 'active' ORDER BY order_position ASC";
    $result = mysqli_query($conn, $query);
    $services_by_category[$category] = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $services_by_category[$category][] = $row;
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
    <link rel="stylesheet" href="assets/css/layanan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="page-container">
        <main class="page-content">
            <!-- Hero Section -->
            <section class="hero-section">
                <div class="container">
                    <h1>Layanan Kami</h1>
                </div>
            </section>

            <!-- Services Section -->
            <section class="services-section">
                <div class="container">

                    <!-- Category Tabs -->
                    <div class="category-tabs">
                        <button class="tab-button active" data-tab="foto-dan-lidar">Foto Udara dan Lidar</button>
                        <button class="tab-button" data-tab="survey">Survey</button>
                        <button class="tab-button" data-tab="tematik">Tematik</button>
                        <button class="tab-button" data-tab="training">Training</button>
                        <button class="tab-button" data-tab="software">Software Development</button>
                    </div>

                    <!-- Content Area -->
                    <div class="content-area">
                        <!-- Foto Udara dan Lidar Tab -->
                        <div id="foto-dan-lidar" class="tab-content active">
                            <div class="service-items">
                                <?php if (!empty($services_by_category['foto-dan-lidar'])): ?>
                                    <?php foreach ($services_by_category['foto-dan-lidar'] as $index => $service): ?>
                                        <div class="service-item">
                                            <?php if ($index % 2 == 0): ?>
                                                <div class="service-image">
                                                    <?php if ($service['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="width: 100%; height:100%;">
                                                    <?php else: ?>
                                                        <i class="fas fa-cogs"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-content">
                                                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                                    <?php if ($service['description']): ?>
                                                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($service['features']): ?>
                                                        <ul class="service-features">
                                                            <?php 
                                                            $features = explode('|', $service['features']);
                                                            foreach ($features as $feature): 
                                                                if (trim($feature)):
                                                            ?>
                                                                <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="service-content">
                                                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                                    <?php if ($service['description']): ?>
                                                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($service['features']): ?>
                                                        <ul class="service-features">
                                                            <?php 
                                                            $features = explode('|', $service['features']);
                                                            foreach ($features as $feature): 
                                                                if (trim($feature)):
                                                            ?>
                                                                <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-image">
                                                    <?php if ($service['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="width: 100%; height:100%;">
                                                    <?php else: ?>
                                                        <i class="fas fa-cogs"></i>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="service-item">
                                        <div class="service-content" style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                                            <h3>Belum ada layanan</h3>
                                            <p>Layanan untuk kategori ini sedang dalam pengembangan.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Survey Tab -->
                        <div id="survey" class="tab-content">
                            <div class="service-items">
                                <?php if (!empty($services_by_category['survey'])): ?>
                                    <?php foreach ($services_by_category['survey'] as $index => $service): ?>
                                        <div class="service-item">
                                            <?php if ($index % 2 == 0): ?>
                                                <div class="service-image">
                                                    <?php if ($service['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="width: 100%; height:100%;">
                                                    <?php else: ?>
                                                        <i class="fas fa-map-marked-alt"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-content">
                                                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                                    <?php if ($service['description']): ?>
                                                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($service['features']): ?>
                                                        <ul class="service-features">
                                                            <?php 
                                                            $features = explode('|', $service['features']);
                                                            foreach ($features as $feature): 
                                                                if (trim($feature)):
                                                            ?>
                                                                <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="service-content">
                                                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                                    <?php if ($service['description']): ?>
                                                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($service['features']): ?>
                                                        <ul class="service-features">
                                                            <?php 
                                                            $features = explode('|', $service['features']);
                                                            foreach ($features as $feature): 
                                                                if (trim($feature)):
                                                            ?>
                                                                <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-image">
                                                    <?php if ($service['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="width: 100%; height:100%;">
                                                    <?php else: ?>
                                                        <i class="fas fa-map-marked-alt"></i>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="service-item">
                                        <div class="service-content" style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                                            <h3>Belum ada layanan</h3>
                                            <p>Layanan untuk kategori ini sedang dalam pengembangan.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Tematik Tab -->
                        <div id="tematik" class="tab-content">
                            <div class="service-items">
                                <?php if (!empty($services_by_category['tematik'])): ?>
                                    <?php foreach ($services_by_category['tematik'] as $index => $service): ?>
                                        <div class="service-item">
                                            <?php if ($index % 2 == 0): ?>
                                                <div class="service-image">
                                                    <?php if ($service['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="width: 100%; height:100%;">
                                                    <?php else: ?>
                                                        <i class="fas fa-layer-group"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-content">
                                                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                                    <?php if ($service['description']): ?>
                                                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($service['features']): ?>
                                                        <ul class="service-features">
                                                            <?php 
                                                            $features = explode('|', $service['features']);
                                                            foreach ($features as $feature): 
                                                                if (trim($feature)):
                                                            ?>
                                                                <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="service-content">
                                                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                                    <?php if ($service['description']): ?>
                                                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($service['features']): ?>
                                                        <ul class="service-features">
                                                            <?php 
                                                            $features = explode('|', $service['features']);
                                                            foreach ($features as $feature): 
                                                                if (trim($feature)):
                                                            ?>
                                                                <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-image">
                                                    <?php if ($service['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="width: 100%; height:100%;">
                                                    <?php else: ?>
                                                        <i class="fas fa-layer-group"></i>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="service-item">
                                        <div class="service-content" style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                                            <h3>Belum ada layanan</h3>
                                            <p>Layanan untuk kategori ini sedang dalam pengembangan.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Training Tab -->
                        <div id="training" class="tab-content">
                            <div class="service-items">
                                <?php if (!empty($services_by_category['training'])): ?>
                                    <?php foreach ($services_by_category['training'] as $index => $service): ?>
                                        <div class="service-item">
                                            <?php if ($index % 2 == 0): ?>
                                                <div class="service-image">
                                                    <?php if ($service['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="width: 100%; height:100%;">
                                                    <?php else: ?>
                                                        <i class="fas fa-chalkboard-teacher"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-content">
                                                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                                    <?php if ($service['description']): ?>
                                                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($service['features']): ?>
                                                        <ul class="service-features">
                                                            <?php 
                                                            $features = explode('|', $service['features']);
                                                            foreach ($features as $feature): 
                                                                if (trim($feature)):
                                                            ?>
                                                                <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="service-content">
                                                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                                    <?php if ($service['description']): ?>
                                                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($service['features']): ?>
                                                        <ul class="service-features">
                                                            <?php 
                                                            $features = explode('|', $service['features']);
                                                            foreach ($features as $feature): 
                                                                if (trim($feature)):
                                                            ?>
                                                                <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-image">
                                                    <?php if ($service['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="width: 100%; height:100%;">
                                                    <?php else: ?>
                                                        <i class="fas fa-chalkboard-teacher"></i>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="service-item">
                                        <div class="service-content" style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                                            <h3>Belum ada layanan</h3>
                                            <p>Layanan untuk kategori ini sedang dalam pengembangan.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Software Tab -->
                        <div id="software" class="tab-content">
                            <div class="service-items">
                                <?php if (!empty($services_by_category['software'])): ?>
                                    <?php foreach ($services_by_category['software'] as $index => $service): ?>
                                        <div class="service-item">
                                            <?php if ($index % 2 == 0): ?>
                                                <div class="service-image">
                                                    <?php if ($service['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="width: 100%; height:100%;">
                                                    <?php else: ?>
                                                        <i class="fas fa-code"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-content">
                                                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                                    <?php if ($service['description']): ?>
                                                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($service['features']): ?>
                                                        <ul class="service-features">
                                                            <?php 
                                                            $features = explode('|', $service['features']);
                                                            foreach ($features as $feature): 
                                                                if (trim($feature)):
                                                            ?>
                                                                <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="service-content">
                                                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                                    <?php if ($service['description']): ?>
                                                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($service['features']): ?>
                                                        <ul class="service-features">
                                                            <?php 
                                                            $features = explode('|', $service['features']);
                                                            foreach ($features as $feature): 
                                                                if (trim($feature)):
                                                            ?>
                                                                <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-image">
                                                    <?php if ($service['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="width: 100%; height:100%;">
                                                    <?php else: ?>
                                                        <i class="fas fa-code"></i>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="service-item">
                                        <div class="service-content" style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                                            <h3>Belum ada layanan</h3>
                                            <p>Layanan untuk kategori ini sedang dalam pengembangan.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
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
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked button
                button.classList.add('active');

                // Show corresponding content
                const tabId = button.getAttribute('data-tab');
                const targetContent = document.getElementById(tabId);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
            });
        });
</script>
</body>

</html>