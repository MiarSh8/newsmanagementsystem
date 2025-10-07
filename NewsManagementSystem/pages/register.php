<?php
session_start();
include "../includes/connection.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    if (!empty($name) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            $check_sql = "SELECT id FROM users WHERE email = ?";
            $check_stmt = $connection->prepare($check_sql);
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("sss", $name, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    $success = "تم إنشاء الحساب بنجاح. يمكنك الآن تسجيل الدخول.";
                } else {
                    $error = "حدث خطأ أثناء إنشاء الحساب. يرجى المحاولة مرة أخرى.";
                }
            } else {
                $error = "البريد الإلكتروني مسجل مسبقاً";
            }
        } else {
            $error = "كلمتا المرور غير متطابقتين";
        }
    } else {
        $error = "يرجى ملء جميع الحقول";
    }
}
?>
//--------------------------------------------------


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب - نظام إدارة الأخبار</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>إنشاء حساب جديد</h1>
                <p>املأ البيانات التالية لإنشاء حساب جديد</p>
            </div>

            <?php if (isset($error)): ?>
                <div style="background: #ffeaea; color: #d63031; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div style="background: #eaffea; color: #00b894; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">الاسم الكامل</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">تأكيد كلمة المرور</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <button type="submit" name="register" class="btn" style="width: 100%;">إنشاء الحساب</button>
            </form>
            
            <div style="text-align: center; margin-top: 20px;">
            <p>لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
            </div>
        </div>
    </div>
</body>
</html>