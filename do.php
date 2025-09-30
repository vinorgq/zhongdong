<?php
// download_fonts.php - 下载所有字体文件到本地
$fonts = [
    // Summernote 字体文件
    'summernote_eot' => [
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/font/summernote.eot',
        'path' => 'assets/fonts/summernote.eot'
    ],
    'summernote_woff' => [
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/font/summernote.woff',
        'path' => 'assets/fonts/summernote.woff'
    ],
    'summernote_woff2' => [
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/font/summernote.woff2',
        'path' => 'assets/fonts/summernote.woff2'
    ],
    'summernote_ttf' => [
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/font/summernote.ttf',
        'path' => 'assets/fonts/summernote.ttf'
    ],
    
    // Font Awesome 字体文件
    'fa_solid_900_woff2' => [
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.woff2',
        'path' => 'assets/fonts/fa-solid-900.woff2'
    ],
    'fa_solid_900_ttf' => [
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.ttf',
        'path' => 'assets/fonts/fa-solid-900.ttf'
    ],
    'fa_solid_900_woff' => [
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.woff',
        'path' => 'assets/fonts/fa-solid-900.woff'
    ],
    'fa_solid_900_eot' => [
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.eot',
        'path' => 'assets/fonts/fa-solid-900.eot'
    ],
    
    // Font Awesome 其他字体文件
    'fa_brands_400_woff2' => [
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-brands-400.woff2',
        'path' => 'assets/fonts/fa-brands-400.woff2'
    ],
    'fa_regular_400_woff2' => [
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-regular-400.woff2',
        'path' => 'assets/fonts/fa-regular-400.woff2'
    ]
];

echo "<h3>下载所有字体文件到本地</h3>";

// 确保字体目录存在
if (!is_dir('assets/fonts')) {
    mkdir('assets/fonts', 0755, true);
}

foreach ($fonts as $name => $font) {
    echo "下载 {$name}... ";
    
    $content = @file_get_contents($font['url']);
    if ($content !== false) {
        file_put_contents($font['path'], $content);
        echo "<span style='color:green'>成功</span><br>";
    } else {
        echo "<span style='color:red'>失败</span><br>";
    }
}

// 创建修复字体路径的CSS文件
echo "创建修复字体路径的CSS文件... ";

// 修复Summernote CSS
$summernote_css = file_get_contents('assets/css/summernote-lite.min.css');
$summernote_css = str_replace(
    "url('font/summernote",
    "url('../fonts/summernote",
    $summernote_css
);
file_put_contents('assets/css/summernote-fixed.css', $summernote_css);

// 修复Font Awesome CSS
$fa_css = file_get_contents('assets/css/fontawesome.min.css');
$fa_css = str_replace(
    "url(../webfonts/",
    "url(../fonts/",
    $fa_css
);
file_put_contents('assets/css/fontawesome-fixed.css', $fa_css);

echo "<span style='color:green'>成功</span><br>";

echo "<h4 style='color:green'>所有字体文件下载完成！</h4>";
?>