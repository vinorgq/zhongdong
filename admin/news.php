<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// 检查登录状态和权限
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 处理删除操作
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo = getDBConnection();
    $sql = "DELETE FROM news WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$id])) {
        $message = "新闻删除成功";
    } else {
        $error = "新闻删除失败";
    }
    header('Location: news.php?message=' . urlencode($message ?? $error ?? ''));
    exit;
}

// 获取新闻列表
$pdo = getDBConnection();
$sql = "SELECT n.*, c.name as category_name, u.username as author_name 
        FROM news n 
        LEFT JOIN categories c ON n.category_id = c.id 
        LEFT JOIN users u ON n.author_id = u.id 
        ORDER BY n.created_at DESC";
$stmt = $pdo->query($sql);
$news_list = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新闻管理 - 众东科技后台</title>
    
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
                <i class="fas fa-newspaper text-primary"></i> 新闻管理
            </h1>
            <a href="news-edit.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> 发布新闻
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
                    <i class="fas fa-list"></i> 新闻列表
                </h5>
            </div>
            <div class="card-body">
                <?php if(empty($news_list)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-newspaper fa-4x mb-3"></i>
                        <h5>暂无新闻</h5>
                        <p>点击"发布新闻"按钮创建第一篇新闻</p>
                        <a href="news-edit.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> 发布新闻
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>标题</th>
                                    <th>分类</th>
                                    <th>状态</th>
                                    <th>作者</th>
                                    <th>发布时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($news_list as $news): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($news['title']); ?></strong>
                                        <?php if($news['summary']): ?>
                                        <br><small class="text-muted"><?php echo mb_substr($news['summary'], 0, 50); ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $news['category_name']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $news['status'] == 'published' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $news['status'] == 'published' ? '已发布' : '草稿'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $news['author_name']; ?></td>
                                    <td>
                                        <?php if($news['published_at']): ?>
                                            <?php echo date('Y-m-d H:i', strtotime($news['published_at'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">未发布</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="news-edit.php?id=<?php echo $news['id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-edit"></i> 编辑
                                            </a>
                                            <a href="news.php?delete=<?php echo $news['id']; ?>" 
                                               class="btn btn-danger" 
                                               onclick="return confirmDelete('确定删除这篇新闻吗？')">
                                                <i class="fas fa-trash"></i> 删除
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
                            共 <strong><?php echo count($news_list); ?></strong> 篇新闻
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#">上一页</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item disabled">
                                    <a class="page-link" href="#">下一页</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>