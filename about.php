<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_content = getPageContent('about');
$page_title = "关于我们";
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
            <h1 data-aos="fade-up">关于我们</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">首页</a></li>
                    <li class="breadcrumb-item active">关于我们</li>
                </ol>
            </nav>
        </div>
    </section>
    
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
                    <p class="mb-4">湖南众东信息科技有限公司于2020年3月在马栏山视频文创产业园成立，深耕"文化+科技"领域5年以上，是一家聚焦"文化科技"领域的综合服务平台。</p>
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
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>7×24小时技术支持</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 公司愿景 -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-up">
                    <div class="card h-100 text-center border-0">
                        <div class="card-body">
                            <div class="service-icon mx-auto">
                                <i class="bi bi-eye"></i>
                            </div>
                            <h4 class="my-3">公司愿景</h4>
                            <p class="text-muted">成为文化科技领域的领先服务商，推动产业数字化转型，创造更大的社会价值。</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 text-center border-0">
                        <div class="card-body">
                            <div class="service-icon mx-auto">
                                <i class="bi bi-bullseye"></i>
                            </div>
                            <h4 class="my-3">公司使命</h4>
                            <p class="text-muted">通过科技创新，为企业提供全方位的数字化解决方案，助力客户实现业务增长。</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 text-center border-0">
                        <div class="card-body">
                            <div class="service-icon mx-auto">
                                <i class="bi bi-heart"></i>
                            </div>
                            <h4 class="my-3">核心价值观</h4>
                            <p class="text-muted">创新、专业、诚信、共赢，以客户为中心，持续创造价值。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 联系我们 -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                    <span class="section-subtitle">联系我们</span>
                    <h2 class="section-title mb-4">随时为您服务</h2>
                    <p class="section-description mb-5">如果您有任何问题或需要咨询我们的服务，请随时联系我们。</p>
                    
                    <div class="row">
                        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="contact-info-item">
                                <i class="bi bi-telephone"></i>
                                <div>
                                    <h5>电话</h5>
                                    <p>400-9910-065</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="contact-info-item">
                                <i class="bi bi-geo-alt"></i>
                                <div>
                                    <h5>地址</h5>
                                    <p>湖南省长沙市马栏山视频文创产业园</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                            <div class="contact-info-item">
                                <i class="bi bi-clock"></i>
                                <div>
                                    <h5>服务时间</h5>
                                    <p>7×24小时竭诚为您服务！</p>
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