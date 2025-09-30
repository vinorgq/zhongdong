<?php
session_start();

// 使用更保守的配置
@ini_set('upload_max_filesize', '5000M');
@ini_set('post_max_size', '5000M');
@ini_set('max_execution_time', '3000');
@ini_set('max_input_time', '3000');
@ini_set('memory_limit', '256M');

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => '未授权访问']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    // 检查上传错误
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => '文件大小超过服务器限制',
            UPLOAD_ERR_FORM_SIZE => '文件大小超过表单限制',
            UPLOAD_ERR_PARTIAL => '文件只有部分被上传',
            UPLOAD_ERR_NO_FILE => '没有文件被上传',
            UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹',
            UPLOAD_ERR_CANT_WRITE => '文件写入失败',
            UPLOAD_ERR_EXTENSION => 'PHP扩展阻止了文件上传'
        ];
        
        $error_message = $error_messages[$_FILES['file']['error']] ?? '未知上传错误';
        
        http_response_code(413);
        echo json_encode([
            'success' => false, 
            'message' => '上传失败: ' . $error_message . ' (错误代码: ' . $_FILES['file']['error'] . ')'
        ]);
        exit;
    }
    
    $upload_result = uploadFile($_FILES['file']);
    
    if ($upload_result['success']) {
        echo json_encode([
            'success' => true,
            'message' => '文件上传成功',
            'file' => [
                'name' => $upload_result['filename'],
                'url' => $upload_result['url'],
                'type' => $upload_result['type'],
                'original_name' => $upload_result['original_name']
            ]
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $upload_result['message']
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '无效请求']);
}
?>