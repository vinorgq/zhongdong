<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

echo "<pre>";
echo "=== 表单诊断信息 ===\n";

// 检查POST数据
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "POST请求收到\n";
    echo "POST数据: " . print_r($_POST, true) . "\n";
    echo "FILES数据: " . print_r($_FILES, true) . "\n";
} else {
    echo "GET请求\n";
}

// 检查分类数据
$categories = getAllCategories('news');
echo "可用分类: " . print_r($categories, true) . "\n";

echo "当前用户ID: " . ($_SESSION['user_id'] ?? '未设置') . "\n";
echo "Session数据: " . print_r($_SESSION, true) . "\n";
echo "</pre>";
?>