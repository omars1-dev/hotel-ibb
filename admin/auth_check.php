<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// محاولة استعادة جلسة المشرف من الكوكيز (إذا أردت تذكره لاحقاً)
auto_login_from_cookie($pdo);

// التحقق من الجلسة والدور
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}