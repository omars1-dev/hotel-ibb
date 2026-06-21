<?php
// ملف: config/database.php
// هذا الملف يختار تلقائياً ملف الإعدادات المناسب للبيئة

// إذا وجد ملف الإعدادات الخاص بالاستضافة، استخدمه
if (file_exists(__DIR__ . '/database_live.php')) {
    require_once __DIR__ . '/database_live.php';
} else {
    // وإلا استخدم الإعدادات المحلية الافتراضية
    $host = 'localhost';
    $dbname = 'hotel_ibb';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
    }
}

// تعريف المسار الأساسي (يُحدد تلقائياً)
define('BASE_URL', '/hotel-ibb/');
// الدوال المساعدة (generate_token و auto_login_from_cookie) تبقى هنا
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

function auto_login_from_cookie($pdo) {
    if (isset($_SESSION['user_id']) || !isset($_COOKIE['remember_user']) || !isset($_COOKIE['remember_token'])) {
        return false;
    }
    $user_id = (int) $_COOKIE['remember_user'];
    $token   = $_COOKIE['remember_token'];
    $stmt = $pdo->prepare("SELECT id, name, remember_token FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user && $user['remember_token'] && password_verify($token, $user['remember_token'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        return true;
    }
    setcookie('remember_user', '', time() - 3600, '/');
    setcookie('remember_token', '', time() - 3600, '/');
    return false;
}