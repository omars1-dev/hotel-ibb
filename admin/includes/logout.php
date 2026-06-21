<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// حذف التوكن إذا كنت تريد تذكره (اختياري)
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

session_destroy();
setcookie('remember_user', '', time() - 3600, '/');
setcookie('remember_token', '', time() - 3600, '/');

header('Location: ' . BASE_URL . 'admin/login.php');
exit;