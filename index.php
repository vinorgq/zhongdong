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
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/aos.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- 轮播图/横幅 -->
    <section class="hero-section">
        <div class="hero-background">
            <div class="hero-overlay"></div>
        </div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="display-4 fw-bold text-white mb-4">企业数字科技综合服务平台</h1>
                    <p class="lead text-white mb-4">科技赋能 / 产数融合 / 共享服务</p>
                    <div class="hero-buttons">
                        <a href="about.php" class="btn btn-primary btn-lg me-3">了解更多</a>
                        <a href="contact.php" class="btn btn-outline-light btn-lg">联系我们</a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="hero-image">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                             alt="数字科技" class="img-fluid rounded-3 shadow">
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-scroll-indicator">
            <i class="bi bi-chevron-down"></i>
        </div>
    </section>

    <!-- 统计数字 -->
    <section class="stats-section py-5 bg-primary text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-item">
                        <h3 class="display-4 fw-bold">50+</h3>
                        <p class="mb-0">合作企业</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-item">
                        <h3 class="display-4 fw-bold">100+</h3>
                        <p class="mb-0">成功项目</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-item">
                        <h3 class="display-4 fw-bold">5+</h3>
                        <p class="mb-0">行业经验</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-item">
                        <h3 class="display-4 fw-bold">24/7</h3>
                        <p class="mb-0">技术支持</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 公司简介 -->
    <section class="py-5 about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4" data-aos="fade-right">
                    <div class="about-image">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                             alt="众东科技团队" class="img-fluid rounded-3 shadow">
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span class="section-subtitle">关于我们</span>
                    <h2 class="section-title mb-4">关于众东科技</h2>
                    <p class="lead mb-4">众东科技是一家集文化科技产业链资源融合、信息技术服务、软件开发及运维、企业数字化全流程于一体的科技综合服务平台。</p>
                    <p class="mb-4">公司以"科技赋能、产数融合、共享服务"为战略导向，依托自主研发的"众东・灵活家"云系统，提供三大维度的服务...</p>
                    <div class="about-features">
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>5年以上行业经验</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>专业的技术团队</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>定制化解决方案</span>
                        </div>
                    </div>
                    <a href="about.php" class="btn btn-primary mt-4">查看更多</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 核心业务 -->
    <section class="py-5 bg-light services-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-subtitle">我们的服务</span>
                <h2 class="section-title">核心业务</h2>
                <p class="section-description">为企业提供全方位的数字化解决方案</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card service-card h-100 text-center">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="bi bi-link-45deg"></i>
                            </div>
                            <h5 class="card-title">产业链资源融合服务</h5>
                            <p class="card-text">为文化科技产业链的企业和人才提供资源融合，助力马栏山园区引入优质企业与高端人才。</p>
                            <div class="service-features">
                                <span class="badge bg-light text-dark">资源整合</span>
                                <span class="badge bg-light text-dark">人才引进</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card service-card h-100 text-center">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="bi bi-gear"></i>
                            </div>
                            <h5 class="card-title">数字化运营服务</h5>
                            <p class="card-text">为产业链上的市场主体提供全生命周期的数字化运营服务。</p>
                            <div class="service-features">
                                <span class="badge bg-light text-dark">全生命周期</span>
                                <span class="badge bg-light text-dark">智能运营</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card service-card h-100 text-center">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <h5 class="card-title">新就业形态服务</h5>
                            <p class="card-text">为产业链上新就业形态人员提供产品服务和解决方案。</p>
                            <div class="service-features">
                                <span class="badge bg-light text-dark">灵活就业</span>
                                <span class="badge bg-light text-dark">职业发展</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 最新动态 -->
    <section class="py-5 news-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-subtitle">最新资讯</span>
                <h2 class="section-title">最新动态</h2>
                <p class="section-description">了解行业最新动态和公司新闻</p>
            </div>
            <div class="row">
                <?php if(empty($latest_news)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">暂无新闻动态</div>
                </div>
                <?php else: ?>
                <?php foreach($latest_news as $news): ?>
                <div class="col-md-4 mb-4" data-aos="fade-up">
                    <div class="card news-card h-100">
                        <?php if($news['image_url']): ?>
                        <img src="<?php echo $news['image_url']; ?>" class="card-img-top" alt="<?php echo $news['title']; ?>">
                        <?php else: ?>
                        <img src="https://images.unsplash.com/photo-1586953208448-b95a79798f07?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                             class="card-img-top" alt="默认新闻图片">
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="news-meta mb-2">
                                <span class="badge bg-primary"><?php echo $news['category_name']; ?></span>
                                <span class="text-muted"><?php echo date('Y-m-d', strtotime($news['published_at'])); ?></span>
                            </div>
                            <h5 class="card-title"><?php echo $news['title']; ?></h5>
                            <p class="card-text"><?php echo $news['summary']; ?></p>
                            <a href="news-detail.php?id=<?php echo $news['id']; ?>" class="btn btn-outline-primary">阅读更多</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4" data-aos="fade-up">
                <a href="news.php" class="btn btn-primary">查看所有动态</a>
            </div>
        </div>
    </section>

    <!-- 合作伙伴 -->
    <section class="py-5 bg-light partners-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-subtitle">信任我们</span>
                <h2 class="section-title">合作伙伴</h2>
                <p class="section-description">与行业领先企业共同成长</p>
            </div>
            <div class="row justify-content-center align-items-center" data-aos="fade-up">
                <div class="col-6 col-md-3 text-center mb-4">
                    <div class="partner-logo">
                        <img src="https://images.unsplash.com/photo-1611605698335-8b1569810432?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                             alt="抖音" class="img-fluid">
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center mb-4">
                    <div class="partner-logo">
                        <img src="https://images.unsplash.com/photo-1611605698335-8b1569810432?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                             alt="芒果TV" class="img-fluid">
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center mb-4">
                    <div class="partner-logo">
                        <img src="https://images.unsplash.com/photo-1611605698335-8b1569810432?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                             alt="天娱传媒" class="img-fluid">
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center mb-4">
                    <div class="partner-logo">
                        <img src="https://images.unsplash.com/photo-1611605698335-8b1569810432?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                             alt="兴盛优选" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA 部分 -->
    <section class="py-5 cta-section bg-primary text-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <h2 class="mb-4">准备好开始您的数字化转型了吗？</h2>
                    <p class="lead mb-4">让我们帮助您实现业务增长和创新</p>
                    <div class="cta-buttons">
                        <a href="contact.php" class="btn btn-light btn-lg me-3">立即咨询</a>
                        <a href="about.php" class="btn btn-outline-light btn-lg">了解更多</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js//aos.js"></script>
    <script>
        // 初始化AOS动画
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // 滚动指示器
        document.querySelector('.hero-scroll-indicator').addEventListener('click', function() {
            window.scrollTo({
                top: document.querySelector('.stats-section').offsetTop,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>