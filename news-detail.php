<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$news = getNewsById($id);

if (!$news || $news['status'] != 'published') {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

$page_title = $news['title'];

// 获取相关新闻
$pdo = getDBConnection();
$related_sql = "SELECT id, title, image_url, published_at, summary FROM news 
                WHERE status = 'published' AND category_id = ? AND id != ? 
                ORDER BY published_at DESC LIMIT 3";
$related_stmt = $pdo->prepare($related_sql);
$related_stmt->execute([$news['category_id'], $news['id']]);
$related_news = $related_stmt->fetchAll();
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
            <h1 data-aos="fade-up" class="news-detail-title"><?php echo $news['title']; ?></h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">首页</a></li>
                    <li class="breadcrumb-item"><a href="news.php">新闻动态</a></li>
                    <li class="breadcrumb-item active">新闻详情</li>
                </ol>
            </nav>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <article>
                        <header class="mb-4" data-aos="fade-up">
                            <div class="news-meta-large mb-3">
                                <span class="badge bg-primary fs-6"><?php echo $news['category_name']; ?></span>
                                <span class="text-muted mx-3">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?php echo date('Y年m月d日', strtotime($news['published_at'])); ?>
                                </span>
                                <?php if($news['author_name']): ?>
                                <span class="text-muted">
                                    <i class="bi bi-person me-1"></i>
                                    <?php echo $news['author_name']; ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </header>
                        
                        <?php if($news['image_url']): ?>
                        <div class="mb-4" data-aos="fade-up">
                            <img src="<?php echo $news['image_url']; ?>" alt="<?php echo $news['title']; ?>" class="img-fluid rounded-3 shadow">
                        </div>
                        <?php endif; ?>
                        
                        <div class="news-content" data-aos="fade-up">
                            <?php echo $news['content']; ?>
                        </div>
                        
                        <footer class="mt-5 pt-4 border-top" data-aos="fade-up">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">最后更新: <?php echo date('Y-m-d H:i', strtotime($news['updated_at'])); ?></small>
                                </div>
                                <div class="share-buttons">
                                    <span class="text-muted me-2">分享:</span>
                                    <a href="#" class="text-muted me-2"><i class="bi bi-wechat fs-5"></i></a>
                                    <a href="#" class="text-muted me-2"><i class="bi bi-weibo fs-5"></i></a>
                                    <a href="#" class="text-muted"><i class="bi bi-link-45deg fs-5"></i></a>
                                </div>
                            </div>
                        </footer>
                    </article>
                    
                    <!-- 相关新闻 -->
                    <?php if(!empty($related_news)): ?>
                    <div class="mt-5" data-aos="fade-up">
                        <h4 class="mb-4">相关新闻</h4>
                        <div class="row">
                            <?php foreach($related_news as $related): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 h-100">
                                    <?php if($related['image_url']): ?>
                                    <img src="<?php echo $related['image_url']; ?>" class="card-img-top" alt="<?php echo $related['title']; ?>" style="height: 120px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo mb_strimwidth($related['title'], 0, 30, '...'); ?></h6>
                                        <small class="text-muted"><?php echo date('m-d', strtotime($related['published_at'])); ?></small>
                                        <a href="news-detail.php?id=<?php echo $related['id']; ?>" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-5 text-center" data-aos="fade-up">
                        <a href="news.php" class="btn btn-outline-primary me-3">
                            <i class="bi bi-arrow-left me-2"></i>返回新闻列表
                        </a>
                        <a href="contact.php" class="btn btn-primary">
                            <i class="bi bi-envelope me-2"></i>联系我们
                        </a>
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