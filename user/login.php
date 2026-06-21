<?php
session_start();
require_once '../config/database.php';
auto_login_from_cookie($pdo);

if (isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . 'index.php'); exit; }


$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($email) || empty($password)) {
        $error = 'الرجاء إدخال البريد الإلكتروني وكلمة المرور.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'البريد الإلكتروني غير صالح.';
    } else {
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
               if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // --- تذكر تلقائي (دائم) ---
            $token = generate_token();
            $hashed_token = password_hash($token, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmt->execute([$hashed_token, $user['id']]);

            // كوكيز صالح لمدة 30 يومًا
            setcookie('remember_user', $user['id'], time() + 60*60*24*30, '/');
            setcookie('remember_token', $token, time() + 60*60*24*30, '/');
            // --------------------------

            header('Location: ' . BASE_URL . 'index.php');
            exit;
        
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | إبّدار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-card" style="margin: auto;">
        <h2> أهلًا بعودتك</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-auth">دخول</button>
        </form>
        <div class="auth-links">
            ليس لديك حساب؟ <a href="<?= BASE_URL ?>user/register.php">إنشاء حساب جديد</a>

        </div>
    </div>
</body>
</html>