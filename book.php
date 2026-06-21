<?php
session_start();

require_once 'config/database.php';
auto_login_from_cookie($pdo);   

// 1. منع دخول الزوار غير المسجلين
if (!isset($_SESSION['user_id'])) {
   header('Location: ' . BASE_URL . 'user/login.php');
    exit;
}


// 2. التحقق من وجود hotel_id
if (!isset($_GET['hotel_id']) || empty($_GET['hotel_id'])) {
header('Location: ' . BASE_URL . 'hotels.php');    exit;
}

$hotel_id = (int) $_GET['hotel_id'];

// 3. جلب بيانات الفندق
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();

if (!$hotel) {
    die('الفندق غير موجود.');
}

$error   = '';
$success = '';

// 4. معالجة إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in  = $_POST['check_in']  ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $guests    = (int) ($_POST['guests'] ?? 1);

    // التحقق من صحة التواريخ
    if (empty($check_in) || empty($check_out)) {
        $error = 'يرجى تحديد تاريخي الوصول والمغادرة.';
    } elseif ($check_in >= $check_out) {
        $error = 'تاريخ المغادرة يجب أن يكون بعد تاريخ الوصول.';
    } elseif ($guests < 1) {
        $error = 'عدد النزلاء يجب أن يكون 1 على الأقل.';
    } else {
        // حساب عدد الليالي والسعر
        $date1  = new DateTime($check_in);
        $date2  = new DateTime($check_out);
        $nights = $date1->diff($date2)->days;

        if ($nights <= 0) {
            $error = 'فترة الإقامة غير صالحة.';
        } else {
            $total = $nights * $hotel['price_per_night'];

            // إدخال الحجز في قاعدة البيانات
            $insert = $pdo->prepare(
                "INSERT INTO bookings (user_id, hotel_id, check_in, check_out, guests, total_price, status) 
                 VALUES (?, ?, ?, ?, ?, ?, 'pending')"
            );
            $insert->execute([
                $_SESSION['user_id'],
                $hotel_id,
                $check_in,
                $check_out,
                $guests,
                $total
            ]);

            $success = "تم الحجز بنجاح! السعر الإجمالي: " . number_format($total) . " ريال يمني.";
        }
    }
}

$pageTitle = 'حجز ' . htmlspecialchars($hotel['name']);
include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h2 class="mb-4">حجز <?= htmlspecialchars($hotel['name']) ?></h2>
            
            <!-- رسائل الخطأ والنجاح -->
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
<a href="<?= BASE_URL ?>user/dashboard.php" class="btn-solid-green mt-2">الذهاب إلى حجوزاتي</a>            <?php else: ?>
                <!-- نموذج الحجز -->
                <div class="card p-4 shadow-sm">
                    <p class="text-muted">السعر لليلة الواحدة: <strong><?= number_format($hotel['price_per_night']) ?> ريال</strong></p>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">تاريخ الوصول</label>
                            <input type="date" name="check_in" class="form-control" 
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تاريخ المغادرة</label>
                            <input type="date" name="check_out" class="form-control" 
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">عدد النزلاء</label>
                            <input type="number" name="guests" class="form-control" 
                                   min="1" value="1" required>
                        </div>
                        <button type="submit" class="btn-solid-green btn-lg w-100">تأكيد الحجز</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>