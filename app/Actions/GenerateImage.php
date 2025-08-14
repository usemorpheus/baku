<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateImage
{
    use AsAction;

    private array $config = [
        'fontSize'        => 13,
        'titleFontSize'   => 16,
        'textColor'       => '#000000',
        'contentColor'    => '#333333',
        'backgroundColor' => '#FDF1E5',
        'quality'         => 100,
        'scale'           => 1,
        'padding'         => 40,
        'lineHeight'      => 1.9,
        'minTextHeight'   => 280,
    ];

    private array $paths = [
        'topImage'    => 'assets/post/top.png',
        'bottomImage' => 'assets/post/bottom.png',
        'titleFont'   => 'assets/post/fonts/SourceHanSansCN-Bold.otf',
        'contentFont' => 'assets/post/fonts/SourceHanSansCN-Regular.otf',
        'timeFont'    => 'assets/post/fonts/Jersey15-Regular.ttf',
    ];

    public function handle($title, $text): string
    {
        try {
            // 加载图片资源
            $topImage    = $this->loadImage(public_path($this->paths['topImage']));
            $bottomImage = $this->loadImage(public_path($this->paths['bottomImage']));

            if (!$topImage || !$bottomImage) {
                throw new \Exception('Failed to load images');
            }

            // 计算尺寸
            $dimensions = $this->calculateDimensions($topImage, $bottomImage, $title, $text);

            // 创建最终图片
            $finalImage = $this->createFinalImage($dimensions);

            // 组装图片内容
            $this->assembleImage($finalImage, $topImage, $bottomImage, $title, $text, $dimensions);

            // 保存并返回URL
            return $this->saveImage($finalImage);

        } catch (\Exception $e) {
            Log::error('GenerateImage failed: ' . $e->getMessage());
            return '';
        } finally {
            // 清理资源
            if (isset($topImage)) {
                imagedestroy($topImage);
            }
            if (isset($bottomImage)) {
                imagedestroy($bottomImage);
            }
            if (isset($finalImage)) {
                imagedestroy($finalImage);
            }
        }
    }

    private function loadImage(string $path)
    {
        if (!file_exists($path)) {
            return false;
        }

        $imageInfo = getimagesize($path);
        if (!$imageInfo) {
            return false;
        }

        $loaders = [
            IMAGETYPE_JPEG => 'imagecreatefromjpeg',
            IMAGETYPE_PNG  => 'imagecreatefrompng',
            IMAGETYPE_GIF  => 'imagecreatefromgif',
            IMAGETYPE_WEBP => 'imagecreatefromwebp',
        ];

        if (!isset($loaders[$imageInfo[2]])) {
            return false;
        }

        $image = $loaders[$imageInfo[2]]($path);

        // 保持透明度
        if ($imageInfo[2] === IMAGETYPE_PNG) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        return $image;
    }

    private function calculateDimensions($topImage, $bottomImage, string $title, string $text): array
    {
        $scale = $this->config['scale'];
        $width = max(imagesx($topImage), imagesx($bottomImage)) * $scale;

        $titleHeight = $this->getTextHeight($title, $this->config['titleFontSize'] * $scale, $width,
            $this->paths['titleFont']);
        $textHeight  = max($this->config['minTextHeight'],
            $this->getTextHeight($text, $this->config['fontSize'] * $scale, $width, $this->paths['contentFont']));

        return [
            'width'        => (int)$width,
            'topHeight'    => (int)(imagesy($topImage) * $scale),
            'bottomHeight' => (int)(imagesy($bottomImage) * $scale),
            'titleHeight'  => $titleHeight,
            'textHeight'   => $textHeight,
            'middleHeight' => $titleHeight + $textHeight + ($this->config['padding'] * $scale * 2),
            'scale'        => $scale,
        ];
    }

    private function createFinalImage(array $dimensions)
    {
        $totalHeight = $dimensions['topHeight'] + $dimensions['middleHeight'] + $dimensions['bottomHeight'];
        $image       = imagecreatetruecolor((int)$dimensions['width'], (int)$totalHeight);

        imageantialias($image, true);

        // 设置背景色
        $bgColor    = $this->hexToRgb($this->config['backgroundColor']);
        $bgResource = imagecolorallocate($image, $bgColor['r'], $bgColor['g'], $bgColor['b']);
        imagefill($image, 0, 0, $bgResource);

        return $image;
    }

    private function assembleImage(
        $finalImage,
        $topImage,
        $bottomImage,
        string $title,
        string $text,
        array $dimensions
    ): void {
        $scale = $dimensions['scale'];
        $width = $dimensions['width'];

        // 缩放并拼接顶部图片
        $scaledTop = $this->resizeImage($topImage, $width, $dimensions['topHeight']);
        imagecopy($finalImage, $scaledTop, 0, 0, 0, 0, $width, $dimensions['topHeight']);

        // 添加标题
        $this->addText($finalImage, $title, $this->config['titleFontSize'] * $scale, $this->config['textColor'],
            $this->paths['titleFont'], 0, $dimensions['topHeight'] + 20, $width, $scale);

        // 添加内容文本
        $this->addText($finalImage, $text, $this->config['fontSize'] * $scale, $this->config['contentColor'],
            $this->paths['contentFont'], 0, $dimensions['topHeight'] + $dimensions['titleHeight'] + 40, $width, $scale);

        // 缩放并拼接底部图片
        $scaledBottom = $this->resizeImage($bottomImage, $width, $dimensions['bottomHeight']);
        $totalHeight  = $dimensions['topHeight'] + $dimensions['middleHeight'] + $dimensions['bottomHeight'];
        imagecopy($finalImage, $scaledBottom, 0, (int)($dimensions['topHeight'] + $dimensions['middleHeight']),
            0, 0, (int) $width, (int) $dimensions['bottomHeight']);

        // 添加时间戳
        $this->addTimestamp($finalImage, $totalHeight, $scale);

        imagedestroy($scaledTop);
        imagedestroy($scaledBottom);
    }

    private function resizeImage($source, int $newWidth, int $newHeight)
    {
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefill($resized, 0, 0, $transparent);

        imagecopyresampled($resized, $source, 0, 0, 0, 0,
            $newWidth, $newHeight, imagesx($source), imagesy($source));

        return $resized;
    }

    private function addText(
        $image,
        $text,
        $fontSize,
        $color,
        $fontPath,
        $x,
        $y,
        $width,
        $scale
    ): void {
        $rgb       = $this->hexToRgb($color);
        $textColor = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);

        $lines      = $this->wrapText($text, $fontSize, $width - (80 * $scale), public_path($fontPath));
        $lineHeight = $fontSize * $this->config['lineHeight'];

        foreach ($lines as $index => $line) {
            if (empty(trim($line))) {
                continue;
            }

            $currentY = $y + ($index * $lineHeight) + $fontSize;
            $fontFile = public_path($fontPath);

            if (file_exists($fontFile)) {
                imagettftext($image, $fontSize, 0, (int)($x + (40 * $scale)), (int)$currentY, $textColor, $fontFile,
                    $line);
            } else {
                imagestring($image, 5, $x + (40 * $scale), $currentY - $fontSize, $line, $textColor);
            }
        }
    }

    private function addTimestamp($image, $totalHeight, $scale): void
    {
        $fontPath = public_path($this->paths['timeFont']);
        $this->addText($image, date("Y-m-d"), 22, '#000000', $this->paths['timeFont'],
            185, $totalHeight - 80, imagesx($image), $scale);
        $this->addText($image, date("H:i:s"), 22, '#000000', $this->paths['timeFont'],
            185, $totalHeight - 50, imagesx($image), $scale);
    }

    private function saveImage($image): string
    {
        ob_start();
        imagejpeg($image, null, $this->config['quality']);
        $imageData = ob_get_contents();
        ob_end_clean();

        $fileName = Str::random() . '.jpg';
        $path     = '/articles/images/' . $fileName;
        $fullPath = public_path($path);

        // 确保目录存在
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $imageData);
        return url($path);
    }

    private function wrapText(string $text, int $fontSize, float $maxWidth, string $fontPath): array
    {
        $lines      = [];
        $paragraphs = explode("\n", $text);

        foreach ($paragraphs as $paragraph) {
            if (empty(trim($paragraph))) {
                $lines[] = '';
                continue;
            }

            if (preg_match('/[\x{4e00}-\x{9fff}]/u', $paragraph)) {
                $lines = array_merge($lines, $this->wrapChineseText($paragraph, $fontSize, $maxWidth, $fontPath));
            } else {
                $lines = array_merge($lines, $this->wrapEnglishText($paragraph, $fontSize, $maxWidth, $fontPath));
            }
        }

        return $lines ?: [''];
    }

    private function wrapChineseText(string $text, int $fontSize, float $maxWidth, string $fontPath): array
    {
        $lines       = [];
        $currentLine = '';
        $chars       = mb_str_split($text, 1, 'UTF-8');

        foreach ($chars as $char) {
            $testLine = $currentLine . $char;
            if ($this->getTextWidth($testLine, $fontSize, $fontPath) <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $lines[]     = $currentLine;
                    $currentLine = $char;
                } else {
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

    private function wrapEnglishText(string $text, int $fontSize, float $maxWidth, string $fontPath): array
    {
        $lines       = [];
        $words       = preg_split('/(\s+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine . $word;
            if ($this->getTextWidth($testLine, $fontSize, $fontPath) <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $lines[]     = rtrim($currentLine);
                    $currentLine = ltrim($word);
                } else {
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

    private function getTextWidth(string $text, int $fontSize, string $fontPath): float
    {
        if (file_exists($fontPath) && function_exists('imagettfbbox')) {
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
            return $bbox[4] - $bbox[0];
        }
        return strlen($text) * $fontSize * 0.6;
    }

    private function getTextHeight(string $text, int $fontSize, int $maxWidth, string $fontPath): float
    {
        $lines = $this->wrapText($text, $fontSize, $maxWidth - 80, public_path($fontPath));
        return count($lines) * $fontSize * 1.7;
    }

    private function hexToRgb(string $hex): array
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
}
