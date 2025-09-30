<?php
http_response_code(404);
$page_title = "页面未找到";
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
            <h1 data-aos="fade-up"><?php echo $page_title; ?></h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">首页</a></li>
                    <li class="breadcrumb-item active"><?php echo $page_title; ?></li>
                </ol>
            </nav>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 text-center" data-aos="fade-up">
                    <div class="error-page">
                        <h1 class="display-1 fw-bold text-primary">404</h1>
                        <div class="mb-4">
                            <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
                        </div>
                        <h2 class="h1 mb-4">页面未找到</h2>
                        <p class="lead mb-4 text-muted">抱歉，您访问的页面不存在或已被移动。</p>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <p class="text-muted mb-4">可能是输入地址有误，或者该页面已删除。您可以返回首页或联系我们获取帮助。</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                            <a href="index.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-house me-2"></i>返回首页
                            </a>
                            <a href="contact.php" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-headset me-2"></i>联系支持
                            </a>
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