<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// 获取最新新闻
$latest_news = getLatestNews(3);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>众东科技 - 企业数字科技综合服务平台</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/logo.png" alt="众东科技" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">首页</a></li>
                    <li class="nav-item"><a class="nav-link" href="news.php">动态</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">产品介绍</a></li>
                    <li class="nav-item"><a class="nav-link" href="industry.php">行业背景</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">关于我们</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 轮播图/横幅 -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white">企业数字科技综合服务平台</h1>
                    <p class="lead text-white">科技赋能 / 产数融合 / 共享服务</p>
                    <a href="about.php" class="btn btn-primary btn-lg mt-3">了解更多</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 公司简介 -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="section-title">关于众东科技</h2>
                    <p>众东科技是一家集文化科技产业链资源融合、信息技术服务、软件开发及运维、企业数字化全流程于一体的科技综合服务平台。</p>
                    <p>公司以"科技赋能、产数融合、共享服务"为战略导向，依托自主研发的"众东・灵活家"云系统，提供三大维度的服务...</p>
                    <a href="about.php" class="btn btn-outline-primary">查看更多</a>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/company-image.jpg" alt="众东科技" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- 核心业务 -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5">核心业务</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <h5 class="card-title">产业链资源融合服务</h5>
                            <p class="card-text">为文化科技产业链的企业和人才提供资源融合，助力马栏山园区引入优质企业与高端人才。</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <h5 class="card-title">数字化运营服务</h5>
                            <p class="card-text">为产业链上的市场主体提供全生命周期的数字化运营服务。</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <h5 class="card-title">新就业形态服务</h5>
                            <p class="card-text">为产业链上新就业形态人员提供产品服务和解决方案。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 最新动态 -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">最新动态</h2>
            <div class="row">
                <?php foreach($latest_news as $news): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if($news['image_url']): ?>
                        <img src="<?php echo $news['image_url']; ?>" class="card-img-top" alt="<?php echo $news['title']; ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $news['title']; ?></h5>
                            <p class="card-text"><?php echo $news['summary']; ?></p>
                            <a href="news-detail.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-outline-primary">阅读更多</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="news.php" class="btn btn-primary">查看所有动态</a>
            </div>
        </div>
    </section>

    <!-- 合作伙伴 -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5">合作伙伴</h2>
            <div class="row justify-content-center">
                <div class="col-auto"><img src="assets/images/partner-douyin.png" alt="抖音" height="50"></div>
                <div class="col-auto"><img src="assets/images/partner-mangotv.png" alt="芒果TV" height="50"></div>
                <div class="col-auto"><img src="assets/images/partner-tianyu.png" alt="天娱传媒" height="50"></div>
                <div class="col-auto"><img src="assets/images/partner-xingsheng.png" alt="兴盛优选" height="50"></div>
            </div>
        </div>
    </section>

    <!-- 页脚 -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>众东科技</h5>
                    <p>企业数字科技综合服务平台</p>
                </div>
                <div class="col-md-4">
                    <h5>联系我们</h5>
                    <p>电话: 400-9910-065</p>
                    <p>地址: 湖南省长沙市马栏山视频文创产业园</p>
                </div>
                <div class="col-md-4">
                    <h5>快速链接</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">首页</a></li>
                        <li><a href="news.php" class="text-white">动态</a></li>
                        <li><a href="products.php" class="text-white">产品介绍</a></li>
                        <li><a href="about.php" class="text-white">关于我们</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-3">
                <p>&copy; 2024 众东科技 版权所有</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>