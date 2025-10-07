<?php
session_start();
include "includes/connection.php";

if (isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
    exit();
} else {
    header("Location: pages/login.php");
    exit();
}
