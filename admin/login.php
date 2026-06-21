<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// إذا كان المشرف مسجلاً بالفعل، انقله للوحة التحكم
if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: ' . BASE_URL . 'admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'الرجاء إدخال البريد الإلكتروني وكلمة المرور.';
    } else {
        // جلب المستخدم بشرط أن يكون دوره admin
        $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // إنشاء جلسة المشرف
            $_SESSION['user_id']   = $admin['id'];
            $_SESSION['user_name'] = $admin['name'];
            $_SESSION['user_role'] = $admin['role'];

            // إعادة التوجيه للوحة التحكم
            header('Location: ' . BASE_URL . 'admin/index.php');
            exit;
        } else {
            $error = 'بيانات الدخول غير صحيحة أو لا تملك صلاحية المشرف.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول المشرف | إبّدار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-bg: #1a202c;
            --darker-bg: #171923;
            --green-accent: #198754;
        }
        body {
            background: linear-gradient(135deg, #1a202c 0%, #0b4632 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            direction: rtl;
            font-family: 'Tajawal', sans-serif;
        }
        .admin-login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeUp 0.5s ease;
            color: #fff;
        }
        .admin-login-card h2 {
            font-weight: 900;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .admin-login-card .form-label {
            font-weight: 600;
            color: rgba(255,255,255,0.8);
        }
        .admin-login-card .form-control {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            padding: 14px 18px;
            border-radius: 14px;
            font-size: 1rem;
        }
        .admin-login-card .form-control::placeholder {
            color: rgba(255,255,255,0.4);
        }
        .admin-login-card .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 4px rgba(25,135,84,0.3);
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .btn-admin {
            background: #198754;
            color: #fff;
            border: none;
            padding: 14px;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 50px;
            width: 100%;
            margin-top: 1.5rem;
            transition: 0.3s;
        }
        .btn-admin:hover {
            background: #146c43;
            box-shadow: 0 10px 25px rgba(25,135,84,0.5);
        }
        .alert {
            background: rgba(220,53,69,0.2);
            border: 1px solid rgba(220,53,69,0.3);
            color: #ffb3b3;
            border-radius: 14px;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="admin-login-card">
        <h2> دخول المشرف</h2>
        <?php if ($error): ?>
            <div class="alert"><?= $error ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" placeholder="admin@ibdar.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-admin">دخول</button>
        </form>
    </div>
</body>
</html>