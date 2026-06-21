</main>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>عن إبّدار</h5>
                <p>منصتكم الأولى لحجز الفنادق في محافظة إب، نقدم لكم تجربة حجز سهلة وآمنة مع أفضل العروض.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h5>روابط سريعة</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= BASE_URL ?>index.php">الرئيسية</a></li>
                    <li><a href="<?= BASE_URL ?>hotels.php">تصفح الفنادق</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?= BASE_URL ?>user/dashboard.php">حجوزاتي</a></li>
                        <li><a href="<?= BASE_URL ?>user/logout.php">تسجيل الخروج</a></li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>user/login.php">تسجيل الدخول</a></li>
                        <li><a href="<?= BASE_URL ?>user/register.php">إنشاء حساب</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5>تواصل معنا</h5>
                <p><a href="mailto:omaralzomor2030@gmail.com"></a>البريد: omaralzomor2030@gmail.com</p>
                <p>الهاتف: 00967 717 266 653</p>
                <p>إب، اليمن</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="mb-0">&copy; <?= date('Y') ?> إبّدار - جميع الحقوق محفوظة</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // =============================================
    // إبّدار - نظام الوضع الليلي (Dark Mode)
    // مضمون العمل على Chrome, Edge, Firefox
    // =============================================
    
    (function() {
        // الحصول على الزر فورًا
        const toggleBtn = document.getElementById('darkModeToggle');
        const html = document.documentElement;
        
        // استعادة الوضع المحفوظ مباشرة (قبل أي عرض)
        const savedTheme = localStorage.getItem('ibdar_theme');
        if (savedTheme === 'dark') {
            html.setAttribute('data-bs-theme', 'dark');
            if (toggleBtn) {
                toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            }
        }
        
        // ربط الحدث بعد اكتمال التحميل
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault(); // منع أي سلوك افتراضي
                
                const current = html.getAttribute('data-bs-theme');
                
                if (current === 'dark') {
                    // العودة للوضع الفاتح
                    html.removeAttribute('data-bs-theme');
                    localStorage.setItem('ibdar_theme', 'light');
                    toggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
                } else {
                    // التحويل للوضع الليلي
                    html.setAttribute('data-bs-theme', 'dark');
                    localStorage.setItem('ibdar_theme', 'dark');
                    toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
                }
            });
        } else {
            console.warn('زر الوضع الليلي غير موجود في الصفحة');
        }
    })();
</script>
</body>

</html>