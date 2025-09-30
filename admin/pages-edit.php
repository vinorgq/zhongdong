<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$page = null;

if ($id) {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM pages WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $page = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $content = $_POST['content'] ?? '';
    $meta_title = $_POST['meta_title'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $status = $_POST['status'] ?? 'published';
    
    $pdo = getDBConnection();
    
    if ($id && $page) {
        $sql = "UPDATE pages SET title = ?, slug = ?, content = ?, meta_title = ?, meta_description = ?, status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$title, $slug, $content, $meta_title, $meta_description, $status, $id]);
        $message = $result ? "页面更新成功" : "页面更新失败";
    } else {
        $sql = "INSERT INTO pages (title, slug, content, meta_title, meta_description, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$title, $slug, $content, $meta_title, $meta_description, $status]);
        $message = $result ? "页面添加成功" : "页面添加失败";
    }
    
    if (isset($message) && strpos($message, '成功') !== false) {
        header("Location: pages.php?message=$message");
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
    <title><?php echo $id ? '编辑' : '创建'; ?>页面 - 众东科技后台</title>
    
    <!-- 本地Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 本地Font Awesome CSS -->
    <link href="../assets/css/fontawesome-fixed.css" rel="stylesheet">
    
    <!-- 本地Summernote CSS -->
    <link href="../assets/css/summernote-fixed.css" rel="stylesheet">
    <link href="../assets/css/summernote-local.css" rel="stylesheet">
    
    <!-- 后台统一样式 -->
    <link href="../assets/css/admin.css" rel="stylesheet">
    
    <style>
        .form-container { max-width: 100%; }
        .required:after { content: " *"; color: red; }
        .character-count { font-size: 12px; color: #6c757d; text-align: right; margin-top: 5px; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-file-alt text-warning"></i> <?php echo $id ? '编辑页面' : '创建页面'; ?>
            </h1>
            <a href="pages.php" class="btn btn-secondary">
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
            <form method="POST" id="pageForm">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> 页面信息
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label required">页面标题</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo htmlspecialchars($page['title'] ?? ''); ?>" 
                                           required maxlength="255">
                                    <div class="character-count" id="title-counter">0/255</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug" class="form-label required">标识符</label>
                                    <input type="text" class="form-control" id="slug" name="slug" 
                                           value="<?php echo htmlspecialchars($page['slug'] ?? ''); ?>" 
                                           required pattern="[a-z0-9-]+" placeholder="如: about, contact">
                                    <div class="form-text">用于URL的英文标识，只能包含小写字母、数字和连字符</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label required">页面内容</label>
                            <textarea class="form-control" id="content" name="content" rows="15" required><?php echo htmlspecialchars($page['content'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-search"></i> SEO设置
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta标题</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                   value="<?php echo htmlspecialchars($page['meta_title'] ?? ''); ?>" 
                                   maxlength="255" placeholder="页面在搜索引擎中显示的标题">
                            <div class="form-text">如果不填，将使用页面标题作为Meta标题</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta描述</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" 
                                      maxlength="500" placeholder="页面在搜索引擎中显示的描述"><?php echo htmlspecialchars($page['meta_description'] ?? ''); ?></textarea>
                            <div class="character-count" id="meta-description-counter">0/500</div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog"></i> 设置
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">页面状态</label>
                            <select class="form-select" id="status" name="status">
                                <option value="published" <?php echo ($page['status'] ?? 'published') == 'published' ? 'selected' : ''; ?>>已发布</option>
                                <option value="draft" <?php echo ($page['status'] ?? '') == 'draft' ? 'selected' : ''; ?>>草稿</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> 保存页面
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="history.back()">
                        <i class="fas fa-times"></i> 取消
                    </button>
                    <button type="button" class="btn btn-info" onclick="previewPage()">
                        <i class="fas fa-eye"></i> 预览
                    </button>
                </div>
            </form>
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
            // 初始化Summernote编辑器
            $('#content').summernote({
                lang: 'zh-CN',
                height: 400,
                placeholder: '请输入页面内容...',
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

            // 字符计数
            $('#title').on('input', function() {
                $('#title-counter').text($(this).val().length + '/255');
            });

            $('#meta_description').on('input', function() {
                $('#meta-description-counter').text($(this).val().length + '/500');
            });

            $('#title').trigger('input');
            $('#meta_description').trigger('input');
        });

        // 页面预览
        function previewPage() {
            var title = $('#title').val();
            var content = $('#content').summernote('code');
            
            if (!title || !content) {
                alert('请先填写标题和内容');
                return;
            }
            
            var previewWindow = window.open('', '_blank');
            previewWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>预览: ${title}</title>
                    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { padding: 20px; background: #f8f9fa; }
                        .preview-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
                    </style>
                </head>
                <body>
                    <div class="preview-container">
                        <h1>${title}</h1>
                        <hr>
                        <div>${content}</div>
                    </div>
                </body>
                </html>
            `);
            previewWindow.document.close();
        }
    </script>
</body>
</html>