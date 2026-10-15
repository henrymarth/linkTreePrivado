<?php

declare(strict_types=1);

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

final class QrCodeService
{
    public function show(string $slug): never
    {
        $baseUrl = rtrim(self::env('PUBLIC_BASE_URL', self::env('APP_URL', 'http://localhost')), '/');
        $targetUrl = $baseUrl . '/' . rawurlencode($slug);
        $qrCode = new QrCode(data: $targetUrl, size: 512, margin: 16);
        $result = (new SvgWriter())->write($qrCode);

        header('Content-Type: ' . $result->getMimeType());
        header('Cache-Control: public, max-age=86400, s-maxage=86400');
        header('Access-Control-Allow-Origin: *');
        header('X-Content-Type-Options: nosniff');
        echo $result->getString();
        exit;
    }

    private static function env(string $key, string $default): string
    {
        $value = getenv($key);
        return $value === false || trim($value) === '' ? $default : trim($value);
    }
}
