<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<?php
// بدء الجلسة فقط إذا لم تكن نشطة
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'config/db.php'; // تأكد أن db.php يرجع كائن PDO

// جلب الأدوار من قاعدة البيانات
$query = "SELECT * FROM role";
$result = mysqli_query($conn, $query);

// جلب الصلاحيات المتاحة
$permissionsQuery = "SELECT * FROM permissions";
$permissionsResult = mysqli_query($conn, $permissionsQuery);
$permissions = [];
while ($row = mysqli_fetch_assoc($permissionsResult)) {
    $permissions[$row['id']] = $row['name'];
}

// معالجة إضافة أو تحديث دور
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $roleName = $_POST['role_name'];
    $rolePermissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    
    if (!empty($_POST['role_id'])) {
        // تحديث دور موجود
        $roleId = $_POST['role_id'];
        $updateQuery = "UPDATE roles SET name = '$roleName' WHERE id = $roleId";
        mysqli_query($conn, $updateQuery);
        
        // تحديث الصلاحيات
        mysqli_query($conn, "DELETE FROM role_permissions WHERE role_id = $roleId");
        foreach ($rolePermissions as $permId) {
            mysqli_query($conn, "INSERT INTO role_permissions (role_id, permission_id) VALUES ($roleId, $permId)");
        }
    } else {
        // إضافة دور جديد
        $insertQuery = "INSERT INTO roles (name) VALUES ('$roleName')";
        mysqli_query($conn, $insertQuery);
        $roleId = mysqli_insert_id($conn);
        
        foreach ($rolePermissions as $permId) {
            mysqli_query($conn, "INSERT INTO role_permissions (role_id, permission_id) VALUES ($roleId, $permId)");
        }
    }
    header("Location: roles.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأدوار</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>إدارة الأدوار والصلاحيات</h2>
    <form method="POST">
        <input type="hidden" name="role_id" id="role_id">
        <label>اسم الدور:</label>
        <input type="text" name="role_name" id="role_name" required>
        <label>الصلاحيات:</label>
        <?php foreach ($permissions as $id => $name): ?>
            <div>
                <input type="checkbox" name="permissions[]" value="<?= $id; ?>"> <?= $name; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit">حفظ</button>
    </form>
    
    <h3>الأدوار الحالية</h3>
    <table>
        <tr>
            <th>الرقم</th>
            <th>الدور</th>
            <th>الصلاحيات</th>
            <th>إجراءات</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['name']; ?></td>
                <td>
                    <?php
                    $roleId = $row['id'];
                    $rolePermQuery = "SELECT permissions.name FROM role_permissions JOIN permissions ON role_permissions.permission_id = permissions.id WHERE role_permissions.role_id = $roleId";
                    $rolePermResult = mysqli_query($conn, $rolePermQuery);
                    while ($permRow = mysqli_fetch_assoc($rolePermResult)) {
                        echo $permRow['name'] . ', ';
                    }
                    ?>
                </td>
                <td>
                    <a href="edit_role.php?id=<?= $row['id']; ?>">تعديل</a>
                    <a href="delete_role.php?id=<?= $row['id']; ?>" onclick="return confirm('هل أنت متأكد؟');">حذف</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
<?php include 'includes/footer.php'; ?>