<?php
session_start();
require_once '../config/database.php';

// حذف التوكن من قاعدة البيانات إذا كان المستخدم مسجلاً
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

// تدمير الجلسة
session_destroy();

// حذف الكوكيز من المتصفح
setcookie('remember_user', '', time() - 3600, '/');
setcookie('remember_token', '', time() - 3600, '/');

header('Location: ' . BASE_URL . 'index.php');
exit;