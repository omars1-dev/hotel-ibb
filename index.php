<?php
session_start();
require_once 'config/database.php';
$pageTitle = 'الرئيسية';

// جلب 3 فنادق عشوائية
$stmt = $pdo->query("SELECT * FROM hotels ORDER BY RAND() LIMIT 3");
$hotels = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- قسم الترحيب -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title">مرحباً بكم في إبّدار</h1>
        <p class="hero-subtitle">منصتكم الأولى لحجز الفنادق في محافظة إب – اليمن</p>
    </div>
</section>

<!-- الفنادق المميزة -->
<div class="container">
    <div class="row">
        <?php if (count($hotels) > 0): ?>
            <?php foreach ($hotels as $hotel): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="hotel-card">
                        <img src="assets/images/<?= htmlspecialchars($hotel['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($hotel['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($hotel['name']) ?></h5>
                            <p class="card-text"><?= mb_substr(htmlspecialchars($hotel['description']), 0, 80) ?>...</p>
                            <div class="hotel-meta">
                                <span class="hotel-price"><?= number_format($hotel['price_per_night']) ?> ريال/ليلة</span>
                                <a href="hotel_details.php?id=<?= $hotel['id'] ?>" class="btn-outline-green">تفاصيل الفندق</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">لا توجد فنادق متاحة حالياً.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>