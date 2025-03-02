<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php require 'config/db.php'; ?>

<?php
$message = "";

// التحقق من وجود معرف الطالب في الرابط
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ لم يتم تحديد الطالب!");
}

$student_id = $_GET['id'];

// جلب بيانات الطالب الحالية
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    die("❌ الطالب غير موجود!");
}

// تحديث بيانات الطالب
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $student_code = trim($_POST["student_code"]);
    $student_name = trim($_POST["student_name"]);
    $group_name = trim($_POST["group_name"]);
    $level = trim($_POST["level"]);
    $branch_id = $_POST["branch_id"];

    $stmt = $conn->prepare("UPDATE students SET student_code = ?, student_name = ?, group_name = ?, level = ?, branch_id = ? WHERE id = ?");
    if ($stmt->execute([$student_code, $student_name, $group_name, $level, $branch_id, $student_id])) {
        $message = "✅ تم تحديث بيانات الطالب بنجاح!";
        // تحديث البيانات المعروضة بعد الحفظ
        $student = [
            'student_code' => $student_code,
            'student_name' => $student_name,
            'group_name' => $group_name,
            'level' => $level,
            'branch_id' => $branch_id
        ];
    } else {
        $message = "❌ حدث خطأ أثناء التحديث!";
    }
}

// جلب قائمة الفروع
$branches = $conn->query("SELECT * FROM branches")->fetchAll();
?>

<main>
    <h2>✏️ تعديل بيانات الطالب</h2>
    <p class="message"><?php echo $message; ?></p>

    <form method="POST" class="student-form">
        <div class="form-group">
            <label>📌 كود الطالب</label>
            <input type="text" name="student_code" value="<?php echo htmlspecialchars($student['student_code']); ?>" required>
        </div>

        <div class="form-group">
            <label>👤 اسم الطالب</label>
            <input type="text" name="student_name" value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
        </div>

        <div class="form-group">
            <label>📚 المجموعة</label>
            <input type="text" name="group_name" value="<?php echo htmlspecialchars($student['group_name']); ?>" required>
        </div>

        <div class="form-group">
            <label>🎓 المستوى</label>
            <input type="text" name="level" value="<?php echo htmlspecialchars($student['level']); ?>" required>
        </div>

        <div class="form-group">
            <label>🏢 الفرع</label>
            <select name="branch_id" required>
                <?php foreach ($branches as $branch) : ?>
                    <option value="<?php echo $branch['id']; ?>" <?php echo ($student['branch_id'] == $branch['id']) ? 'selected' : ''; ?>>
                        📍 <?php echo htmlspecialchars($branch['branch_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="update" class="btn-update">💾 حفظ التعديلات</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
