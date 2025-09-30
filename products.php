<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$products = getAllProducts();
$categories = getAllCategories('product');
$page_title = "产品服务";
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
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-subtitle">我们的产品</span>
                <h2 class="section-title">产品与服务</h2>
                <p class="section-description">为企业提供全方位的数字化解决方案和服务</p>
            </div>
            
            <!-- 产品分类 -->
            <?php if(!empty($categories)): ?>
            <div class="row mb-5">
                <div class="col-12" data-aos="fade-up">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">产品分类</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="products.php" class="btn btn-outline-primary active">全部产品</a>
                                <?php foreach($categories as $cat): ?>
                                <a href="products.php?category=<?php echo $cat['id']; ?>" class="btn btn-outline-primary">
                                    <?php echo $cat['name']; ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- 产品列表 -->
            <div class="row">
                <?php if(empty($products)): ?>
                <div class="col-12" data-aos="fade-up">
                    <div class="card border-0 text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-box-seam display-1 text-muted mb-3"></i>
                            <h3 class="text-muted">暂无产品信息</h3>
                            <p class="text-muted mb-4">我们正在不断完善产品信息，敬请期待</p>
                            <a href="contact.php" class="btn btn-primary">联系我们获取详情</a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <?php foreach($products as $index => $product): ?>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 100; ?>">
                    <div class="card product-card h-100">
                        <?php if($product['image_url']): ?>
                        <img src="<?php echo $product['image_url']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-image text-muted display-4"></i>
                        </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text text-muted"><?php echo $product['description']; ?></p>
                            <?php if($product['features']): ?>
                            <div class="product-features">
                                <h6 class="text-primary">产品特点</h6>
                                <p class="text-muted small"><?php echo $product['features']; ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">热销产品</span>
                                <a href="contact.php" class="btn btn-outline-primary btn-sm">咨询详情</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- CTA -->
            <div class="row mt-5">
                <div class="col-12" data-aos="fade-up">
                    <div class="card border-0 bg-primary text-white">
                        <div class="card-body p-5 text-center">
                            <h3 class="text-white mb-3">需要定制化解决方案？</h3>
                            <p class="text-white-50 mb-4">我们提供专业的定制化服务，满足您的特定需求</p>
                            <a href="contact.php" class="btn btn-light btn-lg">立即咨询</a>
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