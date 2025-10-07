<?php
session_start();
include "../includes/connection.php";
include "../includes/functions.php";

checkLogin();

$news_count_sql = "SELECT COUNT(*) as count FROM news WHERE deleted = '0'";
$news_count_result = $connection->query($news_count_sql);
$news_count = $news_count_result->fetch_assoc()['count'];

$categories_count_sql = "SELECT COUNT(*) as count FROM categories";
$categories_count_result = $connection->query($categories_count_sql);
$categories_count = $categories_count_result->fetch_assoc()['count'];

$deleted_news_count_sql = "SELECT COUNT(*) as count FROM news WHERE deleted = '1'";
$deleted_news_count_result = $connection->query($deleted_news_count_sql);
$deleted_news_count = $deleted_news_count_result->fetch_assoc()['count'];

$recent_news_sql = "SELECT n.*, c.name as category_name, u.name as user_name 
                    FROM news n 
                    LEFT JOIN categories c ON n.category_id = c.id 
                    LEFT JOIN users u ON n.user_id = u.id 
                    WHERE n.deleted = '0' 
                    ORDER BY n.created_at DESC 
                    LIMIT 5";
$recent_news_result = $connection->query($recent_news_sql);
?>
//----------------------------------------------

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام إدارة الأخبار</title>
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
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a>
                    </li>
                    <li><a href="add_category.php"><i class="fas fa-folder-plus"></i> إضافة فئة</a></li>
                    <li><a href="view_categories.php"><i class="fas fa-folder-open"></i> عرض الفئات</a></li>
                    <li><a href="add_news.php"><i class="fas fa-newspaper"></i> إضافة خبر</a></li>
                    <li><a href="view_news.php"><i class="fas fa-list-alt"></i> عرض جميع الأخبار</a></li>
                    <li><a href="deleted_news.php"><i class="fas fa-trash"></i> الأخبار المحذوفة</a></li>
                </ul>
            </aside>

            <main class="main-content">
                <h1 style="margin-bottom: 20px;"> </h1>
                <div class="cards-grid">
                    <div class="stat-card">
                        <h3><i class="fas fa-newspaper"></i> عدد الأخبار</h3>
                        <p class="number"><?php echo $news_count; ?></p>
                    </div>

                    <div class="stat-card">
                        <h3><i class="fas fa-folder"></i> عدد الفئات</h3>
                        <p class="number"><?php echo $categories_count; ?></p>
                    </div>

                    <div class="stat-card">
                        <h3><i class="fas fa-trash"></i> الأخبار المحذوفة</h3>
                        <p class="number"><?php echo $deleted_news_count; ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                    <h2>آخر الأخبار</h2>
                    <a href="view_news.php" class="btn">عرض الكل</a>
                </div>

                <?php if ($recent_news_result->num_rows > 0): ?>
                    <table class="table">
                    <thead>
                        <tr>
                        <th>العنوان</th>
                        <th>الفئة</th>
                        <th>المستخدم</th>
                        <th>التاريخ</th>
                        </tr>
                    </thead>
                        <tbody>
                <?php while ($news = $recent_news_result->fetch_assoc()): ?>
                        <tr>
                        <td><?php echo $news['title']; ?></td>
                        <td><?php echo $news['category_name']; ?></td>
                        <td><?php echo $news['user_name']; ?></td>
                        <td><?php echo date('Y/m/d', strtotime($news['created_at'])); ?></td>
                        </tr>
                <?php endwhile; ?>
                        </tbody>
                        </table>
                <?php else: ?>
                <p>لا توجد أخبار مضافة بعد.</p>
                <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>