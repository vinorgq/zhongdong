<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// 处理删除操作
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != $_SESSION['user_id']) { // 不能删除自己
        $pdo = getDBConnection();
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$id])) {
            $message = "用户删除成功";
        } else {
            $error = "用户删除失败";
        }
    } else {
        $error = "不能删除自己的账户";
    }
    header('Location: users.php?message=' . urlencode($message ?? $error ?? ''));
    exit;
}

// 获取用户列表
$pdo = getDBConnection();
$sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户管理 - 众东科技后台</title>
    
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
                <i class="fas fa-users text-danger"></i> 用户管理
            </h1>
            <a href="users-edit.php" class="btn btn-danger">
                <i class="fas fa-plus"></i> 添加用户
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
                    <i class="fas fa-list"></i> 用户列表
                </h5>
            </div>
            <div class="card-body">
                <?php if(empty($users)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-users fa-4x mb-3"></i>
                        <h5>暂无用户</h5>
                        <p>点击"添加用户"按钮创建第一个用户</p>
                        <a href="users-edit.php" class="btn btn-danger">
                            <i class="fas fa-plus"></i> 添加用户
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>用户名</th>
                                    <th>邮箱</th>
                                    <th>角色</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                        <span class="badge bg-info ms-1">当前用户</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($user['email']): ?>
                                            <?php echo htmlspecialchars($user['email']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">未设置</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $user['role'] == 'admin' ? 'bg-danger' : 'bg-secondary'; ?>">
                                            <?php echo $user['role'] == 'admin' ? '管理员' : '编辑'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="users-edit.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-edit"></i> 编辑
                                            </a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="users.php?delete=<?php echo $user['id']; ?>" 
                                               class="btn btn-danger" 
                                               onclick="return confirmDelete('确定删除这个用户吗？此操作不可恢复。')">
                                                <i class="fas fa-trash"></i> 删除
                                            </a>
                                            <?php else: ?>
                                            <button class="btn btn-secondary" disabled title="不能删除自己的账户">
                                                <i class="fas fa-trash"></i> 删除
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            共 <strong><?php echo count($users); ?></strong> 个用户
                        </div>
                        <div class="text-muted small">
                            <i class="fas fa-info-circle"></i> 
                            管理员可以管理所有用户，编辑只能管理内容
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>