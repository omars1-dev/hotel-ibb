<?php
session_start();
require_once 'config/database.php';
$pageTitle = 'جميع الفنادق';

// استقبال قيم البحث من الرابط
$search = $_GET['search'] ?? '';
$city   = $_GET['city']   ?? '';

// بناء الاستعلام الأساسي
$sql  = "SELECT * FROM hotels WHERE 1=1";
$params = [];

// إضافة شرط البحث بالاسم إذا وُجد
if (!empty($search)) {
    $sql .= " AND name LIKE :search";
    $params['search'] = "%$search%";
}

// إضافة شرط التصفية بالمدينة إذا وُجد
if (!empty($city)) {
    $sql .= " AND city LIKE :city";
    $params['city'] = "%$city%";
}

// تنفيذ الاستعلام
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$hotels = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- قسم عنوان الصفحة -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title">استكشف فنادق إب</h1>
        <p class="hero-subtitle">اعثر على مكان إقامتك المثالي في محافظة إب</p>
    </div>
</section>

<!-- نموذج البحث -->
<div class="container mb-5">
    <form method="get" class="search-form">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">اسم الفندق</label>
                <input type="text" name="search" class="form-control" placeholder="مثلاً: فندق السحاب" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">المدينة</label>
                <select name="city" class="form-select">
                    <option value="">كل المدن</option>
                    <option value="إب" <?= $city == 'إب' ? 'selected' : '' ?>>إب</option>
                    <!-- يمكن إضافة مدن أخرى هنا -->
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn-solid-green w-100">بحث</button>
            </div>
        </div>
    </form>
</div>

<!-- عرض الفنادق -->
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
                <div class="alert alert-info text-center">لا توجد فنادق مطابقة لبحثك. جرب تغيير المعايير.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>