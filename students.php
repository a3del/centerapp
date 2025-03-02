<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php require 'config/db.php'; ?>

<?php
$message = "";

// إضافة طالب
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $student_code = trim($_POST["student_code"]);
    $student_name = trim($_POST["student_name"]);
    $group_name = trim($_POST["group_name"]); // ✅ تصحيح الفراغ غير الصحيح
    $level = trim($_POST["level"]);
    $branch_id = $_POST["branch_id"];

    // التأكد من أن الفرع موجود في قاعدة البيانات
    $stmt = $conn->prepare("SELECT id FROM branches WHERE id = ?");
    $stmt->execute([$branch_id]);
    if ($stmt->rowCount() > 0) {
        $stmt = $conn->prepare("INSERT INTO students (student_code, student_name, group_name, level, branch_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$student_code, $student_name, $group_name, $level, $branch_id])) {
            $message = "✅ تم إضافة الطالب بنجاح!";
        } else {
            $message = "❌ حدث خطأ أثناء الإضافة!";
        }
    } else {
        $message = "❌ الفرع المحدد غير موجود!";
    }
}

// حذف طالب
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // التأكد من أن الطالب موجود قبل الحذف
    $stmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        $conn->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
        header("Location: students.php");
        exit();
    } else {
        $message = "❌ الطالب غير موجود!";
    }
}

// جلب جميع الطلاب مع الفروع
$stmt = $conn->query("
    SELECT students.*, COALESCE(branches.branch_name, 'غير محدد') AS branch_name 
    FROM students 
    LEFT JOIN branches ON students.branch_id = branches.id
");
$students = $stmt->fetchAll();
?>

<main>
    <h2>إدارة الطلاب</h2>
    <p class="message"><?php echo $message; ?></p>

    <form method="POST" class="student-form">
        <div class="form-group">
            <label>📌 كود الطالب</label>
            <input type="text" name="student_code" placeholder="أدخل كود الطالب" required>
        </div>

        <div class="form-group">
            <label>👤 اسم الطالب</label>
            <input type="text" name="student_name" placeholder="أدخل اسم الطالب" required>
        </div>

        <div class="form-group">
            <label>📚 المجموعة</label>
            <input type="text" name="group_name" placeholder="أدخل اسم المجموعة" required>
        </div>

        <div class="form-group">
            <label>🎓 المستوى</label>
            <input type="text" name="level" placeholder="أدخل المستوى" required>
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

        <button type="submit" name="add" class="btn-add">➕ إضافة الطالب</button>
    </form>

    <table class="student-table">
        <tr>
            <th>كود الطالب</th>
            <th>اسم الطالب</th>
            <th>المجموعة</th>
            <th>المستوى</th>
            <th>الفرع</th>
            <th>إجراءات</th>
        </tr>
        <?php foreach ($students as $student) : ?>
            <tr>
                <td><?php echo htmlspecialchars($student['student_code']); ?></td>
                <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                <td><?php echo htmlspecialchars($student['group_name']); ?></td>
                <td><?php echo htmlspecialchars($student['level']); ?></td>
                <td><?php echo htmlspecialchars($student['branch_name']); ?></td>
                <td>
    <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn-edit">✏️ تعديل</a>
    <a href="students.php?delete=<?php echo $student['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">❌ حذف</a>
</td>

            </tr>
        <?php endforeach; ?>
    </table>
</main>

<?php include 'includes/footer.php'; ?>
