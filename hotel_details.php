<?php
session_start();
require_once 'config/database.php';

// 1. التحقق من وجود معرف الفندق في الرابط
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: hotels.php');
    exit;
}

$id = (int) $_GET['id'];

// 2. جلب بيانات الفندق من قاعدة البيانات
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$id]);
$hotel = $stmt->fetch();

// 3. إذا لم يوجد الفندق، نعرض صفحة خطأ
if (!$hotel) {
    $pageTitle = 'فندق غير موجود';
    include 'includes/header.php';
    echo '<div class="container text-center my-5">
    <h2>عذراً، الفندق غير موجود</h2><a href="<?= BASE_URL ?>hotels.php" class="btn-solid-green mt-3">تصفح الفنادق</a></div>';
    include 'includes/footer.php';
    exit;
}

// 4. تحديد عنوان الصفحة باسم الفندق
$pageTitle = htmlspecialchars($hotel['name']);
include 'includes/header.php';
?>

<!-- قسم تفاصيل الفندق -->
<div class="container my-5">
    <div class="row g-5">
        <!-- عمود الصورة -->
        <div class="col-lg-6">
            <img src="assets/images/<?= htmlspecialchars($hotel['image']) ?>" 
                 class="detail-img w-100" 
                 alt="<?= htmlspecialchars($hotel['name']) ?>">
        </div>
        
        <!-- عمود المعلومات -->
        <div class="col-lg-6 detail-info">
            <h2><?= htmlspecialchars($hotel['name']) ?></h2>
            
            <p class="text-muted mb-3">
                📍 <?= htmlspecialchars($hotel['city']) ?> – <?= htmlspecialchars($hotel['address']) ?>
            </p>
            
            <!-- النجوم -->
            <div class="stars mb-3">
                <?php for ($i = 0; $i < $hotel['stars']; $i++): ?>⭐<?php endfor; ?>
                <span class="ms-2"><?= $hotel['stars'] ?> نجوم</span>
            </div>
            
            <!-- الوصف الكامل -->
            <p class="mb-4"><?= nl2br(htmlspecialchars($hotel['description'])) ?></p>
            
            <!-- السعر والغرف -->
            <div class="d-flex align-items-center gap-4 mb-4">
                <div class="detail-price">
                    <?= number_format($hotel['price_per_night']) ?> ريال <small class="text-muted">/ ليلة</small>
                </div>
                <div>
                    🛏️ <?= $hotel['rooms_available'] ?> غرفة متاحة
                </div>
            </div>
            
            <!-- زر الحجز -->
            <?php if (isset($_SESSION['user_id'])): ?>
<a href="<?= BASE_URL ?>book.php?hotel_id=<?= $hotel['id'] ?>">احجز الآن</a>            <?php else: ?>
                <div class="alert alert-warning">
                    <a href="user/login.php" class="fw-bold">سجل الدخول</a> لتتمكن من حجز هذا الفندق.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>