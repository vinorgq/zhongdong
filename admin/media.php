<?php
session_start();

// 动态设置PHP配置（使用更保守的值）
@ini_set('upload_max_filesize', '5000M');
@ini_set('post_max_size', '5000M');
@ini_set('max_execution_time', '3000');
@ini_set('max_input_time', '3000');
@ini_set('memory_limit', '256M');

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 检查数据库连接
try {
    $pdo = getDBConnection();
    $pdo->query("SELECT 1"); // 测试连接
} catch (PDOException $e) {
    $error = "数据库连接失败: " . $e->getMessage();
}

// 处理文件上传
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $upload_result = uploadFile($_FILES['file']);
    if ($upload_result['success']) {
        $message = "文件上传成功 - " . $upload_result['original_name'];
        logAction('file_upload', '上传文件: ' . $upload_result['original_name']);
    } else {
        $error = $upload_result['message'];
    }
}

// 处理文件删除
if (isset($_GET['delete'])) {
    $filename = basename($_GET['delete']);
    if (deleteUploadedFile($filename)) {
        $message = "文件删除成功";
        logAction('file_delete', '删除文件: ' . $filename);
    } else {
        $error = "文件删除失败或文件不存在";
    }
    
    header('Location: media.php?message=' . urlencode($message ?? $error ?? ''));
    exit;
}

// 使用函数获取文件列表
$files = getAllUploadedFiles();

// 处理消息显示
if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>媒体管理 - 众东科技后台</title>
    
    <!-- 本地Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- 本地Font Awesome CSS -->
    <link href="../assets/css/fontawesome-fixed.css" rel="stylesheet">
    <!-- 后台统一样式 -->
    <link href="../assets/css/admin.css" rel="stylesheet">
    
    <!-- PDF.js 用于PDF预览 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    
    <style>
        .file-icon {
            font-size: 3rem;
        }
        .file-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .image-thumbnail {
            height: 150px;
            object-fit: cover;
        }
        .file-type-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .preview-modal iframe, .preview-modal video, .preview-modal audio {
            width: 100%;
            height: 500px;
        }
        .file-type-icon {
            width: 100%;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .file-type-image { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .file-type-document { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .file-type-video { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .file-type-audio { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .file-type-archive { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
        .file-type-other { background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: #333; }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        .upload-area.dragover {
            border-color: #0d6efd;
            background: #e7f1ff;
        }
        .file-info {
            font-size: 0.875rem;
        }
        .file-actions {
            opacity: 0;
            transition: opacity 0.3s;
        }
        .file-card:hover .file-actions {
            opacity: 1;
        }
        .preview-iframe {
            width: 100%;
            height: 600px;
            border: none;
            border-radius: 8px;
        }
        .local-preview-options .card {
            transition: transform 0.2s;
        }
        .local-preview-options .card:hover {
            transform: translateY(-5px);
        }
        .file-upload-time {
            font-size: 0.75rem;
            color: #6c757d;
        }
        .file-original-name {
            font-weight: 500;
        }
        .file-stats {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .file-type-filter {
            max-width: 200px;
        }
        .preview-tips {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .office-file-notice {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-images text-purple"></i> 媒体文件管理
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshPage()">
                        <i class="fas fa-sync-alt"></i> 刷新
                    </button>
                </div>
            </div>
        </div>

        <?php if (isset($message)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?php echo safeOutput($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i> <?php echo safeOutput($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- 文件统计信息 -->
        <div class="file-stats">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-primary"><?php echo count($files); ?></h4>
                        <small class="text-muted">总文件数</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-success"><?php echo count(array_filter($files, fn($file) => $file['file_type'] === 'image')); ?></h4>
                        <small class="text-muted">图片文件</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-info"><?php echo count(array_filter($files, fn($file) => $file['file_type'] === 'document')); ?></h4>
                        <small class="text-muted">文档文件</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-warning"><?php echo count(array_filter($files, fn($file) => $file['file_type'] === 'video')); ?></h4>
                        <small class="text-muted">视频文件</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 文件上传表单 -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-upload"></i> 上传文件
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="uploadForm">
                    <div class="upload-area mb-3" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h5>拖放文件到此处或点击选择</h5>
                        <p class="text-muted">支持图片、文档、视频、音频、压缩文件等，最大 5000MB</p>
                        <input type="file" class="form-control d-none" id="file" name="file" 
                               accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.7z,.mp4,.avi,.mov,.mp3,.wav,.ogg,.md,.json,.xml,.html,.css,.js" 
                               required>
                        <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('file').click()">
                            <i class="fas fa-folder-open"></i> 选择文件
                        </button>
                    </div>
                    
                    <div class="row d-none" id="fileInfoRow">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <span id="fileInfoText">已选择文件信息将显示在这里</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="custom_name" class="form-label">自定义文件名（可选）</label>
                                <input type="text" class="form-control" id="custom_name" name="custom_name" 
                                       placeholder="留空将使用原始文件名">
                                <div class="form-text">建议使用有意义的文件名，便于管理</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-success w-100" id="uploadBtn">
                                    <i class="fas fa-upload"></i> 开始上传
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress mb-3 d-none" id="uploadProgress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 0%">
                            <span class="progress-text">0%</span>
                        </div>
                    </div>
                    <div id="uploadResult" class="d-none"></div>
                </form>
            </div>
        </div>

        <!-- 文件列表 -->
        <div class="card shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> 文件列表
                    <span class="badge bg-secondary ms-2"><?php echo count($files); ?> 个文件</span>
                    <div class="float-end">
                        <select class="form-select form-select-sm file-type-filter" id="filterType">
                            <option value="all">所有类型</option>
                            <option value="image">图片文件</option>
                            <option value="document">文档文件</option>
                            <option value="video">视频文件</option>
                            <option value="audio">音频文件</option>
                            <option value="archive">压缩文件</option>
                            <option value="other">其他文件</option>
                        </select>
                    </div>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($files)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-folder-open fa-4x mb-3"></i>
                        <h5>暂无文件</h5>
                        <p>上传您的第一个文件</p>
                    </div>
                <?php else: ?>
                    <div class="row" id="fileGrid">
                        <?php foreach($files as $file): 
                            $preview_type = getFilePreviewType($file['name']);
                            $is_previewable = isFilePreviewable($file['name']);
                            $preview_info = getFilePreviewInfo($file['url'], $file['name']);
                        ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4 file-item" data-type="<?php echo $file['file_type']; ?>">
                            <div class="card file-card h-100">
                                <?php if ($file['file_type'] == 'image'): ?>
                                    <img src="<?php echo safeOutput($file['url']); ?>" 
                                         class="card-img-top image-thumbnail" 
                                         alt="<?php echo safeOutput($file['original_name']); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="card-img-top file-type-icon file-type-<?php echo $file['file_type']; ?>">
                                        <i class="fas <?php echo $file['icon']; ?> fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <span class="badge bg-secondary file-type-badge">
                                    <?php 
                                    $type_names = [
                                        'image' => '图片',
                                        'document' => '文档',
                                        'video' => '视频', 
                                        'audio' => '音频',
                                        'archive' => '压缩包',
                                        'other' => '其他'
                                    ];
                                    echo $type_names[$file['file_type']];
                                    ?>
                                </span>
                                
                                <div class="card-body">
                                    <h6 class="card-title file-original-name text-truncate" title="<?php echo safeOutput($file['original_name']); ?>">
                                        <?php echo safeOutput($file['original_name']); ?>
                                    </h6>
                                    <p class="card-text small text-muted mb-1">
                                        <i class="fas fa-hdd"></i> <?php echo formatFileSize($file['size']); ?>
                                    </p>
                                    <p class="card-text small text-muted mb-1">
                                        <i class="fas fa-expand-arrows-alt"></i> <?php echo strtoupper($file['extension']); ?> 格式
                                    </p>
                                    <p class="card-text file-upload-time mb-2">
                                        <i class="fas fa-calendar"></i> <?php echo $file['upload_date']; ?>
                                    </p>
                                    <?php if ($is_previewable): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-eye"></i> 可预览
                                    </span>
                                    <?php elseif (in_array($file['extension'], ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'])): ?>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-download"></i> 需下载查看
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-download"></i> 下载查看
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent file-actions">
                                    <div class="btn-group w-100">
                                        <?php if ($is_previewable): ?>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="previewFile('<?php echo safeOutput($file['url']); ?>', 
                                                                    '<?php echo safeOutput($file['type']); ?>', 
                                                                    '<?php echo safeOutput($file['original_name']); ?>',
                                                                    '<?php echo $preview_type; ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="previewFile('<?php echo safeOutput($file['url']); ?>', 
                                                                    '<?php echo safeOutput($file['type']); ?>', 
                                                                    '<?php echo safeOutput($file['original_name']); ?>',
                                                                    '<?php echo $preview_type; ?>')">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="copyUrl('<?php echo safeOutput($file['url']); ?>')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <a href="media.php?delete=<?php echo safeOutput($file['name']); ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('确定删除文件 &quot;<?php echo safeOutput($file['original_name']); ?>&quot; 吗？此操作不可恢复！')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- 文件预览模态框 -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">
                        <i class="fas fa-eye"></i> 文件预览 - <span id="previewFileName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- 预览内容将在这里动态加载 -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> 关闭
                    </button>
                    <button type="button" class="btn btn-primary" id="downloadBtn">
                        <i class="fas fa-download"></i> 下载文件
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 本地jQuery -->
    <script src="../assets/js/jquery.min.js"></script>
    <!-- 本地Bootstrap JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentPreviewUrl = '';
        let currentFileName = '';
        
        // 页面刷新
        function refreshPage() {
            window.location.reload();
        }
        
        // 复制文件URL
        function copyUrl(url) {
            navigator.clipboard.writeText(url).then(function() {
                showAlert('文件链接已复制到剪贴板', 'success');
            }).catch(function(err) {
                showAlert('复制失败: ' + err, 'danger');
            });
        }
        
        // 文件预览
        function previewFile(url, mimeType, fileName, previewType) {
            currentPreviewUrl = url;
            currentFileName = fileName;
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            const previewContent = document.getElementById('previewContent');
            const downloadBtn = document.getElementById('downloadBtn');
            const previewFileName = document.getElementById('previewFileName');
            
            // 设置文件名和下载按钮
            previewFileName.textContent = fileName;
            downloadBtn.onclick = function() {
                window.open(url, '_blank');
            };
            
            // 显示加载中
            previewContent.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">加载中...</span></div><p class="mt-2">正在准备预览...</p></div>';
            
            // 根据文件类型处理
            switch(previewType) {
                case 'image':
                    previewImage(url);
                    break;
                case 'pdf':
                    previewPDF(url);
                    break;
                case 'text':
                    previewText(url);
                    break;
                case 'video':
                    previewVideo(url, mimeType);
                    break;
                case 'audio':
                    previewAudio(url, mimeType);
                    break;
                case 'download':
                    showFileInfo(url, fileName, mimeType);
                    break;
                default:
                    showFileInfo(url, fileName, mimeType);
                    break;
            }
            
            modal.show();
        }
        
        // 显示文件信息（用于不支持预览的文件）
        function showFileInfo(url, fileName, mimeType) {
            const previewContent = document.getElementById('previewContent');
            const extension = fileName.split('.').pop().toLowerCase();
            
            let fileTypeDescription = '';
            let icon = 'fas fa-file';
            
            // 根据文件类型提供不同的描述
            switch(extension) {
                case 'doc':
                case 'docx':
                    fileTypeDescription = 'Word文档';
                    icon = 'fas fa-file-word';
                    break;
                case 'xls':
                case 'xlsx':
                    fileTypeDescription = 'Excel表格';
                    icon = 'fas fa-file-excel';
                    break;
                case 'ppt':
                case 'pptx':
                    fileTypeDescription = 'PowerPoint演示文稿';
                    icon = 'fas fa-file-powerpoint';
                    break;
                case 'zip':
                case 'rar':
                case '7z':
                    fileTypeDescription = '压缩文件';
                    icon = 'fas fa-file-archive';
                    break;
                default:
                    fileTypeDescription = '文件';
                    icon = 'fas fa-file';
            }
            
            previewContent.innerHTML = '<div class="text-center py-5">' +
                '<i class="' + icon + ' fa-5x text-primary mb-4"></i>' +
                '<h4>' + escapeHtml(fileName) + '</h4>' +
                '<div class="office-file-notice mt-4">' +
                '<h5><i class="fas fa-info-circle"></i> 文件信息</h5>' +
                '<div class="text-start mt-3">' +
                '<p><strong>文件类型:</strong> ' + fileTypeDescription + '</p>' +
                '<p><strong>MIME类型:</strong> ' + mimeType + '</p>' +
                '<p><strong>文件格式:</strong> .' + extension.toUpperCase() + '</p>' +
                '</div>' +
                '</div>' +
                '<div class="preview-tips mt-4">' +
                '<h6><i class="fas fa-lightbulb"></i> 查看说明</h6>' +
                '<p>该文件类型需要在本地使用相应的软件打开。</p>' +
                '<p>请下载文件后使用合适的应用程序查看。</p>' +
                '</div>' +
                '<div class="mt-4">' +
                '<a href="' + url + '" target="_blank" class="btn btn-primary btn-lg">' +
                '<i class="fas fa-download"></i> 下载文件' +
                '</a>' +
                '</div>' +
                '</div>';
        }
        
        // 图片预览
        function previewImage(url) {
            const previewContent = document.getElementById('previewContent');
            const img = new Image();
            img.onload = function() {
                previewContent.innerHTML = '<div class="text-center">' +
                    '<img src="' + url + '" class="img-fluid" alt="预览" style="max-height: 70vh;">' +
                    '<p class="text-muted mt-3">图片预览 - 支持缩放</p>' +
                    '</div>';
            };
            img.onerror = function() {
                previewContent.innerHTML = '<div class="text-center py-5">' +
                    '<i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>' +
                    '<p class="text-danger">图片加载失败</p>' +
                    '<p>请尝试下载后查看</p>' +
                    '</div>';
            };
            img.src = url;
        }
        
        // PDF预览
        function previewPDF(url) {
            const previewContent = document.getElementById('previewContent');
            
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
            
            previewContent.innerHTML = '<div class="text-center py-3">' +
                '<div class="spinner-border text-primary" role="status">' +
                '<span class="visually-hidden">加载中...</span>' +
                '</div>' +
                '<p class="mt-2">正在加载PDF文档...</p>' +
                '</div>';
            
            pdfjsLib.getDocument(url).promise.then(function(pdf) {
                pdf.getPage(1).then(function(page) {
                    const scale = 1.5;
                    const viewport = page.getViewport({ scale: scale });
                    
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    
                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    
                    page.render(renderContext).promise.then(function() {
                        previewContent.innerHTML = '<div class="text-center">' +
                            '<canvas class="border rounded"></canvas>' +
                            '<p class="text-muted mt-3">第 1 页，共 ' + pdf.numPages + ' 页 - PDF预览</p>' +
                            '<div class="alert alert-info mt-3">' +
                            '<i class="fas fa-info-circle"></i> ' +
                            'PDF预览只显示第一页，请下载完整文件查看所有内容' +
                            '</div>' +
                            '</div>';
                        previewContent.querySelector('canvas').replaceWith(canvas);
                    });
                });
            }).catch(function(error) {
                previewContent.innerHTML = '<div class="text-center py-5">' +
                    '<i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>' +
                    '<p class="text-danger">PDF加载失败: ' + error.message + '</p>' +
                    '<p>请尝试下载后查看</p>' +
                    '</div>';
            });
        }
        
        // 文本文件预览
        function previewText(url) {
            const previewContent = document.getElementById('previewContent');
            
            previewContent.innerHTML = '<div class="text-center py-3">' +
                '<div class="spinner-border text-primary" role="status">' +
                '<span class="visually-hidden">加载中...</span>' +
                '</div>' +
                '<p class="mt-2">正在加载文本内容...</p>' +
                '</div>';
            
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('网络响应不正常');
                    return response.text();
                })
                .then(text => {
                    if (text.length > 100000) {
                        text = text.substring(0, 100000) + '\n\n... (文件过大，已截断显示)';
                    }
                    previewContent.innerHTML = '<div class="text-start">' +
                        '<h5 class="mb-3"><i class="fas fa-file-alt"></i> 文本内容预览</h5>' +
                        '<pre class="p-3 bg-light border rounded" style="max-height: 400px; overflow: auto; white-space: pre-wrap; font-family: \'Courier New\', monospace; font-size: 14px;">' + escapeHtml(text) + '</pre>' +
                        '<p class="text-muted mt-2">字符数: ' + text.length + '</p>' +
                        '</div>';
                })
                .catch(error => {
                    previewContent.innerHTML = '<div class="text-center py-5">' +
                        '<i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>' +
                        '<p class="text-danger">无法加载文件: ' + error.message + '</p>' +
                        '<p>请尝试下载后查看</p>' +
                        '</div>';
                });
        }
        
        // 视频预览
        function previewVideo(url, mimeType) {
            const previewContent = document.getElementById('previewContent');
            previewContent.innerHTML = '<div class="text-center">' +
                '<h5 class="mb-3"><i class="fas fa-video"></i> 视频播放</h5>' +
                '<video controls class="w-100" style="max-height: 70vh;">' +
                '<source src="' + url + '" type="' + mimeType + '">' +
                '您的浏览器不支持视频播放' +
                '</video>' +
                '<div class="mt-3 text-center">' +
                '<p class="text-muted">如果视频无法播放，请下载后使用本地播放器查看</p>' +
                '</div>' +
                '</div>';
        }
        
        // 音频预览
        function previewAudio(url, mimeType) {
            const previewContent = document.getElementById('previewContent');
            previewContent.innerHTML = '<div class="text-center py-4">' +
                '<h5 class="mb-4"><i class="fas fa-music"></i> 音频播放</h5>' +
                '<i class="fas fa-music fa-4x text-primary mb-4"></i>' +
                '<audio controls class="w-100">' +
                '<source src="' + url + '" type="' + mimeType + '">' +
                '您的浏览器不支持音频播放' +
                '</audio>' +
                '<div class="mt-4 text-center">' +
                '<p class="text-muted">如果音频无法播放，请下载后使用本地播放器查看</p>' +
                '</div>' +
                '</div>';
        }
        
        // HTML转义函数
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // 显示提示信息
        function showAlert(message, type) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-' + type + ' alert-dismissible fade show position-fixed';
            alert.style.top = '20px';
            alert.style.right = '20px';
            alert.style.zIndex = '9999';
            alert.style.minWidth = '300px';
            alert.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle') + '"></i> ' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            document.body.appendChild(alert);
            
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }
        
        // 文件类型过滤
        $(document).ready(function() {
            // 文件类型过滤
            $('#filterType').on('change', function() {
                const filter = $(this).val();
                $('.file-item').each(function() {
                    if (filter === 'all' || $(this).data('type') === filter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
            
            // 拖放上传功能
            const uploadArea = $('#uploadArea');
            const fileInput = $('#file');
            
            uploadArea.on('dragover', function(e) {
                e.preventDefault();
                uploadArea.addClass('dragover');
            });
            
            uploadArea.on('dragleave', function(e) {
                e.preventDefault();
                uploadArea.removeClass('dragover');
            });
            
            uploadArea.on('drop', function(e) {
                e.preventDefault();
                uploadArea.removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    fileInput[0].files = files;
                    handleFileSelect(files[0]);
                }
            });
            
            uploadArea.on('click', function() {
                fileInput.click();
            });
            
            fileInput.on('change', function(e) {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });
            
            function handleFileSelect(file) {
                if (file) {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    if (fileSize > 5000) {
                        showAlert('文件大小不能超过 5000MB', 'danger');
                        fileInput.val('');
                        $('#fileInfoRow').addClass('d-none');
                        return false;
                    }
                    
                    const fileInfo = '已选择: ' + file.name + ' (' + fileSize + ' MB) - ' + 
                                   '类型: ' + file.type + ' - ' +
                                   '最后修改: ' + new Date(file.lastModified).toLocaleString();
                    $('#fileInfoText').html(fileInfo);
                    $('#fileInfoRow').removeClass('d-none');
                    
                    const fileNameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
                    $('#custom_name').val(fileNameWithoutExt);
                }
            }
            
            // 文件上传处理
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                
                const fileInput = $('#file')[0];
                if (!fileInput.files.length) {
                    showAlert('请选择要上传的文件', 'warning');
                    return;
                }
                
                const submitBtn = $('#uploadBtn');
                const originalText = submitBtn.html();
                const progressBar = $('#uploadProgress .progress-bar');
                const progressText = $('.progress-text');
                const progressContainer = $('#uploadProgress');
                const uploadResult = $('#uploadResult');
                
                uploadResult.removeClass('alert-success alert-danger').addClass('d-none').html('');
                
                progressContainer.removeClass('d-none');
                submitBtn.html('<span class="spinner-border spinner-border-sm" role="status"></span> 上传中...');
                submitBtn.prop('disabled', true);
                
                progressBar.css('width', '0%');
                progressBar.removeClass('bg-success bg-danger').addClass('bg-primary');
                progressText.text('0%');
                
                const formData = new FormData(this);
                
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new XMLHttpRequest();
                        
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percentComplete = (e.loaded / e.total) * 100;
                                const percentRounded = Math.round(percentComplete);
                                
                                progressBar.css('width', percentComplete + '%');
                                progressText.text(percentRounded + '%');
                                
                                if (percentRounded < 100) {
                                    submitBtn.html('<span class="spinner-border spinner-border-sm" role="status"></span> ' + percentRounded + '%');
                                }
                            }
                        }, false);
                        
                        return xhr;
                    },
                    success: function(response) {
                        setTimeout(function() {
                            progressBar.css('width', '100%');
                            progressText.text('100%');
                            progressBar.removeClass('bg-primary').addClass('bg-success');
                            submitBtn.html('<i class="fas fa-check"></i> 上传完成');
                            
                            showAlert('文件上传成功，页面即将刷新...', 'success');
                            
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        handleUploadError('上传失败: ' + error);
                    }
                });
                
                function handleUploadError(message) {
                    progressBar.removeClass('bg-primary bg-success').addClass('bg-danger');
                    progressText.text('上传失败');
                    submitBtn.html('<i class="fas fa-times"></i> 上传失败');
                    submitBtn.prop('disabled', false);
                    
                    showAlert(message, 'danger');
                }
            });
        });
    </script>
</body>
</html>