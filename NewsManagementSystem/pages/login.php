<?php
session_start();
include "../includes/connection.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    
    if (!empty($email) && !empty($password)) {
        $sql = "SELECT id, name, password FROM users WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
     if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
            
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
                
                header("Location: dashboard.php");
                exit();
            } else {
        $error = "كلمة المرور غير صحيحة";
            }
        } else {
        $error = "البريد الإلكتروني غير مسجل";
        }
    } else {
        $error = "يرجى ملء جميع الحقول";
    }
}

?>
//------------------------------------------------------

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة الأخبار</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>تسجيل الدخول</h1>
                <p>أدخل بياناتك للوصول إلى النظام</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div style="background: #ffeaea; color: #d63031; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" name="login" class="btn" style="width: 100%;">تسجيل الدخول</button>
            </form>
            
            <div style="text-align: center; margin-top: 20px;">
            <p>ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
            </div>
        </div>
    </div>
</body>
</html> 