<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php require 'config/db.php'; ?>

<?php
$message = "";

// إضافة معاملة جديدة
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $branch_id = $_POST["branch_id"];
    $amount = $_POST["amount"];
    $type = $_POST["type"];
    $description = $_POST["description"];

    $stmt = $conn->prepare("INSERT INTO transactions (branch_id, amount, type, description) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$branch_id, $amount, $type, $description])) {
        $message = "✅ تمت إضافة المعاملة بنجاح!";
    } else {
        $message = "❌ حدث خطأ أثناء الإضافة!";
    }
}

// جلب جميع الفروع
$branches = $conn->query("SELECT * FROM branches")->fetchAll();

// جلب جميع المعاملات المالية
$transactions = $conn->query("
    SELECT transactions.*, branches.branch_name 
    FROM transactions 
    JOIN branches ON transactions.branch_id = branches.id
    ORDER BY transactions.id DESC
")->fetchAll();

// حساب إجمالي الدخل والمصروفات
$total_income = $conn->query("SELECT SUM(amount) FROM transactions WHERE type = 'income'")->fetchColumn();
$total_expense = $conn->query("SELECT SUM(amount) FROM transactions WHERE type = 'expense'")->fetchColumn();
$total_profit = $total_income - $total_expense;
?>

<main>
    <h2>إدارة الحسابات المالية</h2>
    <p class="message"><?php echo $message; ?></p>

    <div class="finance-summary">
        <div class="card income">💰 إجمالي الإيرادات: <?php echo number_format($total_income, 2); ?> EGP</div>
        <div class="card expense">📉 إجمالي المصروفات: <?php echo number_format($total_expense, 2); ?> EGP</div>
        <div class="card profit">📊 إجمالي الأرباح: <?php echo number_format($total_profit, 2); ?> EGP</div>
    </div>

    <form method="POST" class="finance-form">
        <h3>➕ إضافة معاملة مالية</h3>
        <div class="form-group">
            <label>🏢 الفرع</label>
            <select name="branch_id" required>
                <?php foreach ($branches as $branch) : ?>
                    <option value="<?php echo $branch['id']; ?>"><?php echo $branch['branch_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>💲 المبلغ</label>
            <input type="number" name="amount" step="0.01" required>
        </div>

        <div class="form-group">
            <label>📌 النوع</label>
            <select name="type" required>
                <option value="income">إيراد</option>
                <option value="expense">مصروف</option>
            </select>
        </div>

        <div class="form-group">
            <label>✍️ الوصف</label>
            <input type="text" name="description" required>
        </div>

        <button type="submit" name="add" class="btn-add">➕ إضافة</button>
    </form>

    <table class="finance-table">
        <tr>
            <th>🏢 الفرع</th>
            <th>💲 المبلغ</th>
            <th>📌 النوع</th>
            <th>✍️ الوصف</th>
            <th>⏰ التاريخ</th>
        </tr>
        <?php foreach ($transactions as $transaction) : ?>
            <tr class="<?php echo ($transaction['type'] == 'income') ? 'income-row' : 'expense-row'; ?>">
                <td><?php echo $transaction['branch_name']; ?></td>
                <td><?php echo number_format($transaction['amount'], 2); ?> EGP</td>
                <td><?php echo ($transaction['type'] == 'income') ? 'إيراد' : 'مصروف'; ?></td>
                <td><?php echo $transaction['description']; ?></td>
                <td><?php echo $transaction['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

<?php include 'includes/footer.php'; ?>
