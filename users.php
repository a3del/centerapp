<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php require 'config/db.php'; ?>

<?php
$message = "";

// إضافة مستخدم جديد
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $role])) {
        $message = "✅ تم إضافة المستخدم بنجاح!";
    } else {
        $message = "❌ حدث خطأ أثناء الإضافة!";
    }
}

// حذف مستخدم
if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "✅ تم حذف المستخدم بنجاح!";
    } else {
        $message = "❌ حدث خطأ أثناء الحذف!";
    }
}

// جلب جميع المستخدمين
$users = $conn->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<main>
    <h2>👥 إدارة المستخدمين</h2>
    <p class="message"><?php echo $message; ?></p>

    <div class="user-actions">
        <input type="text" id="searchUser" placeholder="🔍 بحث عن مستخدم...">
    </div>

    <form method="POST" class="user-form">
        <h3>➕ إضافة مستخدم جديد</h3>
        <div class="form-group">
            <label>👤 اسم المستخدم</label>
            <input type="text" name="username" required>
        </div>
        
        <div class="form-group">
            <label>🔑 كلمة المرور</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <label>⚡ الدور</label>
            <select name="role" required>
                <option value="admin">مدير</option>
                <option value="employee">موظف</option>
                <option value="teacher">مدرس</option>
            </select>
        </div>

        <button type="submit" name="add_user" class="btn-add">➕ إضافة</button>
    </form>

    <table class="users-table">
        <tr>
            <th>🆔 ID</th>
            <th>👤 اسم المستخدم</th>
            <th>⚡ الدور</th>
            <th>🛠️ العمليات</th>
        </tr>
        <?php foreach ($users as $user) : ?>
            <tr class="user-row">
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo $user['role']; ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-edit">✏️ تعديل</a>
                    <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">🗑️ حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

<script>
document.getElementById("searchUser").addEventListener("input", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll(".user-row");
    
    rows.forEach(row => {
        let username = row.cells[1].textContent.toLowerCase();
        let role = row.cells[2].textContent.toLowerCase();
        row.style.display = (username.includes(filter) || role.includes(filter)) ? "" : "none";
    });
});
</script>

<?php include 'includes/footer.php'; ?>
