<?php
// check_permissions.php
header('Content-Type: text/plain; charset=utf-8');

echo "=== 权限检查 ===\n\n";

$dirs_to_check = [
    '/www/wwwroot/47.93.138.227/assets/',
    '/www/wwwroot/47.93.138.227/assets/uploads/',
    '/www/wwwroot/47.93.138.227/assets/uploads/images/',
    '/www/wwwroot/47.93.138.227/assets/uploads/documents/',
    '/www/wwwroot/47.93.138.227/assets/uploads/videos/',
    '/www/wwwroot/47.93.138.227/assets/uploads/audios/',
    '/www/wwwroot/47.93.138.227/assets/uploads/archives/',
    '/www/wwwroot/47.93.138.227/assets/uploads/others/',
    '/www/wwwroot/47.93.138.227/assets/temp/',
    '/www/wwwroot/47.93.138.227/assets/previews/'
];

foreach ($dirs_to_check as $dir) {
    echo "检查: $dir\n";
    if (file_exists($dir)) {
        echo "✓ 目录存在\n";
        echo "权限: " . substr(sprintf('%o', fileperms($dir)), -4) . "\n";
        echo "可写: " . (is_writable($dir) ? '是' : '否') . "\n";
        echo "所有者: " . fileowner($dir) . "\n";
        echo "组: " . filegroup($dir) . "\n";
    } else {
        echo "✗ 目录不存在\n";
        // 尝试创建
        if (@mkdir($dir, 0755, true)) {
            echo "✓ 目录创建成功\n";
        } else {
            echo "✗ 目录创建失败\n";
        }
    }
    echo "---\n";
}

// 检查当前用户
echo "当前用户: " . get_current_user() . "\n";
echo "进程用户: " . exec('whoami') . "\n";

// 检查 Web 服务器用户
echo "\nWeb 服务器进程:\n";
exec('ps aux | grep -E "nginx|apache|httpd" | head -5', $output);
foreach ($output as $line) {
    echo $line . "\n";
}
?>