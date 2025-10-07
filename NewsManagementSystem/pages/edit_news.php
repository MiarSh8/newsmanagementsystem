<?php
session_start();
include "../includes/connection.php";
include "../includes/functions.php";
checkLogin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_news.php");
    exit();
}

$news_id = $_GET['id'];
$sql = "SELECT * FROM news WHERE id = ? AND deleted = '0'";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: view_news.php");
    exit();
}

$news = $result->fetch_assoc();
$categories = getCategories($connection);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_news"])) {
    $title = trim($_POST["title"]);
    $category_id = $_POST["category_id"];
    $content = trim($_POST["content"]);
    
    $image_name = $news['image'];
    
if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $allowed_types = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
    $file_name = $_FILES["image"]["name"];
    $file_type = $_FILES["image"]["type"];
    $file_size = $_FILES["image"]["size"];
    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        
if (!array_key_exists($ext, $allowed_types)) {
    $error = "يرجى اختيار صورة بصيغة JPG, JPEG, أو PNG";
}
        
    $max_size = 5 * 1024 * 1024;
        
if ($file_size > $max_size) {
    $error = "حجم الصورة كبير جداً. الحد الأقصى هو 5MB";
}
        
if (empty($error)) {
if (!empty($news['image']) && file_exists("../assets/images/" . $news['image'])) {
    unlink("../assets/images/" . $news['image']);
}
            
        $image_name = "news_" . time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES["image"]["tmp_name"], "../assets/images/" . $image_name);
        }
    }
    
if (empty($error)) {
        $sql = "UPDATE news SET title = ?, category_id = ?, content = ?, image = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sissi", $title, $category_id, $content, $image_name, $news_id);
        
if ($stmt->execute()) {
        $success = "تم تعديل الخبر بنجاح";
        $news['title'] = $title;
        $news['category_id'] = $category_id;
        $news['content'] = $content;
        $news['image'] = $image_name;
    } else {
        $error = "حدث خطأ أثناء تعديل الخبر";
        }
    }
}
?>
//----------------------------------------------

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل خبر - نظام إدارة الأخبار</title>
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
                    <li><a href="add_news.php">إضافة خبر</a></li>
                    <li><a href="view_news.php">عرض جميع الأخبار</a></li>
                    <li><a href="deleted_news.php">الأخبار المحذوفة</a></li>
                </ul>
            </aside>

            <main class="main-content">
                <div class="card">
                    <div class="card-header">
                        <h1>تعديل الخبر</h1>
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
                            <label for="title">عنوان الخبر</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?php echo $news['title']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">الفئة</label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">اختر الفئة</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $news['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">تفاصيل الخبر</label>
                            <textarea id="content" name="content" class="form-control" rows="10" required><?php echo $news['content']; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">صورة الخبر</label>
                            <?php if (!empty($news['image'])): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="../assets/images/<?php echo $news['image']; ?>" alt="صورة الخبر" style="max-width: 200px; max-height: 150px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        </div>
                        
                        <button type="submit" name="edit_news" class="btn">تحديث الخبر</button>
                        <a href="view_news.php" class="btn" style="background: #7f8c8d;">عودة إلى الأخبار</a>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>