<?php
// 安装脚本 - 众东科技网站
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 检查是否已安装
if (file_exists('config/installed.lock')) {
    die('网站已经安装完成，如需重新安装请删除 config/installed.lock 文件');
}

// 处理安装表单
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_name = $_POST['db_name'] ?? 'zhongdong_tech';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';
    $admin_user = $_POST['admin_user'] ?? 'admin';
    $admin_pass = $_POST['admin_pass'] ?? 'admin123';
    $admin_email = $_POST['admin_email'] ?? 'admin@zhongdong.com';
    
    try {
        // 测试数据库连接
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 创建数据库
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$db_name`");
        
        // 创建表结构
        createDatabaseTables($pdo);
        
        // 创建管理员账户
        createAdminUser($pdo, $admin_user, $admin_pass, $admin_email);
        
        // 写入配置文件
        writeConfigFile($db_host, $db_name, $db_user, $db_pass);
        
        // 创建安装锁定文件
        file_put_contents('config/installed.lock', '安装完成时间: ' . date('Y-m-d H:i:s'));
        
        // 显示成功页面
        showSuccessPage($admin_user, $admin_pass);
        
    } catch (PDOException $e) {
        $error = "数据库连接失败: " . $e->getMessage();
    }
}

/**
 * 创建数据库表
 */
function createDatabaseTables($pdo) {
    // 用户表
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        role ENUM('admin', 'editor') DEFAULT 'editor',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // 分类表
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        parent_id INT DEFAULT NULL,
        type ENUM('news', 'product', 'page') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // 新闻表
    $sql = "CREATE TABLE IF NOT EXISTS news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        summary TEXT,
        category_id INT,
        image_url VARCHAR(255),
        author_id INT,
        status ENUM('published', 'draft') DEFAULT 'draft',
        published_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (author_id) REFERENCES users(id),
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )";
    $pdo->exec($sql);
    
    // 页面表
    $sql = "CREATE TABLE IF NOT EXISTS pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        content TEXT,
        meta_title VARCHAR(255),
        meta_description TEXT,
        status ENUM('published', 'draft') DEFAULT 'published',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // 产品表
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        features TEXT,
        image_url VARCHAR(255),
        category_id INT,
        status ENUM('published', 'draft') DEFAULT 'published',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )";
    $pdo->exec($sql);
    
    // 插入默认数据
    insertDefaultData($pdo);
}

/**
 * 插入默认数据
 */
function insertDefaultData($pdo) {
    // 创建默认分类
    $default_categories = [
        ['name' => '公司新闻', 'slug' => 'company-news', 'type' => 'news'],
        ['name' => '行业动态', 'slug' => 'industry-news', 'type' => 'news'],
        ['name' => '平台产品', 'slug' => 'platform-products', 'type' => 'product'],
        ['name' => '技术服务', 'slug' => 'tech-services', 'type' => 'product']
    ];
    
    foreach ($default_categories as $category) {
        $sql = "INSERT IGNORE INTO categories (name, slug, type) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$category['name'], $category['slug'], $category['type']]);
    }
    
    // 创建默认页面
    $default_pages = [
        [
            'title' => '首页', 
            'slug' => 'home', 
            'content' => '<h1>欢迎来到众东科技</h1><p>企业数字科技综合服务平台</p><p>科技赋能 / 产数融合 / 共享服务</p>'
        ],
        [
            'title' => '关于我们', 
            'slug' => 'about', 
            'content' => '<h1>关于众东科技</h1><p>众东科技是一家集文化科技产业链资源融合、信息技术服务、软件开发及运维、企业数字化全流程于一体的科技综合服务平台。</p>'
        ],
        [
            'title' => '行业背景', 
            'slug' => 'industry', 
            'content' => '<h1>行业背景</h1><p>2024年将是中国社会经济系统全面强化数据要素的一年，是各产业全面开展数字化转型的一年...</p>'
        ],
        [
            'title' => '产品介绍', 
            'slug' => 'products', 
            'content' => '<h1>产品与服务</h1><p>我们提供全方位的数字科技解决方案...</p>'
        ]
    ];
    
    foreach ($default_pages as $page) {
        $sql = "INSERT IGNORE INTO pages (title, slug, content) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$page['title'], $page['slug'], $page['content']]);
    }
}

/**
 * 创建管理员账户
 */
function createAdminUser($pdo, $username, $password, $email) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT IGNORE INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $hashed_password, $email]);
}

/**
 * 写入配置文件
 */
function writeConfigFile($host, $name, $user, $pass) {
    $config_content = "<?php
// 数据库配置
define('DB_HOST', '$host');
define('DB_NAME', '$name');
define('DB_USER', '$user');
define('DB_PASS', '$pass');
define('DB_CHARSET', 'utf8mb4');

// 创建数据库连接
function getDBConnection() {
    try {
        \$dsn = \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=\" . DB_CHARSET;
        \$pdo = new PDO(\$dsn, DB_USER, DB_PASS);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return \$pdo;
    } catch(PDOException \$e) {
        die(\"数据库连接失败: \" . \$e->getMessage());
    }
}
?>";
    
    // 确保config目录存在
    if (!is_dir('config')) {
        mkdir('config', 0755, true);
    }
    
    file_put_contents('config/database.php', $config_content);
}

/**
 * 显示成功页面
 */
function showSuccessPage($admin_user, $admin_pass) {
    echo "<!DOCTYPE html>
<html lang='zh-CN'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>安装完成 - 众东科技</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css'>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='row justify-content-center'>
            <div class='col-md-8 col-lg-6'>
                <div class='success-card p-5'>
                    <div class='text-center mb-4'>
                        <i class='bi bi-check-circle-fill text-success display-1'></i>
                        <h2 class='mt-3 text-success'>安装成功！</h2>
                    </div>
                    
                    <div class='alert alert-success'>
                        <h5><i class='bi bi-info-circle'></i> 众东科技网站已成功安装</h5>
                    </div>
                    
                    <div class='card mb-4'>
                        <div class='card-header bg-primary text-white'>
                            <h5 class='mb-0'><i class='bi bi-person-badge'></i> 管理员账户信息</h5>
                        </div>
                        <div class='card-body'>
                            <table class='table table-bordered'>
                                <tr>
                                    <th width='30%'>用户名：</th>
                                    <td><strong>$admin_user</strong></td>
                                </tr>
                                <tr>
                                    <th>密码：</th>
                                    <td><strong>$admin_pass</strong></td>
                                </tr>
                                <tr>
                                    <th>后台地址：</th>
                                    <td><code>yourdomain.com/admin/</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class='alert alert-warning'>
                        <h6><i class='bi bi-exclamation-triangle'></i> 重要安全提示</h6>
                        <ul class='mb-0'>
                            <li>请立即删除 <code>install.php</code> 文件</li>
                            <li>建议修改默认管理员密码</li>
                            <li>定期备份数据库</li>
                        </ul>
                    </div>
                    
                    <div class='text-center mt-4'>
                        <a href='admin/login.php' class='btn btn-primary btn-lg me-3'>
                            <i class='bi bi-gear'></i> 进入后台管理
                        </a>
                        <a href='index.php' class='btn btn-outline-primary btn-lg'>
                            <i class='bi bi-house'></i> 查看网站首页
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网站安装 - 众东科技</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .install-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .step-indicator:before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }
        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: bold;
        }
        .step.active .step-number {
            background: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="install-card p-5">
                    <div class="text-center mb-4">
                        <h1 class="h3">众东科技网站安装</h1>
                        <p class="text-muted">请填写以下信息完成网站安装</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="step-indicator">
                        <div class="step active">
                            <div class="step-number">1</div>
                            <small>数据库配置</small>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <small>管理员设置</small>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <small>完成安装</small>
                        </div>
                    </div>
                    
                    <form method="POST">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-database"></i> 数据库配置</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="db_host" class="form-label">数据库主机</label>
                                        <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                                        <div class="form-text">通常是 localhost</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="db_name" class="form-label">数据库名称</label>
                                        <input type="text" class="form-control" id="db_name" name="db_name" value="zhongdong_tech" required>
                                        <div class="form-text">数据库不存在时会自动创建</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="db_user" class="form-label">数据库用户名</label>
                                        <input type="text" class="form-control" id="db_user" name="db_user" value="root" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="db_pass" class="form-label">数据库密码</label>
                                        <input type="password" class="form-control" id="db_pass" name="db_pass">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-person-gear"></i> 管理员账户</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="admin_user" class="form-label">管理员用户名</label>
                                        <input type="text" class="form-control" id="admin_user" name="admin_user" value="admin" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="admin_pass" class="form-label">管理员密码</label>
                                        <input type="password" class="form-control" id="admin_pass" name="admin_pass" value="admin123" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_email" class="form-label">管理员邮箱</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" value="admin@zhongdong.com">
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> 安装说明</h6>
                            <ul class="mb-0">
                                <li>请确保数据库用户有创建数据库的权限</li>
                                <li>安装完成后会自动创建必要的表和默认数据</li>
                                <li>请务必记住管理员账户信息</li>
                            </ul>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-gear"></i> 开始安装
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>