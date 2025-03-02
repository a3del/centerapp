<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<?php
// جلب البيانات من قاعدة البيانات
$students_count = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();
$teachers_count = $conn->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$branches_count = $conn->query("SELECT COUNT(*) FROM branches")->fetchColumn();
$total_revenue = $conn->query("SELECT SUM(amount) FROM transactions WHERE type = 'income'")->fetchColumn();
?>

<h2>لوحة التحكم</h2>

<div class="dashboard-cards">
    <div class="card">
        <h3>عدد الطلاب</h3>
        <p><?php echo $students_count; ?></p>
    </div>
    <div class="card">
        <h3>عدد المدرسين</h3>
        <p><?php echo $teachers_count; ?></p>
    </div>
    <div class="card">
        <h3>عدد الفروع</h3>
        <p><?php echo $branches_count; ?></p>
    </div>
    <div class="card">
        <h3>إجمالي الإيرادات</h3>
        <p><?php echo number_format($total_revenue, 2); ?> EGP</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
