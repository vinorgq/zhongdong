<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 获取页面列表
$pdo = getDBConnection();
$sql = "SELECT * FROM pages ORDER BY title";
$stmt = $pdo->query($sql);
$pages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>页面管理 - 众东科技后台</title>
    
    <!-- 本地Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 本地Font Awesome CSS -->
    <link href="../assets/css/fontawesome-fixed.css" rel="stylesheet">
    
    <!-- 后台统一样式 -->
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-file-alt text-warning"></i> 页面管理
            </h1>
            <a href="pages-edit.php" class="btn btn-warning">
                <i class="fas fa-plus"></i> 创建页面
            </a>
        </div>

        <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?php echo $_GET['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> 页面列表
                </h5>
            </div>
            <div class="card-body">
                <?php if(empty($pages)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-file-alt fa-4x mb-3"></i>
                        <h5>暂无页面</h5>
                        <p>点击"创建页面"按钮创建第一个页面</p>
                        <a href="pages-edit.php" class="btn btn-warning">
                            <i class="fas fa-plus"></i> 创建页面
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>页面标题</th>
                                    <th>标识符</th>
                                    <th>状态</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($pages as $page): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($page['title']); ?></strong>
                                        <?php if($page['meta_title']): ?>
                                        <br><small class="text-muted">Meta: <?php echo htmlspecialchars($page['meta_title']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code class="text-warning"><?php echo $page['slug']; ?></code>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $page['status'] == 'published' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $page['status'] == 'published' ? '已发布' : '草稿'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($page['updated_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="pages-edit.php?id=<?php echo $page['id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-edit"></i> 编辑
                                            </a>
                                            <a href="../<?php echo $page['slug']; ?>.php" target="_blank" class="btn btn-info">
                                                <i class="fas fa-eye"></i> 查看
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            共 <strong><?php echo count($pages); ?></strong> 个页面
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>