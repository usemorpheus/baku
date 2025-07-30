<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateImage
{
    use AsAction;

    public function handle($title, $text): string
    {
        try {
            // 获取动态文本参数
            $fontSize        = 14;
            $textColor       = '#000000';
            $backgroundColor = '#FDF1E5';
            $quality         = 90;  // 图片质量 0-100
            $scale           = 1;   // 高分辨率缩放倍数

            $titleFontSize = 18;

            // 图片路径配置
            $topImagePath    = public_path('images/top.png');
            $bottomImagePath = public_path('images/bottom.png');
            $fontPath        = public_path('fonts/arial.ttc');

            // 检查必要文件是否存在
            if (!file_exists($topImagePath) || !file_exists($bottomImagePath)) {
                return response()->json(['error' => '顶部或底部图片文件不存在'], 404);
            }

            // 检查GD库是否可用
            if (!extension_loaded('gd')) {
                return response()->json(['error' => 'GD库未安装'], 500);
            }

            // 加载顶部和底部图片
            $topImage    = $this->loadHighQualityImage($topImagePath);
            $bottomImage = $this->loadHighQualityImage($bottomImagePath);

            if (!$topImage || !$bottomImage) {
                return response()->json(['error' => '图片加载失败'], 500);
            }

            // 获取原始图片尺寸
            $originalWidth        = max(imagesx($topImage), imagesx($bottomImage));
            $originalTopHeight    = imagesy($topImage);
            $originalBottomHeight = imagesy($bottomImage);

            // 应用高分辨率缩放
            $width               = (int)($originalWidth * $scale);
            $topHeight           = (int)($originalTopHeight * $scale);
            $bottomHeight        = (int)($originalBottomHeight * $scale);
            $scaledFontSize      = (int)($fontSize * $scale);
            $titleScaledFontSize = (int)($titleFontSize * $scale);

            // 缩放顶部和底部图片
            $scaledTopImage    = $this->resizeImageHighQuality($topImage, $width, $topHeight);
            $scaledBottomImage = $this->resizeImageHighQuality($bottomImage, $width, $bottomHeight);

            // 计算文本区域高度
            $titleHeight    = $this->calculateTextHeight($title, $titleScaledFontSize, $width, $fontPath);
            $textAreaHeight = $this->calculateTextHeight($text, $scaledFontSize, $width, $fontPath);
            $padding        = (int)(40 * $scale);
            $middleHeight   = max($titleHeight + $textAreaHeight + $padding * 3, (int)(100 * $scale));

            // 创建高分辨率最终图片
            $totalHeight = $topHeight + $middleHeight + $bottomHeight;
            $finalImage  = imagecreatetruecolor($width, $totalHeight);

            // 启用抗锯齿
            if (function_exists('imageantialias')) {
                imageantialias($finalImage, true);
            }

            // 设置高质量背景色
            $bgColor         = $this->hexToRgb($backgroundColor);
            $bgColorResource = imagecolorallocate($finalImage, $bgColor['r'], $bgColor['g'], $bgColor['b']);
            imagefill($finalImage, 0, 0, $bgColorResource);

            // 高质量拼接顶部图片
            imagecopyresampled($finalImage, $scaledTopImage, 0, 0, 0, 0,
                $width, $topHeight, imagesx($scaledTopImage), imagesy($scaledTopImage));

            // 在中间区域添加高质量文本
            $this->addHighQualityTextToImage($finalImage, $title, $titleScaledFontSize, $textColor, $fontPath,
                0, $topHeight, $width, $titleHeight + $padding, $scale);

            $this->addHighQualityTextToImage($finalImage, $text, $scaledFontSize, '#333333', $fontPath,
                0, $topHeight + $titleHeight, $width, $textAreaHeight + $padding * 3, $scale);

            // 高质量拼接底部图片
            imagecopyresampled($finalImage, $scaledBottomImage, 0, $topHeight + $middleHeight, 0, 0,
                $width, $bottomHeight, imagesx($scaledBottomImage), imagesy($scaledBottomImage));

            // add time stampe
            $this->addHighQualityTextToImage($finalImage, date('Y-m-d H:i:s'), $scaledFontSize, '#000000', $fontPath,
                185, $totalHeight - 75, $width, 50, $scale);

            // 如果需要，缩放回原始尺寸（保持高质量）
            if ($scale > 1.0) {
                $outputImage = $this->resizeImageHighQuality($finalImage, $originalWidth,
                    (int)($totalHeight / $scale));
                imagedestroy($finalImage);
                $finalImage = $outputImage;
            }

            // 输出高质量图片
            ob_start();

            // 根据请求的格式输出
            $format = 'jpg';
            switch (strtolower($format)) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($finalImage, null, $quality);
                    $contentType = 'image/jpeg';
                    break;
                case 'webp':
                    if (function_exists('imagewebp')) {
                        imagewebp($finalImage, null, $quality);
                        $contentType = 'image/webp';
                    } else {
                        imagepng($finalImage, null, (int)((100 - $quality) / 11)); // PNG 压缩级别 0-9
                        $contentType = 'image/png';
                    }
                    break;
                default:
                    imagepng($finalImage, null, (int)((100 - $quality) / 11));
                    $contentType = 'image/png';
            }

            $imageData = ob_get_contents();
            ob_end_clean();

            // 清理内存
            imagedestroy($finalImage);
            imagedestroy($topImage);
            imagedestroy($bottomImage);
            imagedestroy($scaledTopImage);
            imagedestroy($scaledBottomImage);

            $file_name = Str::random() . '.' . $format;
            $path      = '/articles/images/' . $file_name;
            file_put_contents(public_path($path), $imageData);
            return url($path);
        } catch (\Exception $e) {
            Log::debug($e);
        }
    }

    /**
     * 加载高质量图片文件
     */
    private function loadHighQualityImage($imagePath)
    {
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            return false;
        }

        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($imagePath);
                // 保持PNG透明度
                imagealphablending($image, false);
                imagesavealpha($image, true);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($imagePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $image = imagecreatefromwebp($imagePath);
                } else {
                    return false;
                }
                break;
            default:
                return false;
        }

        return $image;
    }

    /**
     * 高质量图片缩放
     */
    private function resizeImageHighQuality($sourceImage, $newWidth, $newHeight)
    {
        $originalWidth  = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        // 创建目标图片
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // 保持透明度
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
        imagefill($resizedImage, 0, 0, $transparent);

        // 启用抗锯齿
        if (function_exists('imageantialias')) {
            imageantialias($resizedImage, true);
        }

        // 高质量重采样
        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0,
            $newWidth, $newHeight, $originalWidth, $originalHeight);

        return $resizedImage;
    }

    /**
     * 高质量文本渲染
     */
    private function addHighQualityTextToImage(
        $image,
        $text,
        $fontSize,
        $color,
        $fontPath,
        $x,
        $y,
        $width,
        $height,
        $scale = 1.0
    ) {
        // 分配文本颜色（抗锯齿）
        $rgb       = $this->hexToRgb($color);
        $textColor = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);

        // 处理换行
        $lines      = $this->wrapText($text, $fontSize, $width - (80 * $scale), $fontPath);
        $lineHeight = $fontSize * 1.9;

        // 计算文本起始Y位置（垂直居中）
        $totalTextHeight = count($lines) * $lineHeight;
        $startY          = $y + ($height - $totalTextHeight) / 2;

        // 逐行绘制高质量文本
        foreach ($lines as $index => $line) {
            if (empty(trim($line))) {
                continue;
            }

            $currentY = $startY + ($index * $lineHeight) + $fontSize;

            if (file_exists($fontPath)) {
                // 使用TTF字体渲染高质量文本
                imagettftext($image, $fontSize, 0, $x + (40 * $scale), $currentY, $textColor, $fontPath, $line);
            } else {
                // 使用系统字体（质量较低）
                imagestring($image, 5, $x + (40 * $scale), $currentY - $fontSize, $line, $textColor);
            }
        }
    }

    /**
     * 十六进制颜色转RGB
     */
    private function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * 在图片上添加文本（支持自动换行）
     */
    private function addTextToImage($image, $text, $fontSize, $color, $fontPath, $x, $y, $width, $height)
    {
        // 分配文本颜色
        $rgb       = $this->hexToRgb($color);
        $textColor = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);

        // 处理换行
        $lines      = $this->wrapText($text, $fontSize, $width - 80, $fontPath); // 左右各留40px边距
        $lineHeight = $fontSize * 1.5;                                           // 行高

        // 计算文本起始Y位置（垂直居中）
        $totalTextHeight = count($lines) * $lineHeight;
        $startY          = $y + ($height - $totalTextHeight) / 2;

        // 逐行绘制文本
        foreach ($lines as $index => $line) {
            $currentY = $startY + ($index * $lineHeight) + $fontSize;

            if (file_exists($fontPath)) {
                // 使用TTF字体
                imagettftext($image, $fontSize, 0, $x + 40, $currentY, $textColor, $fontPath, $line);
            } else {
                // 使用系统默认字体
                imagestring($image, 5, $x + 40, $currentY - $fontSize, $line, $textColor);
            }
        }
    }

    /**
     * 智能文本自动换行（支持中英文混合）
     */
    private function wrapText($text, $fontSize, $maxWidth, $fontPath)
    {
        $lines = [];

        // 处理换行符
        $paragraphs = explode("\n", $text);

        foreach ($paragraphs as $paragraph) {
            if (empty(trim($paragraph))) {
                $lines[] = ''; // 保留空行
                continue;
            }

            // 检测是否包含中文
            if ($this->containsChinese($paragraph)) {
                $lines = array_merge($lines, $this->wrapChineseText($paragraph, $fontSize, $maxWidth, $fontPath));
            } else {
                $lines = array_merge($lines, $this->wrapEnglishText($paragraph, $fontSize, $maxWidth, $fontPath));
            }
        }

        return $lines ?: [''];
    }

    /**
     * 检测文本是否包含中文字符
     */
    private function containsChinese($text)
    {
        return preg_match('/[\x{4e00}-\x{9fff}]/u', $text);
    }

    /**
     * 中文文本换行处理
     */
    private function wrapChineseText($text, $fontSize, $maxWidth, $fontPath)
    {
        $lines       = [];
        $currentLine = '';
        $chars       = mb_str_split($text, 1, 'UTF-8');

        foreach ($chars as $char) {
            $testLine  = $currentLine . $char;
            $textWidth = $this->getTextWidth($testLine, $fontSize, $fontPath);

            if ($textWidth <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $lines[]     = $currentLine;
                    $currentLine = $char;
                } else {
                    // 单个字符都超宽的情况（极少见）
                    $lines[]     = $char;
                    $currentLine = '';
                }
            }
        }

        if ($currentLine) {
            $lines[] = $currentLine;
        }

        return $lines;
    }

    /**
     * 英文文本换行处理（按单词分割）
     */
    private function wrapEnglishText($text, $fontSize, $maxWidth, $fontPath)
    {
        $lines       = [];
        $words       = preg_split('/(\s+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $currentLine = '';

        foreach ($words as $word) {
            $testLine  = $currentLine . $word;
            $textWidth = $this->getTextWidth($testLine, $fontSize, $fontPath);

            if ($textWidth <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $lines[]     = rtrim($currentLine);
                    $currentLine = ltrim($word);
                } else {
                    // 单词太长，强制换行
                    $lines[]     = $word;
                    $currentLine = '';
                }
            }
        }

        if ($currentLine) {
            $lines[] = rtrim($currentLine);
        }

        return $lines;
    }

    /**
     * 计算文本宽度
     */
    private function getTextWidth($text, $fontSize, $fontPath)
    {
        if (file_exists($fontPath) && function_exists('imagettfbbox')) {
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
            return $bbox[4] - $bbox[0];
        }

        // 简单估算（当没有TTF字体时）
        return strlen($text) * $fontSize * 0.6;
    }

    /**
     * 计算文本区域所需高度
     */
    private function calculateTextHeight($text, $fontSize, $maxWidth, $fontPath)
    {
        $lines = $this->wrapText($text, $fontSize, $maxWidth - 80, $fontPath);
        return count($lines) * $fontSize * 1.7;
    }
}
