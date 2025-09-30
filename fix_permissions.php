<?php
/**
 * 修复上传目录权限脚本
 */

// 使用绝对路径
$base_dir = '/www/wwwroot/47.93.138.227/assets/uploads/';
$sub_dirs = ['images/', 'documents/', 'videos/', 'audios/', 'archives/', 'others/'];

echo "<h2>修复上传目录权限</h2>";
echo "目标目录: $base_dir<br><br>";

// 检查并创建主目录
if (!file_exists($base_dir)) {
    if (mkdir($base_dir, 0755, true)) {
        echo "✓ 创建主目录: $base_dir<br>";
    } else {
        echo "✗ 无法创建主目录: $base_dir<br>";
        echo "错误信息: " . error_get_last()['message'] . "<br>";
    }
} else {
    echo "✓ 主目录已存在: $base_dir<br>";
}

// 设置主目录权限
if (is_dir($base_dir)) {
    if (chmod($base_dir, 0755)) {
        echo "✓ 设置主目录权限: 755<br>";
    } else {
        echo "✗ 无法设置主目录权限<br>";
    }
}

// 创建子目录
foreach ($sub_dirs as $sub_dir) {
    $full_path = $base_dir . $sub_dir;
    
    if (!file_exists($full_path)) {
        if (mkdir($full_path, 0755, true)) {
            echo "✓ 创建子目录: $sub_dir<br>";
        } else {
            echo "✗ 无法创建子目录: $sub_dir<br>";
            echo "错误信息: " . error_get_last()['message'] . "<br>";
        }
    } else {
        echo "✓ 子目录已存在: $sub_dir<br>";
    }
    
    // 设置子目录权限
    if (is_dir($full_path)) {
        if (chmod($full_path, 0755)) {
            echo "✓ 设置子目录权限: $sub_dir -> 755<br>";
        } else {
            echo "✗ 无法设置子目录权限: $sub_dir<br>";
        }
    }
}

echo "<br><h3>权限检查结果:</h3>";

// 检查主目录
if (is_dir($base_dir)) {
    echo "主目录存在: 是<br>";
    echo "主目录可读: " . (is_readable($base_dir) ? "是" : "否") . "<br>";
    echo "主目录可写: " . (is_writable($base_dir) ? "是" : "否") . "<br>";
    
    // 测试写入
    $test_file = $base_dir . 'test_write.txt';
    if (file_put_contents($test_file, 'test')) {
        echo "✓ 主目录写入测试成功<br>";
        unlink($test_file);
        echo "✓ 清理测试文件<br>";
    } else {
        echo "✗ 主目录写入测试失败<br>";
    }
} else {
    echo "主目录存在: 否<br>";
}

// 检查子目录
foreach ($sub_dirs as $sub_dir) {
    $full_path = $base_dir . $sub_dir;
    if (is_dir($full_path)) {
        echo "$sub_dir 可写: " . (is_writable($full_path) ? "是" : "否") . "<br>";
    } else {
        echo "$sub_dir 目录不存在<br>";
    }
}

echo "<br><h3>建议的SSH命令:</h3>";
echo "<code>chmod 755 /www/wwwroot/47.93.138.227/assets/</code><br>";
echo "<code>chmod 755 /www/wwwroot/47.93.138.227/assets/uploads/</code><br>";
echo "<code>chmod -R 755 /www/wwwroot/47.93.138.227/assets/uploads/*</code><br>";

echo "<br><h3>如果仍有问题，请检查:</h3>";
echo "1. 确保 assets 目录存在且有权权限<br>";
echo "2. 联系服务器管理员检查SELinux或AppArmor设置<br>";
echo "3. 检查磁盘空间是否充足<br>";
?>