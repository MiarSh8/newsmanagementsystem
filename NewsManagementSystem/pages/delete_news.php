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
$sql = "UPDATE news SET deleted = '1', updated_at = CURRENT_TIMESTAMP WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $news_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "تم حذف الخبر بنجاح";
} else {
    $_SESSION['error'] = "حدث خطأ أثناء حذف الخبر";
}

header("Location: view_news.php");
exit();
