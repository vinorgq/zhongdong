<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_content = getPageContent('industry');
$page_title = "行业背景";
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
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <?php if($page_content): ?>
                        <div class="page-content" data-aos="fade-up">
                            <h1 class="text-center mb-5"><?php echo $page_content['title']; ?></h1>
                            <div class="content-body">
                                <?php echo $page_content['content']; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- 默认行业背景内容 -->
                        <div data-aos="fade-up">
                            <div class="text-center mb-5">
                                <span class="section-subtitle">行业洞察</span>
                                <h2 class="section-title">行业背景与发展趋势</h2>
                                <p class="section-description">深入了解数字经济发展现状与未来前景</p>
                            </div>
                            
                            <div class="row mb-5">
                                <div class="col-lg-6 mb-4" data-aos="fade-right">
                                    <div class="card border-0 h-100 shadow-sm">
                                        <div class="card-body p-4">
                                            <div class="service-icon mx-auto mb-3">
                                                <i class="bi bi-graph-up-arrow"></i>
                                            </div>
                                            <h3 class="h4 text-center mb-3">数字经济时代</h3>
                                            <p class="text-muted">2024年将是中国社会经济系统全面强化数据要素的一年，是各产业全面开展数字化转型的一年，也会是我国社会开始全面走向数字治理的一年。</p>
                                            <p class="text-muted mb-0">中国将开始系统化打造以数据资源确权与流通体系、全社会主体数字信用体系等为基础的社会经济运营新环境，并以此来引领各产业系统性思考数据驱动的新发展模式，完成产业数字化转型。</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4" data-aos="fade-left">
                                    <div class="card border-0 h-100 shadow-sm">
                                        <div class="card-body p-4">
                                            <div class="service-icon mx-auto mb-3">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </div>
                                            <h3 class="h4 text-center mb-3">政策支持</h3>
                                            <p class="text-muted">2023年2月，中共中央、国务院印发了《数字中国建设整体布局规划》，提出到2025年，要基本形成横向打通、纵向贯通、协调有力的一体化推进格局，数字中国建设取得重要进展。</p>
                                            <p class="text-muted mb-0">据相关报道：2022年中国数字经济市场规模达50.2万亿元，总量稳居世界第二，占GDP比重提升至41.5%。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card border-0 bg-light mb-5" data-aos="fade-up">
                                <div class="card-body p-5">
                                    <div class="row align-items-center">
                                        <div class="col-lg-8">
                                            <h3 class="mb-3">市场前景广阔</h3>
                                            <p class="text-muted mb-3">随着数字经济发展动能加速释放，中商产业研究院分析师预测，2023年中国数字经济市场规模将增长至56.7万亿元，2024年市场规模将增至63.8万亿元。</p>
                                            <p class="text-muted mb-0">数字经济已成为推动中国经济增长主引擎之一。且到2036年，我国自由职业者数量将达到4亿。在巨大的市场供需中，系统性的灵活就业服务将成为我国数字经济的增长点、新经济发展的一股重要力量。</p>
                                        </div>
                                        <div class="col-lg-4 text-center">
                                            <div class="display-4 fw-bold text-primary">63.8万亿</div>
                                            <p class="text-muted">2024年预测市场规模</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row" data-aos="fade-up">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-4">
                                            <h4 class="mb-3">发展机遇</h4>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="d-flex align-items-start">
                                                        <i class="bi bi-check-circle-fill text-primary me-3 mt-1"></i>
                                                        <div>
                                                            <h5>数字化转型加速</h5>
                                                            <p class="text-muted mb-0">传统企业数字化转型需求旺盛，市场空间巨大</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="d-flex align-items-start">
                                                        <i class="bi bi-check-circle-fill text-primary me-3 mt-1"></i>
                                                        <div>
                                                            <h5>政策红利释放</h5>
                                                            <p class="text-muted mb-0">国家政策大力支持数字经济发展</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="d-flex align-items-start">
                                                        <i class="bi bi-check-circle-fill text-primary me-3 mt-1"></i>
                                                        <div>
                                                            <h5>技术不断创新</h5>
                                                            <p class="text-muted mb-0">人工智能、大数据等技术持续突破</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="d-flex align-items-start">
                                                        <i class="bi bi-check-circle-fill text-primary me-3 mt-1"></i>
                                                        <div>
                                                            <h5>人才需求增长</h5>
                                                            <p class="text-muted mb-0">数字化人才市场需求持续扩大</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
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