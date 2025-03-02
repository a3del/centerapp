<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php
require 'config/db.php'; // الاتصال بقاعدة البيانات

$message = "";

// إضافة مدرس جديد
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $teacher_name = trim($_POST["teacher_name"]);
    $phone = trim($_POST["phone"]);
    $group_name = $_POST["group_name"];
    $branch_id = $_POST["branch_id"];

    $stmt = $conn->prepare("INSERT INTO teachers (teacher_name, phone, group_name, branch_id) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$teacher_name, $phone, $group_name, $branch_id])) {
        $message = "✅ تم إضافة المدرس بنجاح!";
    } else {
        $message = "❌ حدث خطأ أثناء الإضافة!";
    }
}

// حذف مدرس
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->prepare("DELETE FROM teachers WHERE id = ?")->execute([$id]);
    header("Location: teachers.php");
}

// جلب جميع المدرسين
$stmt = $conn->query("SELECT teachers.*, branches.branch_name FROM teachers JOIN branches ON teachers.branch_id = branches.id");
$teachers = $stmt->fetchAll();
?>

<main>
    <h2>إدارة المدرسين</h2>
    <p class="message"><?php echo $message; ?></p>

    <form method="POST" class="teacher-form">
        <div class="form-group">
            <label>👤 اسم المدرس</label>
            <input type="text" name="teacher_name" placeholder="أدخل اسم المدرس" required>
        </div>

        <div class="form-group">
            <label>📞 رقم الهاتف</label>
            <input type="text" name="phone" placeholder="أدخل رقم الهاتف" required>
        </div>

        <div class="form-group">
            <label>📚 المجموعة</label>
            <input type="text" name="group_name" placeholder="أدخل اسم المجموعة" required>
        </div>

        <div class="form-group">
            <label>🏢 الفرع</label>
            <select name="branch_id" required>
                <?php
                $branches = $conn->query("SELECT * FROM branches")->fetchAll();
                foreach ($branches as $branch) {
                    echo "<option value='{$branch['id']}'>📍 {$branch['branch_name']}</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" name="add" class="btn-add">➕ إضافة المدرس</button>
    </form>

    <table class="teacher-table">
        <tr>
            <th>اسم المدرس</th>
            <th>رقم الهاتف</th>
            <th>المجموعة</th>
            <th>الفرع</th>
            <th>إجراءات</th>
        </tr>
        <?php foreach ($teachers as $teacher) : ?>
            <tr>
                <td><?php echo $teacher['teacher_name']; ?></td>
                <td><?php echo $teacher['phone']; ?></td>
                <td><?php echo $teacher['group_name']; ?></td>
                <td><?php echo $teacher['branch_name']; ?></td>
                <td>
                    <a href="teachers.php?delete=<?php echo $teacher['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">❌ حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

<?php include 'includes/footer.php'; ?>
