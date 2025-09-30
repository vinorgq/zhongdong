<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => '未授权访问']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $upload_result = uploadFileEnhanced($_FILES['image']);
    
    if ($upload_result['success']) {
        echo json_encode([
            'success' => true,
            'url' => $upload_result['url'],
            'type' => $upload_result['type'],
            'original_name' => $upload_result['original_name']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $upload_result['message']
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => '无效请求']);
}
?>