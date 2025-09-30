<?php
// 使用绝对路径引入 database.php
$config_file = dirname(__DIR__) . '/config/database.php';
if (file_exists($config_file)) {
    require_once $config_file;
} else {
    // 如果文件不存在，显示安装页面
    if (strpos($_SERVER['PHP_SELF'], 'admin') !== false) {
        header('Location: ../install.php');
    } else {
        header('Location: install.php');
    }
    exit;
}

/**
 * 获取项目根目录
 */
function getProjectRoot(): string {
    return '/www/wwwroot/47.93.138.227/';
}

/**
 * 安全的目录创建函数
 */
function createDirectorySafe(string $dir): bool {
    if (file_exists($dir)) {
        return is_writable($dir);
    }
    
    // 尝试创建目录
    if (@mkdir($dir, 0755, true)) {
        return true;
    }
    
    // 如果创建失败，尝试使用更宽松的权限
    if (@mkdir($dir, 0777, true)) {
        @chmod($dir, 0755);
        return true;
    }
    
    return false;
}

/**
 * 获取最新新闻
 */
function getLatestNews(int $limit = 5): array {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT n.*, c.name as category_name 
                FROM news n 
                LEFT JOIN categories c ON n.category_id = c.id 
                WHERE n.status = 'published' 
                ORDER BY n.published_at DESC 
                LIMIT ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        error_log("获取最新新闻失败: " . $e->getMessage());
        return [];
    }
}

/**
 * 获取最近新闻（后台用）
 */
function getRecentNews(int $limit = 5): array {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT * FROM news ORDER BY created_at DESC LIMIT ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        error_log("获取最近新闻失败: " . $e->getMessage());
        return [];
    }
}

/**
 * 获取分类名称
 */
function getCategoryName(?int $category_id): string {
    if (!$category_id) return '未分类';
    
    try {
        $pdo = getDBConnection();
        $sql = "SELECT name FROM categories WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$category_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['name'] : '未分类';
    } catch (PDOException $e) {
        error_log("获取分类名称失败: " . $e->getMessage());
        return '未分类';
    }
}

/**
 * 获取记录数量
 */
function getRecordCount(string $table): int {
    // 防止SQL注入，验证表名
    $allowed_tables = ['news', 'categories', 'users', 'products', 'pages'];
    if (!in_array($table, $allowed_tables)) {
        return 0;
    }
    
    try {
        $pdo = getDBConnection();
        $sql = "SELECT COUNT(*) as count FROM `$table`";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    } catch (PDOException $e) {
        error_log("获取记录数量失败: " . $e->getMessage());
        return 0;
    }
}

/**
 * 获取所有分类
 */
function getAllCategories(?string $type = null): array {
    try {
        $pdo = getDBConnection();
        if ($type) {
            $sql = "SELECT * FROM categories WHERE type = ? ORDER BY name";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$type]);
        } else {
            $sql = "SELECT * FROM categories ORDER BY name";
            $stmt = $pdo->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        error_log("获取所有分类失败: " . $e->getMessage());
        return [];
    }
}

/**
 * 用户登录验证
 */
function loginUser(string $username, string $password) {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // 登录成功后更新最后登录时间
            $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([$user['id']]);
            
            return $user;
        }
        return false;
    } catch (PDOException $e) {
        error_log("用户登录验证失败: " . $e->getMessage());
        return false;
    }
}

/**
 * 获取页面内容
 */
function getPageContent(string $slug): ?array {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT * FROM pages WHERE slug = ? AND status = 'published'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    } catch (PDOException $e) {
        error_log("获取页面内容失败: " . $e->getMessage());
        return null;
    }
}

/**
 * 获取所有产品
 */
function getAllProducts(): array {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'published' 
                ORDER BY p.created_at DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        error_log("获取所有产品失败: " . $e->getMessage());
        return [];
    }
}

/**
 * 获取新闻详情
 */
function getNewsById(int $id): ?array {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT n.*, c.name as category_name, u.username as author_name 
                FROM news n 
                LEFT JOIN categories c ON n.category_id = c.id 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    } catch (PDOException $e) {
        error_log("获取新闻详情失败: " . $e->getMessage());
        return null;
    }
}

/**
 * 获取带分页的新闻
 */
function getNewsWithPagination(?int $category_id = null, ?int $limit = null, int $offset = 0): array {
    try {
        $pdo = getDBConnection();
        
        $sql = "SELECT n.*, c.name as category_name 
                FROM news n 
                LEFT JOIN categories c ON n.category_id = c.id 
                WHERE n.status = 'published'";
        
        $params = [];
        $param_types = [];
        
        if ($category_id) {
            $sql .= " AND n.category_id = ?";
            $params[] = $category_id;
            $param_types[] = PDO::PARAM_INT;
        }
        
        $sql .= " ORDER BY n.published_at DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $param_types[] = PDO::PARAM_INT;
            $param_types[] = PDO::PARAM_INT;
        }
        
        $stmt = $pdo->prepare($sql);
        
        // 绑定参数
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value, $param_types[$key] ?? PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        error_log("获取分页新闻失败: " . $e->getMessage());
        return [];
    }
}

/**
 * 获取新闻总数（用于分页）
 */
function getNewsTotalCount(?int $category_id = null): int {
    try {
        $pdo = getDBConnection();
        
        $sql = "SELECT COUNT(*) as total FROM news WHERE status = 'published'";
        $params = [];
        
        if ($category_id) {
            $sql .= " AND category_id = ?";
            $params[] = $category_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    } catch (PDOException $e) {
        error_log("获取新闻总数失败: " . $e->getMessage());
        return 0;
    }
}

/**
 * 文件上传处理（保留原文件名）
 */
function uploadFile(array $file, string $target_dir = null): array {
    // 使用绝对路径
    if ($target_dir === null) {
        $target_dir = getProjectRoot() . 'assets/uploads/';
    }
    
    // 检查上传错误
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => getUploadErrorMessage($file['error'])];
    }
    
    // 确保上传目录存在
    if (!file_exists($target_dir)) {
        if (!createDirectorySafe($target_dir)) {
            return ['success' => false, 'message' => '无法创建上传目录'];
        }
    }
    
    $original_name = basename($file["name"]);
    $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $file_name_without_ext = pathinfo($original_name, PATHINFO_FILENAME);
    
    // 创建分类目录
    $file_type = getFileType($original_name);
    $type_dirs = [
        'image' => 'images/',
        'document' => 'documents/',
        'video' => 'videos/',
        'audio' => 'audios/',
        'archive' => 'archives/',
        'other' => 'others/'
    ];
    
    $type_dir = $type_dirs[$file_type] ?? 'others/';
    $final_target_dir = $target_dir . $type_dir;
    
    if (!file_exists($final_target_dir)) {
        if (!createDirectorySafe($final_target_dir)) {
            return ['success' => false, 'message' => '无法创建分类目录'];
        }
    }
    
    // 允许的文件类型
    $allowed_types = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'],
        'document' => ['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'rtf', 'md'],
        'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', '3gp', 'mpeg', 'mpg'],
        'audio' => ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma'],
        'archive' => ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz']
    ];
    
    // 检查文件类型是否允许
    $allowed = false;
    foreach ($allowed_types as $type => $extensions) {
        if (in_array($file_extension, $extensions)) {
            $allowed = true;
            break;
        }
    }
    
    if (!$allowed) {
        return ['success' => false, 'message' => '不支持的文件类型: .' . $file_extension];
    }
    
    // 检查文件大小 (限制为5000MB)
    $max_size = 5000 * 1024 * 1024;
    if ($file["size"] > $max_size) {
        return ['success' => false, 'message' => '文件太大，请上传小于5000MB的文件'];
    }
    
    // 安全检查
    if (!$file["tmp_name"] || !is_uploaded_file($file["tmp_name"])) {
        return ['success' => false, 'message' => '文件上传失败'];
    }
    
    // 额外的图片安全检查
    if ($file_type === 'image') {
        $image_info = getimagesize($file["tmp_name"]);
        if (!$image_info) {
            return ['success' => false, 'message' => '无效的图片文件'];
        }
    }
    
    // 处理文件名冲突 - 保留原文件名，冲突时添加序号
    $target_filename = $original_name;
    $counter = 1;
    while (file_exists($final_target_dir . $target_filename)) {
        $target_filename = $file_name_without_ext . '_' . $counter . '.' . $file_extension;
        $counter++;
    }
    
    $target_path = $final_target_dir . $target_filename;
    
    if (move_uploaded_file($file["tmp_name"], $target_path)) {
        // 设置文件权限
        chmod($target_path, 0644);
        
        // 获取文件上传时间
        $upload_time = time();
        
        return [
            'success' => true, 
            'filename' => $target_filename,
            'original_name' => $original_name,
            'path' => $target_path,
            'url' => '../assets/uploads/' . $type_dir . $target_filename,
            'type' => $file_type,
            'file_extension' => $file_extension,
            'size' => $file['size'],
            'upload_time' => $upload_time,
            'upload_date' => date('Y-m-d H:i:s', $upload_time)
        ];
    } else {
        return ['success' => false, 'message' => '文件上传失败'];
    }
}

/**
 * 获取上传错误信息
 */
function getUploadErrorMessage(int $error_code): string {
    $error_messages = [
        UPLOAD_ERR_INI_SIZE => '上传的文件超过了 php.ini 中 upload_max_filesize 指令限制的大小',
        UPLOAD_ERR_FORM_SIZE => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
        UPLOAD_ERR_PARTIAL => '文件只有部分被上传',
        UPLOAD_ERR_NO_FILE => '没有文件被上传',
        UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹',
        UPLOAD_ERR_CANT_WRITE => '文件写入失败',
        UPLOAD_ERR_EXTENSION => 'PHP 扩展程序停止了文件上传'
    ];
    
    return $error_messages[$error_code] ?? '未知上传错误';
}

/**
 * 获取文件类型
 */
function getFileType(string $filename): string {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'];
    $document_extensions = ['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'rtf', 'md'];
    $video_extensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', '3gp', 'mpeg', 'mpg'];
    $audio_extensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma'];
    $archive_extensions = ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz'];
    
    if (in_array($extension, $image_extensions)) return 'image';
    if (in_array($extension, $document_extensions)) return 'document';
    if (in_array($extension, $video_extensions)) return 'video';
    if (in_array($extension, $audio_extensions)) return 'audio';
    if (in_array($extension, $archive_extensions)) return 'archive';
    
    return 'other';
}

/**
 * 获取文件图标类名
 */
function getFileIcon(string $filename): string {
    $type = getFileType($filename);
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $icons = [
        'image' => 'fa-file-image',
        'document' => 'fa-file-alt',
        'video' => 'fa-file-video',
        'audio' => 'fa-file-audio',
        'archive' => 'fa-file-archive',
        'other' => 'fa-file'
    ];
    
    // 特定文件类型的图标
    $specific_icons = [
        'pdf' => 'fa-file-pdf',
        'doc' => 'fa-file-word',
        'docx' => 'fa-file-word',
        'ppt' => 'fa-file-powerpoint',
        'pptx' => 'fa-file-powerpoint',
        'xls' => 'fa-file-excel',
        'xlsx' => 'fa-file-excel',
        'csv' => 'fa-file-csv',
        'txt' => 'fa-file-text',
        'rtf' => 'fa-file-text',
        'md' => 'fa-file-text',
        'zip' => 'fa-file-archive',
        'rar' => 'fa-file-archive',
        '7z' => 'fa-file-archive',
        'tar' => 'fa-file-archive',
        'gz' => 'fa-file-archive',
        'mp3' => 'fa-file-audio',
        'wav' => 'fa-file-audio',
        'ogg' => 'fa-file-audio',
        'm4a' => 'fa-file-audio',
        'flac' => 'fa-file-audio',
        'mp4' => 'fa-file-video',
        'avi' => 'fa-file-video',
        'mov' => 'fa-file-video',
        'wmv' => 'fa-file-video',
        'svg' => 'fa-file-image',
        'ico' => 'fa-file-image'
    ];
    
    return $specific_icons[$extension] ?? $icons[$type];
}

/**
 * 判断文件是否可在线预览
 */
function isFilePreviewable(string $filename): bool {
    $previewable_extensions = [
        // 图片
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg',
        // 文档
        'pdf', 'txt', 'md',
        // 文本文件
        'html', 'htm', 'css', 'js', 'json', 'xml', 'php',
        // 视频
        'mp4', 'webm', 'avi', 'mov', 'wmv', 'flv', 'mkv',
        // 音频
        'mp3', 'wav', 'ogg', 'm4a', 'aac'
    ];
    
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, $previewable_extensions);
}

/**
 * 获取文件预览类型
 */
function getFilePreviewType(string $filename): string {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $preview_types = [
        // 图片
        'jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 
        'gif' => 'image', 'bmp' => 'image', 'webp' => 'image', 'svg' => 'image',
        // PDF
        'pdf' => 'pdf',
        // 文本
        'txt' => 'text', 'csv' => 'text', 'html' => 'text', 'htm' => 'text',
        'css' => 'text', 'js' => 'text', 'json' => 'text', 'xml' => 'text', 'md' => 'text', 'php' => 'text',
        // Office 文档 - 直接下载
        'doc' => 'download', 'docx' => 'download', 'ppt' => 'download', 
        'pptx' => 'download', 'xls' => 'download', 'xlsx' => 'download',
        // 视频
        'mp4' => 'video', 'webm' => 'video', 'avi' => 'video', 
        'mov' => 'video', 'wmv' => 'video', 'flv' => 'video', 'mkv' => 'video',
        // 音频
        'mp3' => 'audio', 'wav' => 'audio', 'ogg' => 'audio', 
        'm4a' => 'audio', 'aac' => 'audio'
    ];
    
    return $preview_types[$extension] ?? 'download';
}

/**
 * 获取文件的MIME类型
 */
function getFileMimeType(string $filename): string {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $mime_types = [
        // 图片
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        
        // 文档
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'txt' => 'text/plain',
        'rtf' => 'application/rtf',
        'md' => 'text/markdown',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'csv' => 'text/csv',
        
        // 视频
        'mp4' => 'video/mp4',
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'wmv' => 'video/x-ms-wmv',
        'flv' => 'video/x-flv',
        'webm' => 'video/webm',
        'mkv' => 'video/x-matroska',
        '3gp' => 'video/3gpp',
        'mpeg' => 'video/mpeg',
        
        // 音频
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'm4a' => 'audio/mp4',
        'aac' => 'audio/aac',
        'flac' => 'audio/flac',
        'wma' => 'audio/x-ms-wma',
        
        // 压缩包
        'zip' => 'application/zip',
        'rar' => 'application/vnd.rar',
        '7z' => 'application/x-7z-compressed',
        'tar' => 'application/x-tar',
        'gz' => 'application/gzip',
        'bz2' => 'application/x-bzip2',
        'xz' => 'application/x-xz'
    ];
    
    return $mime_types[$extension] ?? 'application/octet-stream';
}

/**
 * 获取所有上传的文件
 */
function getAllUploadedFiles(): array {
    $upload_dir = getProjectRoot() . 'assets/uploads/';
    $sub_dirs = ['images/', 'documents/', 'videos/', 'audios/', 'archives/', 'others/'];
    $files = [];
    
    foreach ($sub_dirs as $sub_dir) {
        $current_dir = $upload_dir . $sub_dir;
        if (is_dir($current_dir)) {
            $file_list = scandir($current_dir);
            foreach ($file_list as $file) {
                if ($file != '.' && $file != '..') {
                    $filepath = $current_dir . $file;
                    if (file_exists($filepath)) {
                        $file_type = getFileType($file);
                        $files[] = [
                            'name' => $file,
                            'original_name' => $file,
                            'size' => filesize($filepath),
                            'type' => getFileMimeType($file),
                            'file_type' => $file_type,
                            'modified' => filemtime($filepath),
                            'upload_time' => filemtime($filepath),
                            'upload_date' => date('Y-m-d H:i:s', filemtime($filepath)),
                            'url' => '../assets/uploads/' . $sub_dir . $file,
                            'icon' => getFileIcon($file),
                            'previewable' => isFilePreviewable($file),
                            'extension' => pathinfo($file, PATHINFO_EXTENSION),
                            'preview_type' => getFilePreviewType($file)
                        ];
                    }
                }
            }
        }
    }
    
    // 按修改时间排序（最新的在前面）
    usort($files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
    
    return $files;
}

/**
 * 删除上传的文件
 */
function deleteUploadedFile(string $filename): bool {
    $upload_dir = getProjectRoot() . 'assets/uploads/';
    $sub_dirs = ['images/', 'documents/', 'videos/', 'audios/', 'archives/', 'others/'];
    
    foreach ($sub_dirs as $sub_dir) {
        $filepath = $upload_dir . $sub_dir . $filename;
        if (file_exists($filepath)) {
            if (unlink($filepath)) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * 格式化文件大小
 */
function formatFileSize(int $bytes): string {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * 安全输出函数，防止XSS攻击
 */
function safeOutput(?string $data): string {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * 生成分页HTML
 */
function generatePagination(int $current_page, int $total_pages, string $base_url): string {
    if ($total_pages <= 1) return '';
    
    $pagination = '<nav aria-label="Page navigation"><ul class="pagination">';
    
    // 上一页
    if ($current_page > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . ($current_page - 1) . '">上一页</a></li>';
    }
    
    // 显示页码范围（最多显示7个页码）
    $start_page = max(1, $current_page - 3);
    $end_page = min($total_pages, $start_page + 6);
    
    if ($end_page - $start_page < 6) {
        $start_page = max(1, $end_page - 6);
    }
    
    // 第一页和省略号
    if ($start_page > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . '1">1</a></li>';
        if ($start_page > 2) {
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // 页码
    for ($i = $start_page; $i <= $end_page; $i++) {
        $active = $i == $current_page ? ' active' : '';
        $pagination .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $base_url . $i . '">' . $i . '</a></li>';
    }
    
    // 最后一页和省略号
    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . $total_pages . '">' . $total_pages . '</a></li>';
    }
    
    // 下一页
    if ($current_page < $total_pages) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . ($current_page + 1) . '">下一页</a></li>';
    }
    
    $pagination .= '</ul></nav>';
    return $pagination;
}

/**
 * 获取所有新闻（兼容旧代码，建议使用 getNewsWithPagination）
 */
function getAllNews(?int $category_id = null, ?int $limit = null): array {
    return getNewsWithPagination($category_id, $limit, 0);
}

/**
 * 记录操作日志
 */
function logAction(string $action, string $description = ''): bool {
    try {
        $pdo = getDBConnection();
        $user_id = $_SESSION['user_id'] ?? 0;
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $sql = "INSERT INTO action_logs (user_id, action, description, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$user_id, $action, $description, $ip_address, $user_agent]);
    } catch (PDOException $e) {
        error_log("记录操作日志失败: " . $e->getMessage());
        return false;
    }
}

/**
 * 生成随机字符串
 */
function generateRandomString(int $length = 10): string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * 验证电子邮件格式
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * 获取文件扩展名
 */
function getFileExtension(string $filename): string {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * 获取客户端IP地址
 */
function getClientIp(): string {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * 格式化日期时间
 */
function formatDateTime(string $datetime, string $format = 'Y-m-d H:i:s'): string {
    try {
        $date = new DateTime($datetime);
        return $date->format($format);
    } catch (Exception $e) {
        return $datetime;
    }
}

/**
 * 截断字符串
 */
function truncateString(string $string, int $length = 100, string $suffix = '...'): string {
    if (mb_strlen($string) <= $length) {
        return $string;
    }
    return mb_substr($string, 0, $length) . $suffix;
}

/**
 * 生成SEO友好的URL
 */
function generateSlug(string $string): string {
    $slug = preg_replace('/[^a-z0-9]+/u', '-', strtolower($string));
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * 本地预览系统 - 不依赖外部服务
 */
class LocalPreviewSystem {
    private $temp_dir;
    private $preview_dir;
    
    public function __construct() {
        $this->temp_dir = getProjectRoot() . 'assets/temp/';
        $this->preview_dir = getProjectRoot() . 'assets/previews/';
        $this->createDirectories();
    }
    
    /**
     * 创建必要的目录
     */
    private function createDirectories() {
        $dirs = [$this->temp_dir, $this->preview_dir];
        foreach ($dirs as $dir) {
            createDirectorySafe($dir);
        }
    }
    
    /**
     * 获取文件预览选项
     */
    public function getPreviewOptions(string $file_url, string $filename): array {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $preview_type = getFilePreviewType($filename);
        
        $options = [
            'filename' => $filename,
            'extension' => $extension,
            'preview_type' => $preview_type,
            'methods' => []
        ];
        
        // 根据文件类型提供不同的预览选项
        switch ($preview_type) {
            case 'image':
                $options['methods']['view'] = [
                    'name' => '查看图片',
                    'type' => 'image',
                    'description' => '直接在浏览器中查看图片',
                    'icon' => 'fas fa-image'
                ];
                break;
                
            case 'pdf':
                $options['methods']['view'] = [
                    'name' => '查看PDF',
                    'type' => 'pdf',
                    'description' => '使用PDF.js在浏览器中查看',
                    'icon' => 'fas fa-file-pdf'
                ];
                break;
                
            case 'text':
                $options['methods']['view'] = [
                    'name' => '查看文本',
                    'type' => 'text',
                    'description' => '在浏览器中查看文本内容',
                    'icon' => 'fas fa-file-alt'
                ];
                break;
                
            case 'video':
                $options['methods']['view'] = [
                    'name' => '播放视频',
                    'type' => 'video',
                    'description' => '在浏览器中播放视频',
                    'icon' => 'fas fa-video'
                ];
                break;
                
            case 'audio':
                $options['methods']['view'] = [
                    'name' => '播放音频',
                    'type' => 'audio',
                    'description' => '在浏览器中播放音频',
                    'icon' => 'fas fa-music'
                ];
                break;
                
            case 'download':
            default:
                // Office文档等不支持预览的文件
                $options['methods']['info'] = [
                    'name' => '文件信息',
                    'type' => 'info',
                    'description' => '查看文件详细信息',
                    'icon' => 'fas fa-info-circle'
                ];
                break;
        }
        
        // 总是提供下载选项
        $options['methods']['download'] = [
            'name' => '下载文件',
            'type' => 'download',
            'description' => '下载到本地查看',
            'icon' => 'fas fa-download'
        ];
        
        return $options;
    }
}

/**
 * 获取文件预览信息
 */
function getFilePreviewInfo(string $file_url, string $filename): array {
    $preview = new LocalPreviewSystem();
    return $preview->getPreviewOptions($file_url, $filename);
}
?>