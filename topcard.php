#!/usr/bin/php
<?php
/**
 * Created by PhpStorm.
 * User: ljm
 * Date: 15/9/13
 * Time: 下午6:13
 */

//生成汉字小卡片

if ($argc != 2) {
    echo "Usage: ./hanzicard.php word\n";
    exit;
}

$string = $argv[1];
$w = 900;
$h = 500;
$im = imagecreate($w, $h);

$bgcolor = imagecolorallocate($im, 0xef, 0xef, 0xe1);


//画田字格
$tianzg = imagecreatefrompng('img/ic_img_top_tzg.png');
imagecopy($im, $tianzg, ($w - 360) / 2, ($h - 360) / 2, 0, 0, 360, 360);

//画汉字
$fontcolor = imagecolorallocate($im, 33, 33, 33);
$font = "./Fonts/华文细黑.ttf";
$result = calculateTextBox(190, 0, $font, $string);
$font_w = $result['width'];
$font_h = $result['height'];
//
imagettftext($im, 190, 0, ($w - $font_w) / 2 + $result['left'], ($h - $font_h) / 2 + $result['top'], $fontcolor, $font, $string);

//生成图片
imagepng($im, null, 9);
imagedestroy($im);


//php.net上找的函数,感谢
function calculateTextBox($font_size, $font_angle, $font_file, $text)
{
    $box = imagettfbbox($font_size, $font_angle, $font_file, $text);
    if (!$box)
        return false;
    $min_x = min(array($box[0], $box[2], $box[4], $box[6]));
    $max_x = max(array($box[0], $box[2], $box[4], $box[6]));
    $min_y = min(array($box[1], $box[3], $box[5], $box[7]));
    $max_y = max(array($box[1], $box[3], $box[5], $box[7]));
    $width = ($max_x - $min_x);
    $height = ($max_y - $min_y);
    $left = abs($min_x) + $width;
    $top = abs($min_y) + $height;
    // to calculate the exact bounding box i write the text in a large image
    $img = @imagecreatetruecolor($width << 2, $height << 2);
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $black);
    // for sure the text is completely in the image!
    imagettftext($img, $font_size,
        $font_angle, $left, $top,
        $white, $font_file, $text);
    // start scanning (0=> black => empty)
    $rleft = $w4 = $width << 2;
    $rright = 0;
    $rbottom = 0;
    $rtop = $h4 = $height << 2;
    for ($x = 0; $x < $w4; $x++)
        for ($y = 0; $y < $h4; $y++)
            if (imagecolorat($img, $x, $y)) {
                $rleft = min($rleft, $x);
                $rright = max($rright, $x);
                $rtop = min($rtop, $y);
                $rbottom = max($rbottom, $y);
            }
    // destroy img and serve the result
    imagedestroy($img);
    return array("left" => $left - $rleft,
        "top" => $top - $rtop,
        "width" => $rright - $rleft + 1,
        "height" => $rbottom - $rtop + 1);
}
