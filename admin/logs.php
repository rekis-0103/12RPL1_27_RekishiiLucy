<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// User data
$user_id   = $_SESSION['user_id'];
$username  = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role      = $_SESSION['role'];

// Entity utama
$entities = [
    'login'       => 'Login',
    'logout'      => 'Logout',
    'user'        => 'User',
    'kegiatan'    => 'Kegiatan',
    'webinar'     => 'Webinar',
    'live_stream' => 'Live Stream',
    'galeri'      => 'Galeri',
    'layanan'    => 'Layanan',
    'produk'      => 'Produk',
];

// Get filter parameters
$entity_filter = isset($_GET['entity']) ? strtolower(mysqli_real_escape_string($conn, $_GET['entity'])) : '';
$crud_filter   = isset($_GET['crud']) ? strtolower(mysqli_real_escape_string($conn, $_GET['crud'])) : '';
$date_from     = isset($_GET['date_from']) ? mysqli_real_escape_string($conn, $_GET['date_from']) : '';
$date_to       = isset($_GET['date_to']) ? mysqli_real_escape_string($conn, $_GET['date_to']) : '';
$user_filter   = isset($_GET['user_id']) ? mysqli_real_escape_string($conn, $_GET['user_id']) : '';

// Ambil daftar user
$user_query = "SELECT DISTINCT u.user_id, u.username, u.full_name
               FROM log_aktivitas l
               JOIN users u ON l.user_id = u.user_id
               WHERE u.user_id IS NOT NULL
               ORDER BY u.full_name ASC";
$user_result = mysqli_query($conn, $user_query);

// Build query with filters
$where_conditions = [];
$where_conditions[] = "1=1";

if ($entity_filter) {
    if ($crud_filter) {
        if ($crud_filter === 'create') {
            $where_conditions[] = "(l.action LIKE '%tambah $entity_filter%' OR l.action LIKE '%create $entity_filter%')";
        } elseif ($crud_filter === 'read') {
            $where_conditions[] = "(l.action LIKE '%lihat $entity_filter%' OR l.action LIKE '%view $entity_filter%')";
        } elseif ($crud_filter === 'update') {
            $where_conditions[] = "(l.action LIKE '%edit $entity_filter%' OR l.action LIKE '%update $entity_filter%')";
        } elseif ($crud_filter === 'delete') {
            $where_conditions[] = "(l.action LIKE '%hapus $entity_filter%' OR l.action LIKE '%delete $entity_filter%')";
        }
    } else {
        // tanpa CRUD → semua aksi entitas
        $where_conditions[] = "l.action LIKE '%$entity_filter%'";
    }
}

if ($date_from) {
    $where_conditions[] = "DATE(l.log_time) >= '$date_from'";
}

if ($date_to) {
    $where_conditions[] = "DATE(l.log_time) <= '$date_to'";
}

if ($user_filter) {
    $where_conditions[] = "l.user_id = '$user_filter'";
}

$where_clause = implode(' AND ', $where_conditions);

$logs_query = "SELECT l.*, u.username, u.full_name 
               FROM log_aktivitas l 
               LEFT JOIN users u ON l.user_id = u.user_id 
               WHERE $where_clause
               ORDER BY l.log_time DESC";

$logs_result = mysqli_query($conn, $logs_query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas - PT Waindo Specterra</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/logs.css">
</head>

<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Log Aktivitas</h3>
                <p>Selamat datang, <?php echo htmlspecialchars($full_name); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Kelola User</a></li>
                <li><a href="logs.php" class="active"><i class="fas fa-history"></i> Log Aktivitas</a></li>
                <li><a href="pendidikan.php"><i class="fas fa-graduation-cap"></i> Pendidikan</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Log Aktivitas</h1>
                <p>Monitor aktivitas pengguna sistem</p>
            </div>

            <!-- Filter Section -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-filter"></i> Filter Log</h3>
                </div>
                <div class="filter-form">
                    <form method="GET" class="filter-grid">
                        <div class="form-group">
                            <label for="entity">Entitas</label>
                            <select id="entity" name="entity">
                                <option value="">Semua Entitas</option>
                                <?php foreach ($entities as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>"
                                        <?php echo ($entity_filter == $value) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="crud">CRUD</label>
                            <select id="crud" name="crud" <?php echo $entity_filter ? '' : 'disabled'; ?>>
                                <option value="">Semua</option>
                                <option value="create" <?php echo ($crud_filter == 'create') ? 'selected' : ''; ?>>Create</option>
                                <option value="read" <?php echo ($crud_filter == 'read') ? 'selected' : ''; ?>>Read</option>
                                <option value="update" <?php echo ($crud_filter == 'update') ? 'selected' : ''; ?>>Update</option>
                                <option value="delete" <?php echo ($crud_filter == 'delete') ? 'selected' : ''; ?>>Delete</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user_id">Filter User</label>
                            <select id="user_id" name="user_id">
                                <option value="">Semua User</option>
                                <?php while ($user = mysqli_fetch_assoc($user_result)): ?>
                                    <option value="<?php echo htmlspecialchars($user['user_id']); ?>"
                                        <?php echo ($user_filter == $user['user_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date_from">Tanggal Dari</label>
                            <input type="date" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                        </div>
                        <div class="form-group">
                            <label for="date_to">Tanggal Sampai</label>
                            <input type="date" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                        </div>
                        <div class="form-group filter-buttons">
                            <label>&nbsp;</label>
                            <div class="button-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="logs.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Daftar Log Aktivitas</h3>
                    <div class="export-section">
                        <button class="btn btn-success" onclick="exportLogs()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>

                <div class="logs-table">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($logs_result) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($log = mysqli_fetch_assoc($logs_result)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <div class="log-time">
                                                <div class="date"><?php echo date('d/m/Y', strtotime($log['log_time'])); ?></div>
                                                <div class="time"><?php echo date('H:i:s', strtotime($log['log_time'])); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <div class="username"><?php echo htmlspecialchars($log['username'] ?: 'Unknown'); ?></div>
                                                <div class="full-name"><?php echo htmlspecialchars($log['full_name'] ?: 'N/A'); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="action-badge action-<?php echo strtolower($log['action']); ?>">
                                                <i class="fas fa-<?php echo getActionIcon($log['action']); ?>"></i>
                                                <?php echo htmlspecialchars($log['action']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data log</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.mobile-toggle');
            sidebar.classList.toggle('active');
            toggleBtn.style.display = sidebar.classList.contains('active') ? "none" : "block";
        }

        // Enable/disable CRUD berdasarkan entity
        document.getElementById('entity').addEventListener('change', function() {
            const crudDropdown = document.getElementById('crud');
            const selectedEntity = this.value;

            // Daftar entitas yang tidak memerlukan CRUD
            const nonCrudEntities = ['login', 'logout'];

            if (selectedEntity && !nonCrudEntities.includes(selectedEntity)) {
                // Entity dipilih dan BUKAN login/logout -> aktifkan CRUD
                crudDropdown.disabled = false;
            } else {
                // Tidak ada entity atau entity adalah login/logout -> nonaktifkan CRUD
                crudDropdown.disabled = true;
                crudDropdown.value = '';
            }
        });

        function exportLogs() {
            const urlParams = new URLSearchParams(window.location.search);
            const entity = urlParams.get('entity') || '';
            const crud = urlParams.get('crud') || '';
            const dateFrom = urlParams.get('date_from') || '';
            const dateTo = urlParams.get('date_to') || '';
            const userId = urlParams.get('user_id') || '';

            let exportUrl = 'export_logs.php?';
            if (entity) exportUrl += 'entity=' + entity + '&';
            if (crud) exportUrl += 'crud=' + crud + '&';
            if (dateFrom) exportUrl += 'date_from=' + dateFrom + '&';
            if (dateTo) exportUrl += 'date_to=' + dateTo + '&';
            if (userId) exportUrl += 'user_id=' + userId;

            window.location.href = exportUrl;
        }
    </script>
</body>

</html>

<?php
// Helper function untuk ikon
function getActionIcon($action)
{
    $action_lower = strtolower($action);

    if (strpos($action_lower, 'login') !== false) return 'sign-in-alt';
    if (strpos($action_lower, 'logout') !== false) return 'sign-out-alt';
    if (strpos($action_lower, 'tambah') !== false || strpos($action_lower, 'create') !== false) return 'plus';
    if (strpos($action_lower, 'edit') !== false || strpos($action_lower, 'update') !== false) return 'edit';
    if (strpos($action_lower, 'hapus') !== false || strpos($action_lower, 'delete') !== false) return 'trash';
    if (strpos($action_lower, 'lihat') !== false || strpos($action_lower, 'view') !== false) return 'eye';
    if (strpos($action_lower, 'export') !== false) return 'download';
    if (strpos($action_lower, 'import') !== false) return 'upload';

    return 'user';
}
?>