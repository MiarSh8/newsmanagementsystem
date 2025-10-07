<?php
session_start();
include "../includes/connection.php";
include "../includes/functions.php";
checkLogin();

$sql = "SELECT n.*, c.name as category_name, u.name as user_name 
        FROM news n 
        LEFT JOIN categories c ON n.category_id = c.id 
        LEFT JOIN users u ON n.user_id = u.id 
        WHERE n.deleted = '1' 
        ORDER BY n.created_at DESC";
$result = $connection->query($sql);
?>

//--------------------------------------------------

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الأخبار المحذوفة - نظام إدارة الأخبار</title>
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
                    <li><a href="deleted_news.php" class="active">الأخبار المحذوفة</a></li>
                </ul>
            </aside>

            <main class="main-content">
                <div class="card">
                    <div class="card-header">
                        <h1>الأخبار المحذوفة</h1>
                </div>
                    
                    <?php if ($result->num_rows > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>العنوان</th>
                                    <th>الفئة</th>
                                    <th>المستخدم</th>
                                    <th>تاريخ الحذف</th>
                                </tr>
                            </thead>
                            <tbody>

                    <?php $counter = 1; ?>
                    <?php while ($news = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo $news['title']; ?></td>
                                    <td><?php echo $news['category_name']; ?></td>
                                    <td><?php echo $news['user_name']; ?></td>
                                    <td><?php echo date('Y/m/d', strtotime($news['updated_at'])); ?></td>
                                </tr>
                    <?php $counter++; ?>
                    <?php endwhile; ?>
                    </tbody>
                    </table>
                    <?php else: ?>
                    <p>لا توجد أخبار محذوفة.</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>