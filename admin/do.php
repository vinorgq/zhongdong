<?php
// download_assets.php - 下载富文本编辑器相关文件
$assets = [
    'jquery' => [
        'url' => 'https://cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js',
        'path' => 'assets/js/jquery.min.js'
    ],
    'bootstrap_css' => [
        'url' => 'https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css',
        'path' => 'assets/css/bootstrap.min.css'
    ],
    'bootstrap_js' => [
        'url' => 'https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js',
        'path' => 'assets/js/bootstrap.bundle.min.js'
    ],
    'summernote_css' => [
        'url' => 'https://cdn.bootcdn.net/ajax/libs/summernote/0.8.20/summernote-lite.min.css',
        'path' => 'assets/css/summernote-lite.min.css'
    ],
    'summernote_js' => [
        'url' => 'https://cdn.bootcdn.net/ajax/libs/summernote/0.8.20/summernote-lite.min.js',
        'path' => 'assets/js/summernote-lite.min.js'
    ],
    'summernote_lang' => [
        'url' => 'https://cdn.bootcdn.net/ajax/libs/summernote/0.8.20/lang/summernote-zh-CN.min.js',
        'path' => 'assets/js/summernote-zh-CN.min.js'
    ]
];

echo "<h3>下载富文本编辑器资源文件</h3>";

foreach ($assets as $name => $asset) {
    echo "下载 {$name}... ";
    
    // 确保目录存在
    $dir = dirname($asset['path']);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $content = @file_get_contents($asset['url']);
    if ($content !== false) {
        file_put_contents($asset['path'], $content);
        echo "<span style='color:green'>成功</span><br>";
    } else {
        echo "<span style='color:red'>失败</span><br>";
    }
}

echo "<h4>下载完成！现在可以使用本地文件了。</h4>";
?>