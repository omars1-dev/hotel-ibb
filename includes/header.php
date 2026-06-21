<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// محاولة تسجيل الدخول من الكوكيز
require_once __DIR__ . '/../config/database.php';   // تأكد من المسار الصحيح

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | إبّدار' : 'إبّدار | حجز فنادق في إب' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- منع وميض الوضع الخاطئ + استعادة الثيم فوراً -->
<script>
    (function() {
        const savedTheme = localStorage.getItem('ibdar_theme');
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    })();
</script>
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>index.php">
                <?php if (file_exists(__DIR__ . '/../assets/images/logo.png')): ?>
                    <img src="<?= BASE_URL ?>assets/images/logo.png" alt="إبّدار" style="height: 80px;>
    <?php else: ?>
        <span class=" brand-icon">🏨</span>
                <?php endif; ?>
                <span class="brand-text"></span>
            </a>



            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>hotels.php">الفنادق</a></li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>user/dashboard.php">حجوزاتي</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>user/logout.php">تسجيل الخروج</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>user/login.php">دخول</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>user/register.php">حساب جديد</a></li>
                    <?php endif; ?>
                    <!-- زر الوضع الليلي -->
                    <li class="nav-item d-flex align-items-center ms-2">
                        <button id="darkModeToggle" class="btn btn-link nav-link p-0 fs-4" title="تبديل الوضع الليلي" style="font-size: 1.4rem !important;">
                            <i class="bi bi-moon-fill"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- بداية المحتوى -->
    <main>