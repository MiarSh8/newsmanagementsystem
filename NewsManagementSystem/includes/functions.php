<?php
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function getUserName($connection, $user_id) {
    $sql = "SELECT name FROM users WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    }
    return "مستخدم غير معروف";
}


function getCategories($connection) {//هذه الدالة تقوم بعمل استيراد للفئات الموجودة بالداتا بيز
    $categories = array();
    $sql = "SELECT * FROM categories ORDER BY name";
    $result = $connection->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}


function getNewsCountByCategory($connection, $category_id) {
    $sql = "SELECT COUNT(*) as count FROM news WHERE category_id = ? AND deleted = '0'";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    return 0;
}

