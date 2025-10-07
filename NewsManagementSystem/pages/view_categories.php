<?php
session_start();
include "../includes/connection.php";
include "../includes/functions.php";

checkLogin();

$categories = getCategories($connection);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض الفئات - نظام إدارة الأخبار</title>
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
                    <li><a href="view_categories.php" class="active">عرض الفئات</a></li>
                    <li><a href="add_news.php">إضافة خبر</a></li>
                    <li><a href="view_news.php">عرض جميع الأخبار</a></li>
                    <li><a href="deleted_news.php">الأخبار المحذوفة</a></li>
                </ul>
            </aside>

            <main class="main-content">
                <div class="card">
                    <div class="card-header">
                        <h1>الفئات</h1>
                        <a href="add_category.php" class="btn">إضافة فئة جديدة</a>
                    </div>
                    
                    <?php if (count($categories) > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم الفئة</th>
                                    <th>تاريخ الإضافة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $index => $category): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo $category['name']; ?></td>
                                        <td><?php echo date('Y/m/d', strtotime($category['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                    <p>لا توجد فئات مضافة بعد.</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>