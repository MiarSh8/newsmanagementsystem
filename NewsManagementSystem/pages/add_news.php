<?php
session_start();
include "../includes/connection.php";
include "../includes/functions.php";

// التحقق من تسجيل الدخول
checkLogin();

// الحصول على الفئات
$categories = getCategories($connection);

// معالجة إضافة الخبر
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_news"])) {
    $title = trim($_POST["title"]);
    $category_id = $_POST["category_id"];
    $content = trim($_POST["content"]);
    $user_id = $_SESSION['user_id'];
    
    // معالجة صورة الخبر
    $image_name = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowed_types = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
        $file_name = $_FILES["image"]["name"];
        $file_type = $_FILES["image"]["type"];
        $file_size = $_FILES["image"]["size"];
        
        // التحقق من امتداد الملف
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed_types)) {
            $error = "يرجى اختيار صورة بصيغة JPG, JPEG, أو PNG";
        }
        
        // التحقق من حجم الملف (5MB كحد أقصى)
        $max_size = 5 * 1024 * 1024;
        if ($file_size > $max_size) {
            $error = "حجم الصورة كبير جداً. الحد الأقصى هو 5MB";
        }
        
        if (empty($error)) {
            // إنشاء اسم فريد للصورة
            $image_name = "news_" . time() . "_" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES["image"]["tmp_name"], "../assets/images/" . $image_name);
        }
    }
    
    if (empty($error)) {
        // إضافة الخبر إلى قاعدة البيانات
        $sql = "INSERT INTO news (title, category_id, content, image, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sissi", $title, $category_id, $content, $image_name, $user_id);
        
        if ($stmt->execute()) {
            $success = "تم إضافة الخبر بنجاح";
            // إعادة تعيين الحقول
            $title = $content = "";
        } else {
            $error = "حدث خطأ أثناء إضافة الخبر";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة خبر - نظام إدارة الأخبار</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="header-content">
        <div class="logo">نظام إدارة الأخبار</div>
        <div class="user-info">
            <span><i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
        </div>
    </div>
</header>

    <div class="container">
        <div class="main-wrapper">
            <aside class="sidebar">
                <ul class="sidebar-menu">
                    <li><a href="dashboard.php">لوحة التحكم</a></li>
                    <li><a href="add_category.php">إضافة فئة</a></li>
                    <li><a href="view_categories.php">عرض الفئات</a></li>
                    <li><a href="add_news.php" class="active">إضافة خبر</a></li>
                    <li><a href="view_news.php">عرض جميع الأخبار</a></li>
                    <li><a href="deleted_news.php">الأخبار المحذوفة</a></li>
                </ul>
            </aside>

            <main class="main-content">
                <div class="card">
                    <div class="card-header">
                    <h1>إضافة خبر جديد</h1>
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
                    
    <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="title"><i class="fas fa-heading"></i> عنوان الخبر</label>
        <input type="text" id="title" name="title" class="form-control" placeholder="أدخل عنوان الخبر" value="<?php echo isset($title) ? $title : ''; ?>" required>
    </div>
    
    <div class="form-group">
        <label for="category_id"><i class="fas fa-folder"></i> الفئة</label>
        <select id="category_id" name="category_id" class="form-control" required>
        <option value="">اختر الفئة</option>
        <?php foreach ($categories as $category): ?>
        <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
    <?php endforeach; ?>
    </select>
    </div>
    
    <div class="form-group">
        <label for="content"><i class="fas fa-file-alt"></i> تفاصيل الخبر</label>
        <textarea id="content" name="content" class="form-control" rows="10" placeholder="أدخل تفاصيل الخبر" required><?php echo isset($content) ? $content : ''; ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="image"><i class="fas fa-image"></i> صورة الخبر</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">
    </div>
    
    <div class="form-actions">
        <button type="submit" name="add_news" class="btn btn-success"><i class="fas fa-plus"></i> إضافة الخبر</button>
        <a href="view_news.php" class="btn btn-outline"><i class="fas fa-list"></i> عرض الأخبار</a>
    </div>
</form>
</div>
</main>
</div>
</div>
</body>
</html>