<?php
// debug.php - 诊断配置文件问题
echo "<h3>配置文件诊断</h3>";

// 检查文件是否存在
$config_file = __DIR__ . '/config/database.php';
echo "配置文件路径: " . $config_file . "<br>";
echo "文件是否存在: " . (file_exists($config_file) ? '是' : '否') . "<br>";

if (file_exists($config_file)) {
    echo "文件权限: " . substr(sprintf('%o', fileperms($config_file)), -4) . "<br>";
    echo "文件大小: " . filesize($config_file) . " 字节<br>";
    
    // 检查文件内容
    $content = file_get_contents($config_file);
    if (strpos($content, '<?php') === false) {
        echo "<span style='color:red'>警告: 文件可能不是有效的PHP文件</span><br>";
    }
    
    // 尝试包含文件
    echo "尝试包含文件...<br>";
    try {
        require_once $config_file;
        echo "<span style='color:green'>文件包含成功!</span><br>";
        
        // 测试数据库连接
        if (function_exists('getDBConnection')) {
            try {
                $pdo = getDBConnection();
                echo "<span style='color:green'>数据库连接成功!</span><br>";
            } catch (Exception $e) {
                echo "<span style='color:red'>数据库连接失败: " . $e->getMessage() . "</span><br>";
            }
        } else {
            echo "<span style='color:red'>getDBConnection 函数不存在</span><br>";
        }
        
    } catch (Exception $e) {
        echo "<span style='color:red'>文件包含失败: " . $e->getMessage() . "</span><br>";
    }
} else {
    echo "<span style='color:red'>配置文件不存在!</span><br>";
    
    // 检查目录权限
    $config_dir = __DIR__ . '/config';
    echo "配置目录权限: " . (is_dir($config_dir) ? substr(sprintf('%o', fileperms($config_dir)), -4) : '目录不存在') . "<br>";
}
?>