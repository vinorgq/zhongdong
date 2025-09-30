<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - 众东科技' : '众东科技 - 企业数字科技综合服务平台'; ?></title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/aos.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-cpu-fill text-primary me-2"></i>
                众东科技
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">首页</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">关于我们</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">产品服务</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="news.php">新闻动态</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="industry.php">行业背景</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">联系我们</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="contact.php" class="btn btn-primary">立即咨询</a>
                </div>
            </div>
        </div>
    </nav>