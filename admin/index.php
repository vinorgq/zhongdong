<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 检查登录状态
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 获取统计数据
$news_count = getRecordCount('news');
$products_count = getRecordCount('products');
$categories_count = getRecordCount('categories');
$users_count = getRecordCount('users');

// 获取最近新闻
$recent_news = getRecentNews(5);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>仪表板 - 众东科技后台</title>
    
    <!-- 本地Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 本地Font Awesome CSS -->
    <link href="../assets/css/fontawesome-fixed.css" rel="stylesheet">
    
    <!-- 后台统一样式 -->
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-tachometer-alt text-primary"></i> 仪表板
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <span class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- 统计卡片 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                新闻数量
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $news_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-newspaper fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                产品数量
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $products_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                分类数量
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $categories_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                用户数量
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $users_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 最近新闻 -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-newspaper"></i> 最近新闻
                    </h6>
                </div>
                <div class="card-body">
                    <?php if(empty($recent_news)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>暂无新闻</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>标题</th>
                                        <th>状态</th>
                                        <th>发布时间</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_news as $news): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo mb_substr($news['title'], 0, 30); ?><?php echo mb_strlen($news['title']) > 30 ? '...' : ''; ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $news['status'] == 'published' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo $news['status'] == 'published' ? '已发布' : '草稿'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $news['published_at'] ? date('Y-m-d', strtotime($news['published_at'])) : '未发布'; ?></td>
                                        <td>
                                            <a href="news-edit.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> 编辑
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 快捷操作 -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> 快捷操作
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="news-edit.php" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i> 发布新闻
                        </a>
                        <a href="products-edit.php" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> 添加产品
                        </a>
                        <a href="pages-edit.php" class="btn btn-info btn-block">
                            <i class="fas fa-plus"></i> 创建页面
                        </a>
                        <a href="media.php" class="btn btn-warning btn-block">
                            <i class="fas fa-upload"></i> 上传文件
                        </a>
                    </div>
                </div>
            </div>

            <!-- 系统信息 -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> 系统信息
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-2">
                            <strong>PHP版本:</strong> <?php echo PHP_VERSION; ?>
                        </div>
                        <div class="mb-2">
                            <strong>服务器时间:</strong> <?php echo date('Y-m-d H:i:s'); ?>
                        </div>
                        <div class="mb-2">
                            <strong>登录用户:</strong> <?php echo $_SESSION['username']; ?>
                        </div>
                        <div>
                            <strong>用户角色:</strong> 
                            <span class="badge bg-<?php echo $_SESSION['role'] == 'admin' ? 'danger' : 'secondary'; ?>">
                                <?php echo $_SESSION['role'] == 'admin' ? '管理员' : '编辑'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>