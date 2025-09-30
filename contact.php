<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = "联系我们";

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $company = $_POST['company'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // 这里可以添加邮件发送或数据库存储逻辑
    $success = true; // 模拟成功
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - 众东科技</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- 页面标题 -->
    <section class="page-hero">
        <div class="container">
            <h1 data-aos="fade-up">联系我们</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">首页</a></li>
                    <li class="breadcrumb-item active">联系我们</li>
                </ol>
            </nav>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mb-5" data-aos="fade-right">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h3 class="mb-4">发送消息</h3>
                            
                            <?php if (isset($success) && $success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                感谢您的留言！我们会尽快与您联系。
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>
                            
                            <form class="contact-form" method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">姓名 *</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">邮箱 *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">电话</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="company" class="form-label">公司</label>
                                        <input type="text" class="form-control" id="company" name="company">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">主题 *</label>
                                    <input type="text" class="form-control" id="subject" name="subject" required>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">留言内容 *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg">发送消息</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="contact-info h-100">
                        <h3 class="text-white mb-4">联系信息</h3>
                        
                        <div class="contact-info-item">
                            <i class="bi bi-geo-alt"></i>
                            <div>
                                <h5>公司地址</h5>
                                <p>湖南省长沙市马栏山视频文创产业园</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="bi bi-telephone"></i>
                            <div>
                                <h5>联系电话</h5>
                                <p>400-9910-065</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="bi bi-envelope"></i>
                            <div>
                                <h5>电子邮箱</h5>
                                <p>contact@zhongdongtech.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="bi bi-clock"></i>
                            <div>
                                <h5>服务时间</h5>
                                <p>周一至周五: 9:00-18:00<br>周末: 紧急技术支持</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5 class="text-white mb-3">关注我们</h5>
                            <div class="d-flex gap-3">
                                <a href="#" class="text-white fs-4"><i class="bi bi-wechat"></i></a>
                                <a href="#" class="text-white fs-4"><i class="bi bi-weibo"></i></a>
                                <a href="#" class="text-white fs-4"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 地图部分 -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12" data-aos="fade-up">
                    <div class="card border-0 shadow">
                        <div class="card-body p-0">
                            <div class="ratio ratio-16x9">
                                <!-- 这里可以嵌入实际的地图 -->
                                <div class="d-flex align-items-center justify-content-center bg-primary text-white">
                                    <div class="text-center">
                                        <i class="bi bi-map display-1 mb-3"></i>
                                        <h4>公司位置地图</h4>
                                        <p>湖南省长沙市马栏山视频文创产业园</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
    </script>
</body>
</html>