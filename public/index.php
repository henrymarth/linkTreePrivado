<?php

declare(strict_types=1);

use App\Controllers\ProfileController;
use App\Controllers\ResumeController;
use App\Core\Response;
use App\Services\QrCodeService;

require dirname(__DIR__) . '/vendor/autoload.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = '/' . trim($path, '/');

try {
    if ($method === 'GET' && $path === '/healthz') {
        Response::json(['status' => 'ok']);
    }

    if ($method === 'GET' && $path === '/') {
        Response::view('message', [
            'title' => 'Seus links. Um só endereço.',
            'message' => 'Abra o endereço completo do perfil que você recebeu para encontrar seus links.',
        ]);
    }

    if ($method === 'GET' && preg_match('#^/api/profiles/([a-z0-9][a-z0-9-]{0,62})$#', $path, $matches) === 1) {
        (new ProfileController())->show($matches[1], true);
    }

    if ($method === 'GET' && preg_match('#^/qr/([a-z0-9][a-z0-9-]{0,62})$#', $path, $matches) === 1) {
        (new QrCodeService())->show($matches[1]);
    }

    if ($method === 'GET' && preg_match('#^/curriculo/([a-z0-9][a-z0-9-]{0,62})$#', $path, $matches) === 1) {
        (new ResumeController())->download($matches[1]);
    }

    if ($method === 'GET' && preg_match('#^/([a-z0-9][a-z0-9-]{0,62})$#', $path, $matches) === 1) {
        (new ProfileController())->show($matches[1]);
    }

    if (str_starts_with($path, '/api/')) {
        Response::json(['error' => 'Rota não encontrada.'], 404);
    }

    Response::view('message', [
        'title' => 'Página não encontrada',
        'message' => 'Confira o endereço recebido e tente novamente.',
    ], 404);
} catch (Throwable $exception) {
    error_log((string) $exception);
    if (str_starts_with($path, '/api/') || $path === '/healthz') {
        Response::json(['error' => 'Erro interno.'], 500);
    }

    Response::view('message', [
        'title' => 'Vamos tentar de novo?',
        'message' => 'Não foi possível carregar esta página. Tente novamente em instantes.',
    ], 500);
}
