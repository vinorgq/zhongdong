<?php
// 后台头部文件
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>众东科技后台管理系统</title>
    
    <!-- 本地Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 本地Font Awesome CSS -->
    <link href="../assets/css/fontawesome-fixed.css" rel="stylesheet">
    
    <!-- 后台统一样式 -->
    <link href="../assets/css/admin.css" rel="stylesheet">
    
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
            font-weight: bold;
        }
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- 侧边栏 -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4 p-3">
                        <h4 class="text-white">
                            <i class="fas fa-cogs"></i> 众东科技
                        </h4>
                        <small class="text-white-50">后台管理系统</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                仪表板
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'news.php' || $current_page == 'news-edit.php' ? 'active' : ''; ?>" href="news.php">
                                <i class="fas fa-newspaper"></i>
                                新闻管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'products.php' || $current_page == 'products-edit.php' ? 'active' : ''; ?>" href="products.php">
                                <i class="fas fa-box"></i>
                                产品管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'categories.php' || $current_page == 'categories-edit.php' ? 'active' : ''; ?>" href="categories.php">
                                <i class="fas fa-tags"></i>
                                分类管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'pages.php' || $current_page == 'pages-edit.php' ? 'active' : ''; ?>" href="pages.php">
                                <i class="fas fa-file-alt"></i>
                                页面管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'media.php' ? 'active' : ''; ?>" href="media.php">
                                <i class="fas fa-images"></i>
                                媒体管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'users.php' || $current_page == 'users-edit.php' ? 'active' : ''; ?>" href="users.php">
                                <i class="fas fa-users"></i>
                                用户管理
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../index.php" target="_blank">
                                <i class="fas fa-eye"></i>
                                查看网站
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                退出登录
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- 主内容区 -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">