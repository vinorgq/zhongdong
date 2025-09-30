<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$limit = 10;
$offset = ($page - 1) * $limit;

// 获取新闻总数
$pdo = getDBConnection();
$sql = "SELECT COUNT(*) as total FROM news WHERE status = 'published'";
if ($category_id) {
    $sql .= " AND category_id = ?";
}
$stmt = $pdo->prepare($sql);
$stmt->execute($category_id ? [$category_id] : []);
$total_news = $stmt->fetch()['total'];
$total_pages = ceil($total_news / $limit);

// 获取新闻列表
$news_list = getAllNews($category_id, $limit, $offset);
$categories = getAllCategories('news');
$page_title = "新闻动态";
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
    
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-subtitle">最新资讯</span>
                <h2 class="section-title">新闻动态</h2>
                <p class="section-description">了解行业最新动态和公司新闻</p>
            </div>
            
            <!-- 分类筛选 -->
            <div class="row mb-5">
                <div class="col-12" data-aos="fade-up">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">分类筛选</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="news.php" class="btn btn-outline-primary <?php echo !$category_id ? 'active' : ''; ?>">
                                    全部
                                    <span class="badge bg-primary ms-1"><?php echo $total_news; ?></span>
                                </a>
                                <?php foreach($categories as $cat): ?>
                                <?php 
                                $cat_count_sql = "SELECT COUNT(*) as count FROM news WHERE status = 'published' AND category_id = ?";
                                $cat_stmt = $pdo->prepare($cat_count_sql);
                                $cat_stmt->execute([$cat['id']]);
                                $cat_count = $cat_stmt->fetch()['count'];
                                ?>
                                <a href="news.php?category=<?php echo $cat['id']; ?>" class="btn btn-outline-primary <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                                    <?php echo $cat['name']; ?>
                                    <span class="badge bg-primary ms-1"><?php echo $cat_count; ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 新闻列表 -->
            <div class="row">
                <?php if(empty($news_list)): ?>
                <div class="col-12" data-aos="fade-up">
                    <div class="card border-0 text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-newspaper display-1 text-muted mb-3"></i>
                            <h3 class="text-muted">暂无新闻</h3>
                            <p class="text-muted mb-4">当前分类下还没有发布新闻内容</p>
                            <a href="news.php" class="btn btn-primary">查看所有新闻</a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <?php foreach($news_list as $index => $news): ?>
                <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo ($index % 2) * 100; ?>">
                    <div class="card news-card h-100">
                        <div class="row g-0 h-100">
                            <div class="col-md-4">
                                <?php if($news['image_url']): ?>
                                <img src="<?php echo $news['image_url']; ?>" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="<?php echo $news['title']; ?>">
                                <?php else: ?>
                                <div class="bg-light h-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-image text-muted display-6"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column h-100">
                                    <div class="news-meta mb-2">
                                        <span class="badge bg-primary"><?php echo $news['category_name']; ?></span>
                                        <small class="text-muted"><?php echo date('Y-m-d', strtotime($news['published_at'])); ?></small>
                                    </div>
                                    <h5 class="card-title"><?php echo $news['title']; ?></h5>
                                    <p class="card-text flex-grow-1"><?php echo $news['summary']; ?></p>
                                    <div class="mt-auto">
                                        <a href="news-detail.php?id=<?php echo $news['id']; ?>" class="btn btn-outline-primary btn-sm">阅读全文</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- 分页 -->
            <?php if($total_pages > 1): ?>
            <div class="row mt-5">
                <div class="col-12" data-aos="fade-up">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="news.php?page=<?php echo $page-1; ?><?php echo $category_id ? '&category='.$category_id : ''; ?>">
                                    <i class="bi bi-chevron-left me-1"></i>上一页
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php 
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="news.php?page=1'.($category_id ? '&category='.$category_id : '').'">1</a></li>';
                                if ($start_page > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }
                            
                            for($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="news.php?page=<?php echo $i; ?><?php echo $category_id ? '&category='.$category_id : ''; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="news.php?page='.$total_pages.($category_id ? '&category='.$category_id : '').'">'.$total_pages.'</a></li>';
                            }
                            ?>
                            
                            <?php if($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="news.php?page=<?php echo $page+1; ?><?php echo $category_id ? '&category='.$category_id : ''; ?>">
                                    下一页<i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
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