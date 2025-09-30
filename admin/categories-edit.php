<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = null;

if ($id) {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM categories WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $category = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $description = $_POST['description'] ?? '';
    $type = $_POST['type'] ?? 'news';
    
    $pdo = getDBConnection();
    
    if ($id && $category) {
        $sql = "UPDATE categories SET name = ?, slug = ?, description = ?, type = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$name, $slug, $description, $type, $id]);
        $message = $result ? "分类更新成功" : "分类更新失败";
    } else {
        $sql = "INSERT INTO categories (name, slug, description, type) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$name, $slug, $description, $type]);
        $message = $result ? "分类添加成功" : "分类添加失败";
    }
    
    if (isset($message) && strpos($message, '成功') !== false) {
        header("Location: categories.php?message=$message");
        exit;
    } elseif (isset($message)) {
        $error = $message;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? '编辑' : '添加'; ?>分类 - 众东科技后台</title>
    
    <!-- 本地Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 本地Font Awesome CSS -->
    <link href="../assets/css/fontawesome-fixed.css" rel="stylesheet">
    
    <!-- 后台统一样式 -->
    <link href="../assets/css/admin.css" rel="stylesheet">
    
    <style>
        .form-container { max-width: 100%; }
        .required:after { content: " *"; color: red; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-tags text-info"></i> <?php echo $id ? '编辑分类' : '添加分类'; ?>
            </h1>
            <a href="categories.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> 返回列表
            </a>
        </div>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" id="categoryForm">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> 分类信息
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">分类名称</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" 
                                           required maxlength="100" placeholder="请输入分类名称">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug" class="form-label required">标识符</label>
                                    <input type="text" class="form-control" id="slug" name="slug" 
                                           value="<?php echo htmlspecialchars($category['slug'] ?? ''); ?>" 
                                           required pattern="[a-z0-9-]+" placeholder="如: company-news">
                                    <div class="form-text">用于URL的英文标识，只能包含小写字母、数字和连字符</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">分类描述</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="请输入分类描述（可选）"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label required">分类类型</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="news" <?php echo ($category['type'] ?? 'news') == 'news' ? 'selected' : ''; ?>>新闻分类</option>
                                <option value="product" <?php echo ($category['type'] ?? '') == 'product' ? 'selected' : ''; ?>>产品分类</option>
                                <option value="page" <?php echo ($category['type'] ?? '') == 'page' ? 'selected' : ''; ?>>页面分类</option>
                            </select>
                            <div class="form-text">选择分类的用途，不同类型的分类用于不同的内容</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save"></i> 保存分类
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="history.back()">
                        <i class="fas fa-times"></i> 取消
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- 本地jQuery -->
    <script src="../assets/js/jquery.min.js"></script>
    <!-- 本地Bootstrap JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // 自动生成slug
            $('#name').on('input', function() {
                if (!$('#slug').val() || $('#slug').val() === '<?php echo $category['slug'] ?? ''; ?>') {
                    var slug = $(this).val()
                        .toLowerCase()
                        .replace(/[^\w\u4e00-\u9fa5]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    $('#slug').val(slug);
                }
            });
        });
    </script>
</body>
</html>