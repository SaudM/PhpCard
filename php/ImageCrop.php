<?php

/**
 * Created by PhpStorm.
 * User: Saud
 * Date: 15/10/26
 * Time: 10:17
 * mode 1 : 强制裁剪，生成图片严格按照需要，不足放大，超过裁剪，图片始终铺满
 * mode 2 : 和1类似，但不足的时候 不放大 会产生补白，可以用png消除。
 * mode 3 : 只缩放，不裁剪，保留全部图片信息，会产生补白，
 * mode 4 : 只缩放，不裁剪，保留全部图片信息，生成图片大小为最终缩放后的图片有效信息的实际大小，不产生补白
 * 默认补白为白色，如果要使补白成透明像素，请使用SaveAlpha()方法代替SaveImage()方法
 *
 * 调用方法：
 *
 * $ic=new ImageCrop('old.jpg','afterCrop.jpg');
 * $ic->Crop(120,80,2);
 * $ic->SaveImage();
 *        //$ic->SaveAlpha();将补白变成透明像素保存
 * $ic->destory();
 *
 *
 */
class ImageCrop
{

    private GdImage $sImage;
    private ?GdImage $dImage = null;
    private string $src_file;
    private string $dst_file;
    private int $src_width;
    private int $src_height;
    private int $src_type;
    private string $src_ext;

    public function __construct(string $src_file, string $dst_file = '')
    {
        $this->src_file = $src_file;
        $this->dst_file = $dst_file;
        $this->loadImage();
    }

    /**
     * 设置源文件
     *
     * @param string $src_file 源文件路径
     */
    public function setSrcFile(string $src_file): void
    {
        $this->src_file = $src_file;
        $this->loadImage();
    }

    /**
     * 设置目标文件
     *
     * @param string $dst_file 目标文件路径
     */
    public function setDstFile(string $dst_file): void
    {
        $this->dst_file = $dst_file;
    }


    /**
     * 加载图像并初始化相关属性
     *
     * @throws InvalidArgumentException 如果图像类型不支持
     * @throws RuntimeException 如果加载图像失败
     */
    private function loadImage(): void
    {
        $imageInfo = getimagesize($this->src_file);
        if ($imageInfo === false) {
            throw new RuntimeException("无法获取图像尺寸：{$this->src_file}");
        }

        [$this->src_width, $this->src_height, $this->src_type] = $imageInfo;

        $this->sImage = match ($this->src_type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($this->src_file),
            IMAGETYPE_PNG => imagecreatefrompng($this->src_file),
            IMAGETYPE_GIF => imagecreatefromgif($this->src_file),
            default => throw new InvalidArgumentException("不支持的图像类型：{$this->src_type}"),
        };

        $this->src_ext = match ($this->src_type) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_GIF => 'gif',
        };
    }

    /**
     * 保存图像到指定文件
     *
     * @param string $fileName 目标文件名（可选）
     * @throws RuntimeException 如果保存图像失败
     */
    public function saveImage(string $fileName = ''): void
    {
        $this->dst_file = $fileName ?: $this->dst_file;

        if (!$this->dImage) {
            throw new RuntimeException("目标图像未创建");
        }

        match ($this->src_type) {
            IMAGETYPE_JPEG => imagejpeg($this->dImage, $this->dst_file, 100),
            IMAGETYPE_PNG => imagepng($this->dImage, $this->dst_file),
            IMAGETYPE_GIF => imagegif($this->dImage, $this->dst_file),
            default => throw new RuntimeException("无法保存图像：不支持的图像类型"),
        };
    }

    /**
     * 输出图像到浏览器
     *
     * @throws RuntimeException 如果输出图像失败
     */
    public function outImage(): void
    {
        if (!$this->dImage) {
            throw new RuntimeException("目标图像未创建");
        }

        match ($this->src_type) {
            IMAGETYPE_JPEG => header('Content-Type: image/jpeg') && imagejpeg($this->dImage),
            IMAGETYPE_PNG => header('Content-Type: image/png') && imagepng($this->dImage),
            IMAGETYPE_GIF => header('Content-Type: image/gif') && imagegif($this->dImage),
            default => throw new RuntimeException("无法输出图像：不支持的图像类型"),
        };
    }

    /**
     * 保存图像为带透明通道的 PNG
     *
     * @param string $fileName 目标文件名（可选）
     * @throws RuntimeException 如果保存图像失败
     */
    public function saveAlpha(string $fileName = ''): void
    {
        $this->dst_file = $fileName ? "{$fileName}.png" : "{$this->dst_file}.png";

        if (!$this->dImage) {
            throw new RuntimeException("目标图像未创建");
        }

        imagesavealpha($this->dImage, true);
        imagepng($this->dImage, $this->dst_file);
    }

    /**
     * 输出图像为带透明通道的 PNG
     *
     * @throws RuntimeException 如果输出图像失败
     */
    public function outAlpha(): void
    {
        if (!$this->dImage) {
            throw new RuntimeException("目标图像未创建");
        }

        imagesavealpha($this->dImage, true);
        header('Content-Type: image/png');
        imagepng($this->dImage);
    }

    /**
     * 销毁图像资源
     */
    public function destroy(): void
    {
        if (isset($this->sImage) && is_resource($this->sImage)) {
            imagedestroy($this->sImage);
        }
        if (isset($this->dImage) && is_resource($this->dImage)) {
            imagedestroy($this->dImage);
        }
    }

    /**
     * 裁剪图像
     *
     * @param int $dst_width 目标宽度
     * @param int $dst_height 目标高度
     * @param int $mode 裁剪模式
     * @param string $dst_file 目标文件名（可选）
     * @throws InvalidArgumentException 如果裁剪模式无效
     */
    public function crop(int $dst_width, int $dst_height, int $mode = 1, string $dst_file = ''): void
    {
        if ($dst_file) {
            $this->dst_file = $dst_file;
        }

        $this->dImage = imagecreatetruecolor($dst_width, $dst_height);
        if (!$this->dImage) {
            throw new RuntimeException("无法创建目标图像");
        }

        $bg = imagecolorallocatealpha($this->dImage, 255, 255, 255, 127);
        imagefill($this->dImage, 0, 0, $bg);
        imagecolortransparent($this->dImage, $bg);

        $ratio_w = $dst_width / $this->src_width;
        $ratio_h = $dst_height / $this->src_height;
        $ratio = 1.0;

        switch ($mode) {
            case 1: // 强制裁剪
                if (($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) {
                    $ratio = $ratio_w < $ratio_h ? $ratio_h : $ratio_w;
                    $tmp_w = (int)($dst_width / $ratio);
                    $tmp_h = (int)($dst_height / $ratio);
                    $tmp_img = imagecreatetruecolor($tmp_w, $tmp_h);
                    if (!$tmp_img) {
                        throw new RuntimeException("无法创建临时图像");
                    }
                    $src_x = (int)(($this->src_width - $tmp_w) / 2);
                    $src_y = (int)(($this->src_height - $tmp_h) / 2);
                    imagecopy($tmp_img, $this->sImage, 0, 0, $src_x, $src_y, $tmp_w, $tmp_h);
                    imagecopyresampled($this->dImage, $tmp_img, 0, 0, 0, 0, $dst_width, $dst_height, $tmp_w, $tmp_h);
                    imagedestroy($tmp_img);
                } else {
                    $ratio = $ratio_w < $ratio_h ? $ratio_h : $ratio_w;
                    $tmp_w = (int)($this->src_width * $ratio);
                    $tmp_h = (int)($this->src_height * $ratio);
                    $tmp_img = imagecreatetruecolor($tmp_w, $tmp_h);
                    if (!$tmp_img) {
                        throw new RuntimeException("无法创建临时图像");
                    }
                    imagecopyresampled($tmp_img, $this->sImage, 0, 0, 0, 0, $tmp_w, $tmp_h, $this->src_width, $this->src_height);
                    $src_x = (int)(($tmp_w - $dst_width) / 2);
                    $src_y = (int)(($tmp_h - $dst_height) / 2);
                    imagecopy($this->dImage, $tmp_img, 0, 0, $src_x, $src_y, $dst_width, $dst_height);
                    imagedestroy($tmp_img);
                }
                break;

            case 2: // 仅在缩小时裁剪
                if ($ratio_w < 1 && $ratio_h < 1) {
                    $ratio = $ratio_w < $ratio_h ? $ratio_h : $ratio_w;
                    $tmp_w = (int)($dst_width / $ratio);
                    $tmp_h = (int)($dst_height / $ratio);
                    $tmp_img = imagecreatetruecolor($tmp_w, $tmp_h);
                    if (!$tmp_img) {
                        throw new RuntimeException("无法创建临时图像");
                    }
                    $src_x = (int)(($this->src_width - $tmp_w) / 2);
                    $src_y = (int)(($this->src_height - $tmp_h) / 2);
                    imagecopy($tmp_img, $this->sImage, 0, 0, $src_x, $src_y, $tmp_w, $tmp_h);
                    imagecopyresampled($this->dImage, $tmp_img, 0, 0, 0, 0, $dst_width, $dst_height, $tmp_w, $tmp_h);
                    imagedestroy($tmp_img);
                } elseif ($ratio_w > 1 && $ratio_h > 1) {
                    $dst_x = (int)(abs($dst_width - $this->src_width) / 2);
                    $dst_y = (int)(abs($dst_height - $this->src_height) / 2);
                    imagecopy($this->dImage, $this->sImage, $dst_x, $dst_y, 0, 0, $this->src_width, $this->src_height);
                } else {
                    $src_x = 0;
                    $dst_x = 0;
                    $src_y = 0;
                    $dst_y = 0;

                    if (($dst_width - $this->src_width) < 0) {
                        $src_x = (int)(($this->src_width - $dst_width) / 2);
                        $dst_x = 0;
                    } else {
                        $src_x = 0;
                        $dst_x = (int)(($dst_width - $this->src_width) / 2);
                    }

                    if (($dst_height - $this->src_height) < 0) {
                        $src_y = (int)(($this->src_height - $dst_height) / 2);
                        $dst_y = 0;
                    } else {
                        $src_y = 0;
                        $dst_y = (int)(($dst_height - $this->src_height) / 2);
                    }

                    imagecopy($this->dImage, $this->sImage, $dst_x, $dst_y, $src_x, $src_y, $this->src_width, $this->src_height);
                }
                break;

            case 3: // 仅缩放，不裁剪，保留全部图像信息，会产生补白
                if ($ratio_w > 1 && $ratio_h > 1) {
                    $dst_x = (int)(abs($dst_width - $this->src_width) / 2);
                    $dst_y = (int)(abs($dst_height - $this->src_height) / 2);
                    imagecopy($this->dImage, $this->sImage, $dst_x, $dst_y, 0, 0, $this->src_width, $this->src_height);
                } else {
                    $ratio = $ratio_w > $ratio_h ? $ratio_h : $ratio_w;
                    $tmp_w = (int)($this->src_width * $ratio);
                    $tmp_h = (int)($this->src_height * $ratio);
                    $tmp_img = imagecreatetruecolor($tmp_w, $tmp_h);
                    if (!$tmp_img) {
                        throw new RuntimeException("无法创建临时图像");
                    }
                    imagecopyresampled($tmp_img, $this->sImage, 0, 0, 0, 0, $tmp_w, $tmp_h, $this->src_width, $this->src_height);
                    $dst_x = (int)(abs($tmp_w - $dst_width) / 2);
                    $dst_y = (int)(abs($tmp_h - $dst_height) / 2);
                    imagecopy($this->dImage, $tmp_img, $dst_x, $dst_y, 0, 0, $tmp_w, $tmp_h);
                    imagedestroy($tmp_img);
                }
                break;

            case 4: // 仅缩放，不裁剪，保留全部图像信息，生成图片大小为最终缩放后的图像实际大小，不产生补白
                if ($ratio_w > 1 && $ratio_h > 1) {
                    $this->dImage = imagecreatetruecolor($this->src_width, $this->src_height);
                    if (!$this->dImage) {
                        throw new RuntimeException("无法创建目标图像");
                    }
                    imagecopy($this->dImage, $this->sImage, 0, 0, 0, 0, $this->src_width, $this->src_height);
                } else {
                    $ratio = $ratio_w > $ratio_h ? $ratio_h : $ratio_w;
                    $tmp_w = (int)($this->src_width * $ratio);
                    $tmp_h = (int)($this->src_height * $ratio);
                    $this->dImage = imagecreatetruecolor($tmp_w, $tmp_h);
                    if (!$this->dImage) {
                        throw new RuntimeException("无法创建目标图像");
                    }
                    imagecopyresampled($this->dImage, $this->sImage, 0, 0, 0, 0, $tmp_w, $tmp_h, $this->src_width, $this->src_height);
                }
                break;

            default:
                throw new InvalidArgumentException("无效的裁剪模式：{$mode}");
        }
    }
}
?>