<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

// تغيير حالة الحجز
if (isset($_POST['update_status'])) {
    $bid = (int)$_POST['booking_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE bookings SET status=? WHERE id=?");
    $stmt->execute([$status, $bid]);
    header('Location: bookings.php');
    exit;
}

// جلب كل الحجوزات مع اسم المستخدم والفندق
$bookings = $pdo->query("
    SELECT b.*, u.name AS user_name, h.name AS hotel_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN hotels h ON b.hotel_id = h.id
    ORDER BY b.created_at DESC
")->fetchAll();

$pageTitle = 'الحجوزات';
include __DIR__ . '/includes/admin_header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">📅 جميع الحجوزات</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>المستخدم</th>
                    <th>الفندق</th>
                    <th>من</th>
                    <th>إلى</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>تغيير</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= $b['id'] ?></td>
                        <td><?= htmlspecialchars($b['user_name']) ?></td>
                        <td><?= htmlspecialchars($b['hotel_name']) ?></td>
                        <td><?= $b['check_in'] ?></td>
                        <td><?= $b['check_out'] ?></td>
                        <td><?= number_format($b['total_price']) ?> ريال</td>
                        <td>
                            <?php $status = $b['status'] ?? 'pending'; ?>
                            <?php if ($status === 'confirmed'): ?>
                                <span class="badge bg-success">مؤكد</span>
                            <?php elseif ($status === 'cancelled'): ?>
                                <span class="badge bg-danger">ملغي</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">قيد الانتظار</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" class="d-flex">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>قيد الانتظار</option>
                                    <option value="confirmed" <?= $status == 'confirmed' ? 'selected' : '' ?>>مؤكد</option>
                                    <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>ملغي</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary ms-1">تحديث</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>