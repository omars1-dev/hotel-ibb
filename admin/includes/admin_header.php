<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | إبّدار لوحة التحكم' : 'إبّدار | لوحة التحكم' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>admin/index.php">🛠️ لوحة التحكم</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/index.php">الرئيسية</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/hotels.php">الفنادق</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/bookings.php">الحجوزات</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/users.php">المستخدمين</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/logout.php">تسجيل الخروج</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">