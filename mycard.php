<?php
/**
 * Created by PhpStorm.
 * User: Saud
 * Date: 15/10/16
 * Time: 10:27
 */

require_once "php/ImageCrop.php";


$doc = new DOMDocument();
$doc->load('xml/resource.xml');
$cards = $doc->getElementsByTagName("card");

//解析haed标签
$haed = $doc->getElementsByTagName("haed");
$str_haed = $haed->item(0)->nodeValue;
$str_haed = shanchu($str_haed);

//解析pinyin标签
$pinyin = $doc->getElementsByTagName("pinyin");
$str_pinyin = $pinyin->item(0)->nodeValue;
$str_pinyin = shanchu($str_pinyin);


//画背景
$im = imagecreatetruecolor(750, 7000);
$bgcolor = imagecolorallocate($im, 255, 255, 255);
imagefill($im, 0, 0, $bgcolor);

//生成汉字的背景矩形
$image_width = 694;
$image_height = 368;
//矩形上面加圆角
$radius = 10;
$dst_x = 28;
$y = 40;
$w = 750;

imagebackgroundmycard($im, $dst_x, $y, $image_width, $image_height, $radius);
$y += $image_height;
//画田字格
$src = 'img/ic_img_tianzg.png';
imageresource($im, $src, ($w - 240) / 2, 108, 240, 240);

//田字格中的字
imagetext($im, $w, 120, $str_haed);

//喇叭和拼音
$yin = '[ ' . $str_pinyin . ' ]';
$y += 20;
$dst_y = imagetextcenter($im, $w, $y, $yin);
$y += 60;
$i = 0;

foreach ($cards as $card) {

    $i++;

    $titles = $card->getElementsByTagName("title");
    if ($titles->length > 0) {
        $title = $titles->item(0)->nodeValue;
        $title = shanchu($title);
    }
    $images = $card->getElementsByTagName("image");
    if ($images->length > 0) {
        $image = $images->item(0)->nodeValue;
        $image = shanchu($image);
    }

    $lis = $card->getElementsByTagName("li");
    if ($lis->length > 0) {
        $li = $lis->item(0)->nodeValue;
        $li = shanchu($li);
    }

    $books = $card->getElementsByTagName("book");
    if ($books->length > 0) {
        $book = $books->item(0)->nodeValue;
        $book = shanchu($book);
    }

    $myshi = $card->getElementsByTagName("shi");
    if ($myshi->length > 0) {
        $shii = $myshi->item(0)->nodeValue;
        $shii = shanchu($shii);
    }
    $cis = $card->getElementsByTagName("ci");
    if ($cis->length > 0) {
        $ci = $cis->item(0)->nodeValue;
        $ci = shanchu($ci);
    }

//测量卡片解析的内容占用的高度
    $card_h = 35;
    if ($titles->length > 0) {
        $title_size = titlesize($im, $title);
        $card_h += $title_size + 35;
    } else {
        $title_size = 0;
    }

    if ($images->length > 0) {
        $images_size = 288;
        $card_h += $images_size + 40;
    } else {
        $images_size = 0;
    }

    if ($lis->length > 0) {
        $li_size = lisize($im, $li);
        $card_h += $li_size;
    } else {
        $li_size = 0;
    }

    if ($books->length > 0) {
        $book_size = booksize($im, $book);
        $card_h += $book_size;
    } else {
        $book_size = 0;
    }
    if ($myshi->length > 0) {
        $shii_size = shiisize($im, $shii);
        $card_h += $shii_size;
    } else {
        $shii_size = 0;
    }
    if ($cis->length > 0) {
        $ci_size = cisize($im, $ci);
        $card_h += $ci_size;
    } else {
        $ci_size = 0;
    }
    $card_h += 40;


    //画图
    draw_card($im, $dst_x, $y, $image_width, $card_h, $radius);
    draw_num($im, $i, $y - 29);


    $temp_y = $y + 35;
    if ($titles->length > 0) {
        draw_title($im, $title, $temp_y);
        $temp_y += $title_size + 35;
    }

    if ($images->length > 0) {
        draw_images($im, $image, $temp_y, $radius);
        $temp_y += $images_size + 40;
    }

    if ($lis->length > 0) {
        draw_li($im, $temp_y, $li);
        $temp_y += $li_size;
    }

    if ($books->length > 0) {
        draw_book($im, $temp_y, $book);
        $temp_y += $book_size;
    }

    if ($myshi->length > 0) {
        draw_shii($im, $temp_y, $shii);
        $temp_y += $shii_size;
    }

    if ($cis->length > 0) {
        draw_ci($im, $temp_y, $ci);
    }

    $y += $card_h + 40;

}

//解析“知”标签
$zhi = $doc->getElementsByTagName("zhi");
if ($zhi->length > 0) {
    $str_zhi = $zhi->item(0)->nodeValue;
    $str_zhi = shanchu($str_zhi);

    //测量字的高度：
    $size = zhi_cheng_size($im, $str_zhi, $y + 35, false);
    //绘制
    draw_card($im, $dst_x, $y, $image_width, $size + 90, $radius);
    $src_zhi = "img/ic_img_xuhao_zhi.png";
    draw_zhi_cheng($im, $src_zhi, $y - 29);
    zhi_cheng_size($im, $str_zhi, $y + 35, true);

    $y = $y + $size + 90 + 40;


}
//解析“成”标签
$cheng = $doc->getElementsByTagName("cheng");
if ($cheng->length > 0) {
    $str_cheng = $cheng->item(0)->nodeValue;
    $str_cheng = shanchu($str_cheng);

    //测量字的高度：
    $size = zhi_cheng_size($im, $str_cheng, $y + 35, false);
    //绘制
    draw_card($im, $dst_x, $y, $image_width, $size + 90, $radius);
    $src_cheng = "img/ic_img_xuhao_cheng.png";
    draw_zhi_cheng($im, $src_cheng, $y - 29);
    zhi_cheng_size($im, $str_cheng, $y + 35, true);

    $y = $y + $size + 90 + 40;

}
//绘制二维码
draw_QR($im, $y);

$y = $y + 307 + 40;

//生成图片
imagepng($im, $str_haed . ".png");
imagedestroy($im);

//图片剪裁适合的高度
$new_image = imagecreatetruecolor(750, $y);
$src = imagecreatefromstring(file_get_contents($str_haed . ".png"));
imagecopyresampled($new_image, $src, 0, 0, 0, 0, 750, $y, 750, $y);
header('Content-Type: image/png');
imagepng($new_image, $str_haed . ".png");
imagedestroy($src);
imagedestroy($new_image);


//*****************************************以下为函数方法区***************************************************

function draw_ci($im, $y, $ci)
{
    $src = "img/ic_img_shiyi.png";
    $img = imagecreatefrompng($src);
    imagecopy($im, $img, 100, $y, 0, 0, 54, 54);
    return drawmintext($im, $y, $ci);
}

function draw_shii($im, $y, $shii)
{
    $src = "img/ic_img_shiyi.png";
    $img = imagecreatefrompng($src);
    imagecopy($im, $img, 100, $y, 0, 0, 54, 54);
    return drawmintext($im, $y, $shii);
}


function draw_book($im, $y, $li)
{
    $li = "——  " . $li;
    return drawmintext($im, $y, $li);

}

/**
 * 获取文件名
 * @param $url
 * @return mixed
 */
function retrieve($url)
{
    $path = parse_url($url);
    $str = explode('.', $path['path']);
    return $str[0];
}

/**
 * 绘制图片
 * @param $im
 * @param $src
 * @param $y
 * @param $radius
 */
function draw_images($im, $src, $y, $radius)
{

    //裁图
    $newsrc = "res/" . $src;
    $filename = retrieve($newsrc);
    $ic = new ImageCrop($newsrc, $filename);
    $ic->Crop(560, 288, 1);
    $ic->SaveAlpha();
    $ic->destory();

    $img = imagecreatefrompng($filename . ".png");

    imagecopy($im, $img, 100, $y, 0, 0, 560, 288);
    //画圆角
    $lt_corner = get_lt_rounder_corner($radius, 0xef, 0xef, 0xe1);//圆角的背景色
    myradus($im, 100, $y, $lt_corner, $radius, 288, 560);

}

/**
 * 绘制二维码
 * @param $im
 * @param $src
 * @param $y
 * @param $radius
 */
function draw_QR($im, $y)
{
    $src = "img/ic_qr.png";
    $img = imagecreatefrompng($src);
    imagecopy($im, $img, 0, $y, 0, 0, 750, 307);


}


/**
 *
 * 画浅色的背景card
 * @param $im
 * @param $dst_x
 * @param $y
 * @param $image_width
 * @param $image_height
 * @param $radius
 */
function draw_card($im, $dst_x, $y, $image_width, $image_height, $radius)
{

    imagebackgroundmycard($im, $dst_x, $y, $image_width, $image_height, $radius);
}


/**
 * 画左边的小标记
 * @param $im
 * @param $num
 * @param $y
 */
function draw_num($im, $num, $y)
{

    $ic_num = "img/ic_img_xuhao" . $num . ".png";
    imageresource($im, $ic_num, 5, $y, 72, 72);

}

/**
 * 画左边的小标记
 * @param $im
 * @param $num
 * @param $y
 */
function draw_zhi_cheng($im, $src, $y)
{

    imageresource($im, $src, 5, $y, 72, 72);

}

/**
 * 测量语句的出处的大小
 * @param $im
 * @param $str
 * @return int
 */
function booksize($im, $str)
{

    $fontsize = 27;
    $hang_size = 50;
    $temp = array("color" => array(55, 55, 55), "fontsize" => $fontsize, "width" => 496, "left" => 100, "top" => 0, "hang_size" => $hang_size);
    return draw_txt_to($im, $temp, $str, false);
}

/**
 * 测量“词”的大小
 * @param $im
 * @param $str
 * @return int
 */
function cisize($im, $str)
{
    return lisize($im, $str);
}

/**
 * 测量"释"的大小
 * @param $im
 * @param $str
 * @return int
 */
function shiisize($im, $str)
{
    return lisize($im, $str);
}


/**
 * 绘制“例”的图标和内容
 * @param $im
 * @param $y
 * @param $li
 * @return int
 */
function draw_li($im, $y, $li)
{

    $src = "img/ic_img_lizi.png";
    $img = imagecreatefrompng($src);
    imagecopy($im, $img, 100, $y, 0, 0, 54, 54);
    return drawmintext($im, $y, $li);

}

/**
 * 绘制“例”“释”等汉字
 * @param $im
 * @param $y
 * @param $li
 * @return int
 */
function drawmintext($im, $y, $li)
{
    $fontsize = 23;
    $hang_size = 50;
    $temp = array("color" => array(77, 77, 77), "fontsize" => $fontsize, "width" => 496, "left" => 164, "top" => $y - 10, "hang_size" => $hang_size);
    return draw_txt_to($im, $temp, $li, true);
}

/**
 * 测量“例”的大小
 * @param $im
 * @param $str
 * @return int
 */
function lisize($im, $str)
{
    $fontsize = 23;
    $hang_size = 50;
    $temp = array("color" => array(55, 55, 55), "fontsize" => $fontsize, "width" => 496, "left" => 100, "top" => 0, "hang_size" => $hang_size);
    $h = draw_txt_to($im, $temp, $str, false);
    return $h > 54 ? $h : 54;
}

/**
 * 测量“例”的大小
 * @param $im
 * @param $str
 * @return int
 */
function zhi_cheng_size($im, $str, $top, $isdraw)
{
    $fontsize = 23;
    $hang_size = 50;
    $temp = array("color" => array(55, 55, 55), "fontsize" => $fontsize, "width" => 560, "left" => 100, "top" => $top, "hang_size" => $hang_size);
    return draw_txt_to($im, $temp, $str, $isdraw);

}

/**
 * 画title文字
 * @param $im
 * @param $title
 * @param $y
 * @return int
 */
function draw_title($im, $title, $y)
{


    $fontsize = 26;
    $hang_size = 50;
    $temp = array("color" => array(11, 11, 11), "fontsize" => $fontsize, "width" => 560, "left" => 100, "top" => $y, "hang_size" => $hang_size);
    return draw_txt_to($im, $temp, $title, true);

}

/**
 * 测量“头”的大小
 * @param $im
 * @param $str
 * @return int
 */
function  titlesize($im, $title)
{

    $fontsize = 26;
    $hang_size = 50;
    $temp = array("color" => array(55, 55, 55), "fontsize" => $fontsize, "width" => 560, "left" => 100, "top" => 0, "hang_size" => $hang_size);
    return draw_txt_to($im, $temp, $title, false);
}


/**
 * 文字自动换行算法
 * @param $card 画板
 * @param $pos 数组，top距离画板顶端的距离，fontsize文字的大小，width宽度，left距离左边的距离，hang_size行高
 * @param $str 要写的字符串
 * @param $iswrite  是否输出，ture，花出文字，false只计算占用的高度
 * @return int 返回整个字符所占用的高度
 */
function draw_txt_to($card, $pos, $str, $iswrite)
{

    $_str_h = $pos["top"];
    $fontsize = $pos["fontsize"];
    $width = $pos["width"];
    $margin_lift = $pos["left"];
    $hang_size = $pos["hang_size"];
    $temp_string = "";
    $font_file = "./Fonts/华文细黑.ttf";
    $tp = 0;

    $font_color = imagecolorallocate($card, $pos["color"][0], $pos["color"][1], $pos["color"][2]);
    for ($i = 0; $i < mb_strlen($str); $i++) {

        $box = imagettfbbox($fontsize, 0, $font_file, $temp_string);
        $_string_length = $box[2] - $box[0];
        $temptext = mb_substr($str, $i, 1);

        $temp = imagettfbbox($fontsize, 0, $font_file, $temptext);

        if ($_string_length + $temp[2] - $temp[0] < $width) {//长度不够，字数不够，需要

            //继续拼接字符串。

            $temp_string .= mb_substr($str, $i, 1);

            if ($i == mb_strlen($str) - 1) {//是不是最后半行。不满一行的情况
                $_str_h += $hang_size;//计算整个文字换行后的高度。
                $tp++;//行数
                if ($iswrite) {//是否需要写入，核心绘制函数
                    imagettftext($card, $fontsize, 0, $margin_lift, $_str_h, $font_color, $font_file, $temp_string);
                }

            }
        } else {//一行的字数够了，长度够了。

//            打印输出，对字符串零时字符串置null
            $texts = mb_substr($str, $i, 1);//零时行的开头第一个字。

//            判断默认第一个字符是不是符号；
            $isfuhao = preg_match("/[\\pP]/u", $texts) ? true : false;//一行的开头这个字符，是不是标点符号
            if ($isfuhao) {//如果是标点符号，则添加在第一行的结尾
                $temp_string .= $texts;

//                判断如果是连续两个字符出现，并且两个丢失必须放在句末尾的，单独处理
                $f = mb_substr($str, $i + 1, 1);
                $fh = preg_match("/[\\pP]/u", $f) ? true : false;
                if ($fh) {
                    $temp_string .= $f;
                    $i++;
                }

            } else {
                $i--;
            }

            $tmp_str_len = mb_strlen($temp_string);
            $s = mb_substr($temp_string, $tmp_str_len-1, 1);//取零时字符串最后一位字符

                if (is_firstfuhao($s)) {//判断零时字符串的最后一个字符是不是可以放在见面
                    //讲最后一个字符用“_”代替。指针前移动一位。重新取被替换的字符。
                    $temp_string=rtrim($temp_string,$s);
                    $i--;
                }
//            }



//            计算行高，和行数。
            $_str_h += $hang_size;
            $tp++;
            if ($iswrite) {

                imagettftext($card, $fontsize, 0, $margin_lift, $_str_h, $font_color, $font_file, $temp_string);
            }
//           写完了改行，置null该行的临时字符串。
            $temp_string = "";
        }
    }

    return $tp * $hang_size;

}


function is_firstfuhao($str)
{
    $fuhaos = array("\"", "“", "'", "<", "《",);

    return in_array($str, $fuhaos);

}

/**
 * 喇叭和拼音
 * @param $im 总图
 * @param $w 总图宽
 * @param $str 拼音
 */
function imagetextcenter($im, $w, $y, $str)
{
    //画小喇叭和拼音
    $font = "./Fonts/华文黑体.ttf";
    $imgtext = imgtextsize(24, $font, $str);
    $fontcolor = imagecolorallocate($im, 33, 33, 33);
    $laba = imagecreatefrompng('./img/ic_img_laba.png');
    imagecopy($im, $laba, ($w - $imgtext["width"]) / 2, $y, 0, 0, 42, 42);
    imagettftext($im, 24, 0, ($w - $imgtext["width"]) / 2 + 42 + 10, 424 + 4 + $imgtext["height"], $fontcolor, $font, $str);

    return $imgtext["height"];
}


/**
 * 测量图片和字一起的大小
 * @param $imgsize
 * @param $font
 * @param $str
 * @return array [width,height]
 */
function imgtextsize($imgsize, $font, $str)
{

    $bbox = imagettfbbox($imgsize, 0, $font, $str);
    $yin_w = $bbox[2] - $bbox[0];
    $yin_h = $bbox[1] - $bbox[5];
    $all_w = 42 + 20 + $yin_w;//算总宽
    return array("width" => $all_w, "height" => $yin_h);

}


/**除去空格回车
 * @param $str
 * @return mixed|string
 */
function shanchu($str)
{
    $str = trim($str);
    $str = preg_replace('/\n/', '', $str);
    $str = preg_replace('/\r/', '', $str);
    return $str;
}

/**
 * 写一个居中的字
 * @param $im 字相对于最大的一个图
 * @param $w    最大图的大小
 * @param $size 字体大小
 * @param $font 字体样式
 * @param $str 这些字
 */
function imagetext($im, $w, $size, $str)
{
    //往上写汉字
    $font = "./Fonts/华文细黑.ttf";
    $fontcolor = imagecolorallocate($im, 33, 33, 33);
    $bbox = imagettfbbox($size, 0, $font, $str);
    $font_w = $bbox[2] - $bbox[0];
    $font_h = $bbox[1] - $bbox[5];
    imagettftext($im, $size, 0, ($w - $font_w) / 2 - 12, 108 + $font_h + (240 - $font_h) / 2 - $bbox[1], $fontcolor, $font, $str);
}

/**
 * 讲资源文件图片画到指定位置
 * @param $im
 * @param $src
 * @param $dst_x
 * @param $dst_y
 * @param $image_w
 * @param $image_h
 */
function imageresource($im, $src, $dst_x, $dst_y, $image_w, $image_h)
{
    $tianzg = imagecreatefrompng($src);
    imagecopy($im, $tianzg, $dst_x, $dst_y, 0, 0, $image_w, $image_h);

}

/**
 * 画一个带圆角的背景图
 * @param $im  底图
 * @param $dst_x 画出的图的（0，0）位于底图的x轴位置
 * @param $dst_y 画出的图的（0，0）位于底图的y轴位置
 * @param $image_w 画的图的宽
 * @param $image_h 画的图的高
 * @param $radius 圆角的值
 */
function imagebackgroundmycard($im, $dst_x, $dst_y, $image_w, $image_h, $radius)
{
    $resource = imagecreatetruecolor($image_w, $image_h);
    $bgcolor = imagecolorallocate($resource, 0xef, 0xef, 0xe1);//该图的背景色
    imagefill($resource, 0, 0, $bgcolor);
    $lt_corner = get_lt_rounder_corner($radius, 255, 255, 255);//圆角的背景色
    // lt(左上角)
    imagecopymerge($resource, $lt_corner, 0, 0, 0, 0, $radius, $radius, 100);
    // lb(左下角)
    $lb_corner = imagerotate($lt_corner, 90, 0);
    imagecopymerge($resource, $lb_corner, 0, $image_h - $radius, 0, 0, $radius, $radius, 100);
    // rb(右上角)
    $rb_corner = imagerotate($lt_corner, 180, 0);
    imagecopymerge($resource, $rb_corner, $image_w - $radius, $image_h - $radius, 0, 0, $radius, $radius, 100);
    // rt(右下角)
    $rt_corner = imagerotate($lt_corner, 270, 0);
    imagecopymerge($resource, $rt_corner, $image_w - $radius, 0, 0, 0, $radius, $radius, 100);

    imagecopy($im, $resource, $dst_x, $dst_y, 0, 0, $image_w, $image_h);
}


/** 画圆角
 * @param $radius 圆角位置
 * @param $color_r
 * @param $color_g
 * @param $color_b
 * @return resource
 */
function get_lt_rounder_corner($radius, $color_r, $color_g, $color_b)
{
    // 创建一个正方形的图像
    $img = imagecreatetruecolor($radius, $radius);
    // 图像的背景
    $bgcolor = imagecolorallocate($img, $color_r, $color_g, $color_b);
    $fgcolor = imagecolorallocate($img, 0, 0, 0);
    imagefill($img, 0, 0, $bgcolor);
    // $radius,$radius：以图像的右下角开始画弧
    // $radius*2, $radius*2：已宽度、高度画弧
    // 180, 270：指定了角度的起始和结束点
    // fgcolor：指定颜色
    imagefilledarc($img, $radius, $radius, $radius * 2, $radius * 2, 180, 270, $fgcolor, IMG_ARC_PIE);
    // 将弧角图片的颜色设置为透明
    imagecolortransparent($img, $fgcolor);
    return $img;
}

/**
 * @param $im
 * @param $lt_corner
 * @param $radius
 * @param $image_h
 * @param $image_w
 */
function myradus($im, $lift, $top, $lt_corner, $radius, $image_h, $image_w)
{
/// lt(左上角)
    imagecopymerge($im, $lt_corner, $lift, $top, 0, 0, $radius, $radius, 100);
// lb(左下角)
    $lb_corner = imagerotate($lt_corner, 90, 0);
    imagecopymerge($im, $lb_corner, $lift, $image_h - $radius + $top, 0, 0, $radius, $radius, 100);
// rb(右上角)
    $rb_corner = imagerotate($lt_corner, 180, 0);
    imagecopymerge($im, $rb_corner, $image_w + $lift - $radius, $image_h + $top - $radius, 0, 0, $radius, $radius, 100);
// rt(右下角)
    $rt_corner = imagerotate($lt_corner, 270, 0);
    imagecopymerge($im, $rt_corner, $image_w - $radius + $lift, $top, 0, 0, $radius, $radius, 100);
}


?>