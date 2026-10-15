<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    public static function view(string $template, array $data, int $status = 200, string $cacheControl = 'no-store'): never
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Language: pt-BR');
        header('Cache-Control: ' . $cacheControl);
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: default-src 'self'; img-src 'self' https: http:; style-src 'self'; script-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'none'");

        extract($data, EXTR_SKIP);
        require dirname(__DIR__) . '/Views/' . $template . '.php';
        exit;
    }

    public static function json(array $payload, int $status = 200, string $cacheControl = 'no-store'): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: ' . $cacheControl);
        header('Access-Control-Allow-Origin: *');
        header('X-Content-Type-Options: nosniff');

        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
}
