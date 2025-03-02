<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php require 'config/db.php'; ?>

<?php
$message = "";
$user = null;

// التحقق من وجود معرف المستخدم في الرابط
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // جلب بيانات المستخدم المحدد
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<p>❌ المستخدم غير موجود!</p>";
        exit;
    }
}

// تحديث بيانات المستخدم
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_user"])) {
    $username = $_POST["username"];
    $role = $_POST["role"];
    
    // تحديث كلمة المرور إذا تم إدخالها
    if (!empty($_POST["password"])) {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
        $success = $stmt->execute([$username, $password, $role, $user_id]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $success = $stmt->execute([$username, $role, $user_id]);
    }

    if ($success) {
        $message = "✅ تم تحديث بيانات المستخدم بنجاح!";
        // تحديث البيانات المعروضة
        $user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch();
    } else {
        $message = "❌ حدث خطأ أثناء التحديث!";
    }
}
?>

<main>
    <h2>✏️ تعديل بيانات المستخدم</h2>
    <p class="message"><?php echo $message; ?></p>

    <form method="POST" class="edit-user-form">
        <div class="form-group">
            <label>👤 اسم المستخدم</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>

        <div class="form-group">
            <label>🔑 كلمة المرور الجديدة (اتركها فارغة إذا لم ترغب بالتغيير)</label>
            <input type="password" name="password">
        </div>

        <div class="form-group">
            <label>⚡ الصلاحية</label>
            <select name="role" required>
                <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>مدير</option>
                <option value="employee" <?php if ($user['role'] == 'employee') echo 'selected'; ?>>موظف</option>
                <option value="teacher" <?php if ($user['role'] == 'teacher') echo 'selected'; ?>>مدرس</option>
            </select>
        </div>

        <button type="submit" name="update_user" class="btn-update">💾 تحديث البيانات</button>
        <a href="users.php" class="btn-back">🔙 العودة</a>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
