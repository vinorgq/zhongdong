<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = null;

if ($id) {
    $pdo = getDBConnection();
    $sql = "SELECT id, username, email, role FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'editor';
    $password = $_POST['password'] ?? '';
    
    $pdo = getDBConnection();
    
    if ($id && $user) {
        // 更新用户
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$username, $email, $role, $hashed_password, $id]);
        } else {
            $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$username, $email, $role, $id]);
        }
        $message = $result ? "用户更新成功" : "用户更新失败";
    } else {
        // 新增用户
        if (empty($password)) {
            $error = "新用户必须设置密码";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, role, password) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$username, $email, $role, $hashed_password]);
            $message = $result ? "用户添加成功" : "用户添加失败";
        }
    }
    
    if (isset($message) && strpos($message, '成功') !== false) {
        header("Location: users.php?message=$message");
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
    <title><?php echo $id ? '编辑' : '添加'; ?>用户 - 众东科技后台</title>
    
    <!-- 本地Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 本地Font Awesome CSS -->
    <link href="../assets/css/fontawesome-fixed.css" rel="stylesheet">
    
    <!-- 后台统一样式 -->
    <link href="../assets/css/admin.css" rel="stylesheet">
    
    <style>
        .form-container { max-width: 100%; }
        .required:after { content: " *"; color: red; }
        .password-strength { height: 5px; margin-top: 5px; border-radius: 2px; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-user text-danger"></i> <?php echo $id ? '编辑用户' : '添加用户'; ?>
            </h1>
            <a href="users.php" class="btn btn-secondary">
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
            <form method="POST" id="userForm">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> 用户信息
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label required">用户名</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" 
                                           required maxlength="50" pattern="[a-zA-Z0-9_]+" placeholder="只能包含字母、数字和下划线">
                                    <div class="form-text">用户名用于登录系统，只能包含字母、数字和下划线</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">邮箱地址</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" 
                                           placeholder="可选，用于接收通知">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label <?php echo !$id ? 'required' : ''; ?>">密码</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   <?php echo !$id ? 'required' : ''; ?> 
                                   minlength="6" placeholder="<?php echo $id ? '留空表示不修改密码' : '请输入密码'; ?>">
                            <div class="form-text">
                                <?php if ($id): ?>
                                    留空表示不修改密码
                                <?php else: ?>
                                    密码至少6位字符
                                <?php endif; ?>
                            </div>
                            <div class="password-strength" id="password-strength"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label required">用户角色</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="editor" <?php echo ($user['role'] ?? 'editor') == 'editor' ? 'selected' : ''; ?>>编辑</option>
                                <option value="admin" <?php echo ($user['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>管理员</option>
                            </select>
                            <div class="form-text">
                                <strong>编辑</strong>：可以管理新闻、产品、页面等内容<br>
                                <strong>管理员</strong>：拥有所有权限，包括用户管理
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save"></i> 保存用户
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
            // 密码强度检测
            $('#password').on('input', function() {
                const password = $(this).val();
                const strengthBar = $('#password-strength');
                let strength = 0;
                
                if (password.length >= 6) strength += 1;
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
                if (password.match(/\d/)) strength += 1;
                if (password.match(/[^a-zA-Z\d]/)) strength += 1;
                
                const colors = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success'];
                const widths = ['25%', '50%', '75%', '100%'];
                
                strengthBar.removeClass('bg-danger bg-warning bg-info bg-success');
                if (password.length > 0) {
                    strengthBar.addClass(colors[strength - 1] || 'bg-danger');
                    strengthBar.css('width', widths[strength - 1] || '25%');
                } else {
                    strengthBar.css('width', '0%');
                }
            });
            
            // 用户名格式验证
            $('#username').on('input', function() {
                const username = $(this).val();
                if (!username.match(/^[a-zA-Z0-9_]+$/)) {
                    this.setCustomValidity('用户名只能包含字母、数字和下划线');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
</body>
</html>