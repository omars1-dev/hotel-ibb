<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

// إحصائيات سريعة
$hotelsCount   = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
$bookingsCount = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$usersCount    = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

$pageTitle = 'لوحة التحكم';
include __DIR__ . '/includes/admin_header.php';
?>

<div class="container mt-4">
    <h2 class="fw-bold mb-4">👋 مرحباً <?= htmlspecialchars($_SESSION['user_name']) ?></h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body text-center">
                    <h5>🏨 الفنادق</h5>
                    <p class="display-4"><?= $hotelsCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body text-center">
                    <h5>📅 الحجوزات</h5>
                    <p class="display-4"><?= $bookingsCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card bg-warning text-white shadow">
                <div class="card-body text-center">
                    <h5>👥 المستخدمين</h5>
                    <p class="display-4"><?= $usersCount ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>