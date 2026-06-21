<?php
session_start();
require_once '../config/database.php';
auto_login_from_cookie($pdo);

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT b.*, h.name AS hotel_name 
    FROM bookings b 
    JOIN hotels h ON b.hotel_id = h.id 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'حجوزاتي';
include '../includes/header.php';
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container my-5">
    <h2 class="fw-bold mb-4">🗓️ حجوزاتي</h2>
    <?php if (count($bookings) > 0): ?>
        <div class="booking-table table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>الفندق</th>
                        <th>تاريخ الوصول</th>
                        <th>تاريخ المغادرة</th>
                        <th>النزلاء</th>
                        <th>السعر</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b):
                        $status = $b['status'] ?? 'pending'; ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($b['hotel_name']) ?></td>
                            <td><?= htmlspecialchars($b['check_in']) ?></td>
                            <td><?= htmlspecialchars($b['check_out']) ?></td>
                            <td><?= (int)$b['guests'] ?></td>
                            <td><?= number_format($b['total_price']) ?> ريال</td>
                            <td>
                                <?php if ($status === 'confirmed'): ?>
                                    <span class="badge bg-success status-badge">مؤكد</span>
                                <?php elseif ($status === 'cancelled'): ?>
                                    <span class="badge bg-danger status-badge">ملغي</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark status-badge">قيد الانتظار</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">لا توجد حجوزات بعد. <a href="<?= BASE_URL ?>hotels.php" class="fw-bold">تصفح الفنادق الآن</a>.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>