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
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$id])) {
        $message = "产品删除成功";
    } else {
        $error = "产品删除失败";
    }
    header('Location: products.php?message=' . urlencode($message ?? $error ?? ''));
    exit;
}

// 获取产品列表
$products = getAllProducts();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>产品管理 - 众东科技后台</title>
    
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
                <i class="fas fa-box text-success"></i> 产品管理
            </h1>
            <a href="products-edit.php" class="btn btn-success">
                <i class="fas fa-plus"></i> 添加产品
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
                    <i class="fas fa-list"></i> 产品列表
                </h5>
            </div>
            <div class="card-body">
                <?php if(empty($products)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-box fa-4x mb-3"></i>
                        <h5>暂无产品</h5>
                        <p>点击"添加产品"按钮创建第一个产品</p>
                        <a href="products-edit.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> 添加产品
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach($products as $product): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <?php if($product['image_url']): ?>
                                <img src="../<?php echo $product['image_url']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-box fa-3x text-muted"></i>
                                </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-muted small">
                                        <?php echo mb_substr($product['description'], 0, 80); ?>...
                                    </p>
                                    <div class="mb-2">
                                        <span class="badge bg-info"><?php echo $product['category_name']; ?></span>
                                        <span class="badge <?php echo $product['status'] == 'published' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $product['status'] == 'published' ? '已发布' : '草稿'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100">
                                        <a href="products-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> 编辑
                                        </a>
                                        <a href="products.php?delete=<?php echo $product['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirmDelete('确定删除这个产品吗？')">
                                            <i class="fas fa-trash"></i> 删除
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            共 <strong><?php echo count($products); ?></strong> 个产品
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>