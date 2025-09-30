<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$base_url = 'https://yourdomain.com/'; // 替换为实际域名

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// 静态页面
$pages = [
    'index.php' => 'weekly',
    'about.php' => 'monthly', 
    'products.php' => 'weekly',
    'news.php' => 'daily',
    'industry.php' => 'monthly',
    'contact.php' => 'monthly'
];

foreach ($pages as $page => $freq) {
    echo '<url>';
    echo '<loc>' . $base_url . $page . '</loc>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
    echo '<changefreq>' . $freq . '</changefreq>';
    echo '<priority>' . ($page == 'index.php' ? '1.0' : '0.8') . '</priority>';
    echo '</url>';
}

// 新闻详情页
$pdo = getDBConnection();
$sql = "SELECT id, updated_at FROM news WHERE status = 'published'";
$stmt = $pdo->query($sql);
$news = $stmt->fetchAll();

foreach ($news as $item) {
    echo '<url>';
    echo '<loc>' . $base_url . 'news-detail.php?id=' . $item['id'] . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($item['updated_at'])) . '</lastmod>';
    echo '<changefreq>monthly</changefreq>';
    echo '<priority>0.6</priority>';
    echo '</url>';
}

// 产品页面（如果有独立产品详情页的话）
$product_sql = "SELECT id, updated_at FROM products WHERE status = 'active'";
$product_stmt = $pdo->query($product_sql);
$products = $product_stmt->fetchAll();

foreach ($products as $product) {
    echo '<url>';
    echo '<loc>' . $base_url . 'product-detail.php?id=' . $product['id'] . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($product['updated_at'])) . '</lastmod>';
    echo '<changefreq>monthly</changefreq>';
    echo '<priority>0.7</priority>';
    echo '</url>';
}

echo '</urlset>';
?>