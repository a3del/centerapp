<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php require 'config/db.php'; ?>

<?php
$message = "";

// إضافة رسوم جديدة
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_payment"])) {
    $student_id = $_POST["student_id"];
    $amount = $_POST["amount"];
    $description = $_POST["description"];

    $stmt = $conn->prepare("INSERT INTO student_expenses (student_id, amount, description) VALUES (?, ?, ?)");
    if ($stmt->execute([$student_id, $amount, $description])) {
        $message = "✅ تم تسجيل المدفوعات بنجاح!";
    } else {
        $message = "❌ حدث خطأ أثناء التسجيل!";
    }
}

// جلب جميع الطلاب مع بيانات الفرع
$students = $conn->query("
    SELECT students.*, branches.branch_name 
    FROM students 
    JOIN branches ON students.branch_id = branches.id
")->fetchAll();

// جلب جميع المدفوعات
$payments = $conn->query("
    SELECT student_expenses.*, students.student_name, students.student_code, branches.branch_name 
    FROM student_expenses 
    JOIN students ON student_expenses.student_id = students.id
    JOIN branches ON students.branch_id = branches.id
    ORDER BY student_expenses.id DESC
")->fetchAll();

// حساب إجمالي المصاريف
$total_paid = $conn->query("SELECT SUM(amount) FROM student_expenses")->fetchColumn();
?>

<main>
    <h2>📌 إدارة مصاريف الطلاب</h2>
    <p class="message"><?php echo $message; ?></p>

    <div class="expense-summary">
        <div class="card" style="color:#000;">💰 إجمالي المدفوعات: <?php echo number_format($total_paid, 2); ?> EGP</div>
    </div>

    <form method="POST" class="expense-form">
        <h3>➕ تسجيل دفعة جديدة</h3>

        <div class="form-group">
            <label>🔍 بحث سريع:</label>
            <input type="text" id="searchStudent" placeholder="ابحث بالاسم أو الكود أو الفرع">
        </div>

        <div class="form-group">
            <label>👨‍🎓 الطالب</label>
            <select name="student_id" id="studentSelect" size="6" required>
                <?php foreach ($students as $student) : ?>
                    <option value="<?php echo $student['id']; ?>"
                            data-code="<?php echo $student['student_code']; ?>"
                            data-branch="<?php echo strtolower($student['branch_name']); ?>">
                        <?php echo $student['student_code'] . " - " . $student['student_name'] . " (" . $student['branch_name'] . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>💲 المبلغ</label>
            <input type="number" name="amount" step="0.01" required>
        </div>

        <div class="form-group">
            <label>✍️ الوصف</label>
            <input type="text" name="description" required>
        </div>

        <button type="submit" name="add_payment" class="btn-add">➕ إضافة</button>
    </form>

    <table class="expense-table">
        <tr>
            <th>🎫 كود الطالب</th>
            <th>👨‍🎓 الطالب</th>
            <th>🏫 الفرع</th>
            <th>💲 المبلغ</th>
            <th>✍️ الوصف</th>
            <th>⏰ التاريخ</th>
        </tr>
        <?php foreach ($payments as $payment) : ?>
            <tr>
                <td><?php echo $payment['student_code']; ?></td>
                <td><?php echo $payment['student_name']; ?></td>
                <td><?php echo $payment['branch_name']; ?></td>
                <td><?php echo number_format($payment['amount'], 2); ?> EGP</td>
                <td><?php echo $payment['description']; ?></td>
                <td><?php echo $payment['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

<script>
document.getElementById("searchStudent").addEventListener("input", function() {
    let filter = this.value.toLowerCase();
    let select = document.getElementById("studentSelect");
    let options = select.options;
    
    for (let i = 0; i < options.length; i++) {
        let name = options[i].text.toLowerCase();
        let code = options[i].getAttribute("data-code").toLowerCase();
        let branch = options[i].getAttribute("data-branch").toLowerCase();

        if (name.includes(filter) || code.includes(filter) || branch.includes(filter)) {
            options[i].style.display = "";
        } else {
            options[i].style.display = "none";
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
