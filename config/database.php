<?php
// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'zhongdong');
define('DB_USER', 'zhongdong');
define('DB_PASS', '1112223334');
define('DB_CHARSET', 'utf8mb4');

// 创建数据库连接
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("数据库连接失败: " . $e->getMessage());
    }
}
?>