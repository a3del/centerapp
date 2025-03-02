<?php
require 'config/db.php'; // الاتصال بقاعدة البيانات

// جلب بيانات المصاريف مع بيانات الطالب
$query = "SELECT s.student_name, sf.* FROM student_fees sf JOIN students s ON sf.student_id = s.id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المصاريف</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>إدارة مصاريف الطلاب</h2>
    <table border="1">
        <tr>
            <th>اسم الطالب</th>
            <th>نوع المصروف</th>
            <th>إجمالي المصروف</th>
            <th>المبلغ المدفوع</th>
            <th>المبلغ المتبقي</th>
            <th>تاريخ الاستحقاق</th>
            <th>الإجراءات</th>
        </tr>
        <?php foreach ($fees as $fee) : ?>
            <tr>
                <td><?= htmlspecialchars($fee['student_name']) ?></td>
                <td><?= htmlspecialchars($fee['fee_type']) ?></td>
                <td><?= htmlspecialchars($fee['total_amount']) ?> EGP</td>
                <td><?= htmlspecialchars($fee['paid_amount']) ?> EGP</td>
                <td><?= ($fee['total_amount'] - $fee['paid_amount']) ?> EGP</td>
                <td><?= htmlspecialchars($fee['due_date']) ?></td>
                <td>
                    <a href="edit_fee.php?id=<?= $fee['id'] ?>">تعديل</a> |
                    <a href="delete_fee.php?id=<?= $fee['id'] ?>" onclick="return confirm('هل أنت متأكد؟')">حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="add_fee.php">إضافة مصروف جديد</a>
</body>
</html>
