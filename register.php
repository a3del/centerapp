<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=education_center", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];

    // التحقق من وجود المستخدم مسبقًا
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    
    if ($stmt->rowCount() > 0) {
        $message = "⚠️ اسم المستخدم موجود بالفعل!";
    } else {
        // تشفير كلمة المرور
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // إدخال المستخدم الجديد في قاعدة البيانات
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        if ($stmt->execute(['username' => $username, 'password' => $hashedPassword, 'role' => $role])) {
            $message = "✅ تم التسجيل بنجاح! يمكنك تسجيل الدخول الآن.";
        } else {
            $message = "❌ حدث خطأ أثناء التسجيل!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل حساب جديد</title>
    <link rel="stylesheet" href="styles.css"> <!-- استبدل بـ ملف الـ CSS الخاص بك -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>تسجيل حساب جديد</h2>
    <form action="" method="POST">
        <input type="text" name="username" placeholder="اسم المستخدم" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <select name="role">
            <option value="admin">مدير</option>
            <option value="employee">موظف</option>
            <option value="teacher">مدرس</option>
        </select>
        <button type="submit">تسجيل</button>
    </form>
    <p class="message"><?php echo $message; ?></p>
    <p>هل لديك حساب؟ <a href="login.php">تسجيل الدخول</a></p>
</div>

</body>
</html>
