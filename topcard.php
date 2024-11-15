#!/usr/bin/php
<?php
/**
 * 汉字小卡片生成
 * 更新为 PHP 8 兼容版
 */

if ($argc !== 2) {
    echo "Usage: ./hanzicard.php word\n";
    exit(1); // 改为返回非 0 值表示错误
}

$string = $argv[1];
$w = 900;
$h = 500;

// 创建图像资源
$im = imagecreatetruecolor($w, $h);
if (!$im) {
    throw new RuntimeException("Failed to create image");
}

// 设置背景色
$bgcolor = imagecolorallocate($im, 0xef, 0xef, 0xe1);
imagefill($im, 0, 0, $bgcolor);

// 检查田字格图像文件
$tianzgPath = 'img/ic_img_top_tzg.png';
if (!file_exists($tianzgPath)) {
    throw new RuntimeException("田字格文件不存在: $tianzgPath");
}
$tianzg = imagecreatefrompng($tianzgPath);
if (!$tianzg) {
    throw new RuntimeException("无法加载田字格图像: $tianzgPath");
}

// 画田字格
imagecopy($im, $tianzg, ($w - 360) / 2, ($h - 360) / 2, 0, 0, 360, 360);
imagedestroy($tianzg); // 释放田字格资源

// 设置字体颜色
$fontcolor = imagecolorallocate($im, 33, 33, 33);

// 字体文件路径
$font = './Fonts/华文细黑.ttf';
if (!file_exists($font)) {
    throw new RuntimeException("字体文件不存在: $font");
}

// 计算文字位置和尺寸
$result = calculateTextBox(190, 0, $font, $string);
if (!$result) {
    throw new RuntimeException("无法计算文本边框");
}

$font_w = $result['width'];
$font_h = $result['height'];

// 绘制汉字
imagettftext(
    $im,
    190,
    0,
    ($w - $font_w) / 2 + $result['left'],
    ($h - $font_h) / 2 + $result['top'],
    $fontcolor,
    $font,
    $string
);

// 输出图片
header("Content-Type: image/png");
imagepng($im, null, 9);

// 释放资源
imagedestroy($im);

/**
 * 计算文字边框
 * @param int $font_size 字体大小
 * @param int $font_angle 字体角度
 * @param string $font_file 字体文件
 * @param string $text 文本
 * @return array|false 边框信息或 false
 */
function calculateTextBox($font_size, $font_angle, $font_file, $text)
{
    $box = imagettfbbox($font_size, $font_angle, $font_file, $text);
    if (!$box) {
        return false;
    }

    $min_x = min($box[0], $box[2], $box[4], $box[6]);
    $max_x = max($box[0], $box[2], $box[4], $box[6]);
    $min_y = min($box[1], $box[3], $box[5], $box[7]);
    $max_y = max($box[1], $box[3], $box[5], $box[7]);

    $width = $max_x - $min_x;
    $height = $max_y - $min_y;
    $left = abs($min_x);
    $top = abs($min_y);

    // 使用更小的调试图像避免内存浪费
    $img = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    imagefilledrectangle($img, 0, 0, $width, $height, $black);

    imagettftext($img, $font_size,
        $font_angle,
        $left,
        $top,
        $white,
        $font_file,
        $text
    );

    imagedestroy($img);

    return [
        "left" => $left,
        "top" => $top,
        "width" => $width,
        "height" => $height
    ];
}
