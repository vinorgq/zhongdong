<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$news = null;
$categories = getAllCategories('news');

if ($id) {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM news WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $news = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $summary = $_POST['summary'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $status = $_POST['status'] ?? 'draft';
    
    if (empty($title)) {
        $error = "标题不能为空";
    } else {
        $pdo = getDBConnection();
        
        if ($id && $news) {
            $sql = "UPDATE news SET title = ?, content = ?, summary = ?, category_id = ?, status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$title, $content, $summary, $category_id, $status, $id]);
            $message = $result ? "新闻更新成功" : "新闻更新失败";
        } else {
            $sql = "INSERT INTO news (title, content, summary, category_id, author_id, status, published_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $published_at = ($status == 'published') ? date('Y-m-d H:i:s') : null;
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$title, $content, $summary, $category_id, $_SESSION['user_id'], $status, $published_at]);
            $message = $result ? "新闻添加成功" : "新闻添加失败";
        }
        
        if (isset($message) && strpos($message, '成功') !== false) {
            header("Location: news.php?message=" . urlencode($message));
            exit;
        } elseif (isset($message)) {
            $error = $message;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? '编辑' : '添加'; ?>新闻 - 众东科技后台</title>
    
    <!-- 本地Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 本地Font Awesome CSS (修复版) -->
    <link href="../assets/css/fontawesome-fixed.css" rel="stylesheet">
    
    <!-- 本地Summernote CSS (修复版) -->
    <link href="../assets/css/summernote-fixed.css" rel="stylesheet">
    
    <!-- 本地Summernote额外样式 -->
    <link href="../assets/css/summernote-local.css" rel="stylesheet">
    
    <!-- 后台样式 -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <style>
        .form-container { max-width: 100%; }
        .required:after { content: " *"; color: red; }
        .character-count { font-size: 12px; color: #6c757d; text-align: right; margin-top: 5px; }
        .loading-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.8); display: none;
            justify-content: center; align-items: center; z-index: 9999;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><?php echo $id ? '编辑新闻' : '添加新闻'; ?></h1>
            <a href="news.php" class="btn btn-secondary">
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
            <form method="POST" id="newsForm">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-edit"></i> 新闻内容</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label required">标题</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($news['title'] ?? ''); ?>" 
                                   required maxlength="255">
                            <div class="character-count" id="title-counter">0/255</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="summary" class="form-label">摘要</label>
                            <textarea class="form-control" id="summary" name="summary" rows="3" maxlength="500"><?php echo htmlspecialchars($news['summary'] ?? ''); ?></textarea>
                            <div class="character-count" id="summary-counter">0/500</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label required">内容</label>
                            <textarea class="form-control" id="content" name="content" rows="15" required><?php echo htmlspecialchars($news['content'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-cog"></i> 设置</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label required">分类</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">请选择分类</option>
                                        <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo ($news['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo $cat['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">状态</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="draft" <?php echo ($news['status'] ?? 'draft') == 'draft' ? 'selected' : ''; ?>>草稿</option>
                                        <option value="published" <?php echo ($news['status'] ?? '') == 'published' ? 'selected' : ''; ?>>已发布</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 保存新闻
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="history.back()">
                        <i class="fas fa-times"></i> 取消
                    </button>
                </div>
            </form>
        </div>

        <div class="loading-overlay" id="loadingOverlay">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">正在保存，请稍候...</p>
            </div>
        </div>
    </main>

    <!-- 本地jQuery -->
    <script src="../assets/js/jquery.min.js"></script>
    <!-- 本地Bootstrap JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <!-- 本地Summernote JS -->
    <script src="../assets/js/summernote-lite.min.js"></script>
    <!-- 本地Summernote中文语言包 -->
    <script src="../assets/js/summernote-zh-CN.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#content').summernote({
                lang: 'zh-CN',
                height: 400,
                placeholder: '请输入新闻内容...',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                fontNames: ['Arial', 'Microsoft YaHei', 'SimHei', 'SimSun', 'Times New Roman'],
                fontSizes: ['12', '14', '16', '18', '24'],
                callbacks: {
                    onImageUpload: function(files) {
                        var reader = new FileReader();
                        reader.onloadend = function() {
                            var image = $('<img>').attr('src', reader.result).css('max-width', '100%');
                            $('#content').summernote('insertNode', image[0]);
                        };
                        reader.readAsDataURL(files[0]);
                    }
                }
            });

            $('#title').on('input', function() {
                $('#title-counter').text($(this).val().length + '/255');
            });

            $('#summary').on('input', function() {
                $('#summary-counter').text($(this).val().length + '/500');
            });

            $('#title').trigger('input');
            $('#summary').trigger('input');

            $('#newsForm').on('submit', function() {
                $('#loadingOverlay').show();
            });
        });
    </script>
</body>
</html>