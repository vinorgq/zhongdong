<?php
echo "<h2>服务器上传配置检查</h2>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "max_input_time: " . ini_get('max_input_time') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";

// 测试上传目录权限
$upload_dir = "../assets/uploads/";
echo "上传目录权限: " . (is_writable($upload_dir) ? "可写" : "不可写") . "<br>";

// 测试各子目录
$sub_dirs = ['images/', 'documents/', 'videos/', 'audios/', 'archives/', 'others/'];
foreach ($sub_dirs as $sub_dir) {
    $dir = $upload_dir . $sub_dir;
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    echo "{$sub_dir} 权限: " . (is_writable($dir) ? "可写" : "不可写") . "<br>";
}
?>