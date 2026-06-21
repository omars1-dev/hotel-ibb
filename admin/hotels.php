<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$message = '';
$upload_error = '';

// مسار مجلد رفع الصور (بالنسبة لجذر المشروع)
$upload_dir = __DIR__ . '/../assets/uploads/';

// ------ إضافة فندق ------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name    = trim($_POST['name'] ?? '');
    $city    = trim($_POST['city'] ?? 'إب');
    $address = trim($_POST['address'] ?? '');
    $desc    = trim($_POST['description'] ?? '');
    $stars   = (int)($_POST['stars'] ?? 3);
    $price   = (float)($_POST['price'] ?? 0);
    $rooms   = (int)($_POST['rooms'] ?? 1);
    $image   = 'hotel_default.jpg'; // صورة افتراضية

    // --- معالجة رفع الصورة ---
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $max_size  = 2 * 1024 * 1024; // 2MB

        if (!in_array($file_type, $allowed_types)) {
            $upload_error = 'نوع الملف غير مسموح به. يسمح فقط JPG, PNG, WebP.';
        } elseif ($file_size > $max_size) {
            $upload_error = 'حجم الصورة كبير جدًا. الحد الأقصى 2 ميجابايت.';
        } else {
            // إنشاء اسم فريد للصورة
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid('hotel_') . '.' . $ext;
            $destination = $upload_dir . $new_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image = $new_name;
            } else {
                $upload_error = 'فشل رفع الصورة. تأكد من صلاحيات المجلد.';
            }
        }
    }
    // ------------------------

    if (empty($upload_error) && !empty($name) && $price > 0) {
        $stmt = $pdo->prepare("INSERT INTO hotels (name, city, address, description, stars, price_per_night, rooms_available, image) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$name, $city, $address, $desc, $stars, $price, $rooms, $image]);
        $message = 'تمت إضافة الفندق بنجاح.';
    } elseif (empty($upload_error)) {
        $upload_error = 'الرجاء تعبئة الاسم والسعر على الأقل.';
    }
}

// ------ حذف فندق ------
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // جلب اسم الصورة لحذفها من السيرفر إن لم تكن الافتراضية
    $stmt = $pdo->prepare("SELECT image FROM hotels WHERE id = ?");
    $stmt->execute([$id]);
    $hotel = $stmt->fetch();
    if ($hotel && $hotel['image'] !== 'hotel_default.jpg' && file_exists($upload_dir . $hotel['image'])) {
        unlink($upload_dir . $hotel['image']);
    }

    $stmt = $pdo->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . BASE_URL . 'admin/hotels.php');
    exit;
}

// ------ تعديل فندق (عرض النموذج) ------
$editHotel = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
    $stmt->execute([$id]);
    $editHotel = $stmt->fetch();
}

// ------ حفظ التعديل ------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id      = (int)$_POST['id'];
    $name    = trim($_POST['name'] ?? '');
    $city    = trim($_POST['city'] ?? 'إب');
    $address = trim($_POST['address'] ?? '');
    $desc    = trim($_POST['description'] ?? '');
    $stars   = (int)($_POST['stars'] ?? 3);
    $price   = (float)($_POST['price'] ?? 0);
    $rooms   = (int)($_POST['rooms'] ?? 1);

    // جلب الصورة الحالية
    $stmt = $pdo->prepare("SELECT image FROM hotels WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();
    $image = $current['image'] ?? 'hotel_default.jpg';

    // --- معالجة رفع صورة جديدة ---
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $max_size  = 2 * 1024 * 1024;

        if (!in_array($file_type, $allowed_types)) {
            $upload_error = 'نوع الملف غير مسموح به. يسمح فقط JPG, PNG, WebP.';
        } elseif ($file_size > $max_size) {
            $upload_error = 'حجم الصورة كبير جدًا. الحد الأقصى 2 ميجابايت.';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid('hotel_') . '.' . $ext;
            $destination = $upload_dir . $new_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                // حذف الصورة القديمة إن لم تكن الافتراضية
                if ($image !== 'hotel_default.jpg' && file_exists($upload_dir . $image)) {
                    unlink($upload_dir . $image);
                }
                $image = $new_name;
            } else {
                $upload_error = 'فشل رفع الصورة.';
            }
        }
    }
    // --------------------------

    if (empty($upload_error)) {
        $stmt = $pdo->prepare("UPDATE hotels SET name=?, city=?, address=?, description=?, stars=?, price_per_night=?, rooms_available=?, image=? WHERE id=?");
        $stmt->execute([$name, $city, $address, $desc, $stars, $price, $rooms, $image, $id]);
        $message = 'تم تحديث الفندق بنجاح.';
        $editHotel = null;
    }
}

// جلب جميع الفنادق
$hotels = $pdo->query("SELECT * FROM hotels ORDER BY id DESC")->fetchAll();

$pageTitle = 'إدارة الفنادق';
include __DIR__ . '/includes/admin_header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">🏨 إدارة الفنادق</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($upload_error): ?>
        <div class="alert alert-danger"><?= $upload_error ?></div>
    <?php endif; ?>

    <!-- نموذج الإضافة / التعديل -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <?= $editHotel ? 'تعديل فندق' : 'إضافة فندق جديد' ?>
        </div>
        <div class="card-body">
            <!-- لاحظ إضافة enctype="multipart/form-data" لدعم رفع الملفات -->
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="<?= $editHotel ? 'update' : 'add' ?>" value="1">
                <?php if ($editHotel): ?>
                    <input type="hidden" name="id" value="<?= $editHotel['id'] ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>اسم الفندق</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($editHotel['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>المدينة</label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($editHotel['city'] ?? 'إب') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>العنوان</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($editHotel['address'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>النجوم</label>
                        <select name="stars" class="form-select">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= ($editHotel['stars'] ?? 3) == $i ? 'selected' : '' ?>><?= $i ?> نجوم</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>السعر لليلة (ريال)</label>
                        <input type="number" name="price" class="form-control" value="<?= $editHotel['price_per_night'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>عدد الغرف</label>
                        <input type="number" name="rooms" class="form-control" value="<?= $editHotel['rooms_available'] ?? 1 ?>" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label>الوصف</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($editHotel['description'] ?? '') ?></textarea>
                    </div>
                    <!-- حقل رفع الصورة -->
                    <div class="col-md-6 mb-3">
                        <label>صورة الفندق</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <?php if ($editHotel && !empty($editHotel['image'])): ?>
                            <div class="mt-2">
                                <img src="<?= BASE_URL ?>assets/uploads/<?= $editHotel['image'] ?>" width="100" style="border-radius:10px;">
                                <p class="text-muted small">الصورة الحالية. اختر ملفًا لتغييرها.</p>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">JPG, PNG, WebP - الحد الأقصى 2MB</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success"><?= $editHotel ? 'حفظ التعديلات' : 'إضافة' ?></button>
                <?php if ($editHotel): ?>
                    <a href="<?= BASE_URL ?>admin/hotels.php" class="btn btn-secondary">إلغاء</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- جدول الفنادق -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>صورة</th>
                    <th>الاسم</th>
                    <th>المدينة</th>
                    <th>السعر</th>
                    <th>الغرف</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hotels as $h): ?>
                    <tr>
                        <td><?= $h['id'] ?></td>
                        <td>
                            <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($h['image']) ?>" width="60" height="60" style="object-fit:cover; border-radius:8px;" onerror="this.src='<?= BASE_URL ?>assets/images/hotel_default.jpg'">
                        </td>
                        <td><?= htmlspecialchars($h['name']) ?></td>
                        <td><?= htmlspecialchars($h['city']) ?></td>
                        <td><?= number_format($h['price_per_night']) ?></td>
                        <td><?= $h['rooms_available'] ?></td>
                        <td>
                            <a href="?edit=<?= $h['id'] ?>" class="btn btn-sm btn-warning">تعديل</a>
                            <a href="?delete=<?= $h['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('متأكد من الحذف؟')">حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>