<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 处理删除操作
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo = getDBConnection();
    
    // 检查是否有相关数据
    $sql = "SELECT COUNT(*) as count FROM news WHERE category_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $news_count = $stmt->fetch()['count'];
    
    $sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $products_count = $stmt->fetch()['count'];
    
    if ($news_count == 0 && $products_count == 0) {
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$id])) {
            $message = "分类删除成功";
        } else {
            $error = "分类删除失败";
        }
    } else {
        $error = "无法删除：该分类下还有数据（新闻：{$news_count}篇，产品：{$products_count}个）";
    }
    
    header("Location: categories.php?message=" . urlencode($message ?? $error ?? ''));
    exit;
}

// 获取分类列表
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>分类管理 - 众东科技后台</title>
    
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
                <i class="fas fa-tags text-info"></i> 分类管理
            </h1>
            <a href="categories-edit.php" class="btn btn-info">
                <i class="fas fa-plus"></i> 添加分类
            </a>
        </div>

        <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-<?php echo strpos($_GET['message'], '成功') !== false ? 'success' : 'danger'; ?> alert-dismissible fade show">
            <i class="fas <?php echo strpos($_GET['message'], '成功') !== false ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i> 
            <?php echo $_GET['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> 分类列表
                </h5>
            </div>
            <div class="card-body">
                <?php if(empty($categories)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-tags fa-4x mb-3"></i>
                        <h5>暂无分类</h5>
                        <p>点击"添加分类"按钮创建第一个分类</p>
                        <a href="categories-edit.php" class="btn btn-info">
                            <i class="fas fa-plus"></i> 添加分类
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>分类名称</th>
                                    <th>标识符</th>
                                    <th>类型</th>
                                    <th>描述</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($categories as $category): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                    </td>
                                    <td>
                                        <code class="text-info"><?php echo $category['slug']; ?></code>
                                    </td>
                                    <td>
                                        <?php 
                                        $type_names = [
                                            'news' => '新闻',
                                            'product' => '产品', 
                                            'page' => '页面'
                                        ];
                                        $type_color = [
                                            'news' => 'primary',
                                            'product' => 'success',
                                            'page' => 'info'
                                        ];
                                        ?>
                                        <span class="badge bg-<?php echo $type_color[$category['type']] ?? 'secondary'; ?>">
                                            <?php echo $type_names[$category['type']] ?? $category['type']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($category['description']): ?>
                                            <?php echo htmlspecialchars($category['description']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">无描述</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($category['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="categories-edit.php?id=<?php echo $category['id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-edit"></i> 编辑
                                            </a>
                                            <a href="categories.php?delete=<?php echo $category['id']; ?>" 
                                               class="btn btn-danger" 
                                               onclick="return confirmDelete('确定删除这个分类吗？')">
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
                            共 <strong><?php echo count($categories); ?></strong> 个分类
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>