<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php
require 'config/db.php'; // الاتصال بقاعدة البيانات

$message = "";

// إضافة فرع جديد
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $branch_name = trim($_POST["branch_name"]);
    $address = trim($_POST["address"]);

    $stmt = $conn->prepare("INSERT INTO branches (branch_name, address) VALUES (?, ?)");
    if ($stmt->execute([$branch_name, $address])) {
        $message = "✅ تم إضافة الفرع بنجاح!";
    } else {
        $message = "❌ حدث خطأ أثناء الإضافة!";
    }
}

// حذف فرع
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->prepare("DELETE FROM branches WHERE id = ?")->execute([$id]);
    header("Location: branches.php");
}

// جلب جميع الفروع
$stmt = $conn->query("SELECT * FROM branches");
$branches = $stmt->fetchAll();
?>

<main>
    <h2>إدارة الفروع</h2>
    <p class="message"><?php echo $message; ?></p>

    <form method="POST" class="branch-form">
        <div class="form-group">
            <label>🏢 اسم الفرع</label>
            <input type="text" name="branch_name" placeholder="أدخل اسم الفرع" required>
        </div>

        <div class="form-group">
            <label>📍 العنوان</label>
            <input type="text" name="address" placeholder="أدخل عنوان الفرع" required>
        </div>

        <button type="submit" name="add" class="btn-add">➕ إضافة الفرع</button>
    </form>

    <table class="branch-table">
        <tr>
            <th>🏢 اسم الفرع</th>
            <th>📍 العنوان</th>
            <th>⚙️ إجراءات</th>
        </tr>
        <?php foreach ($branches as $branch) : ?>
            <tr>
                <td><?php echo $branch['branch_name']; ?></td>
                <td><?php echo $branch['address']; ?></td>
                <td>
                    <a href="branches.php?delete=<?php echo $branch['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">❌ حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

<?php include 'includes/footer.php'; ?>
