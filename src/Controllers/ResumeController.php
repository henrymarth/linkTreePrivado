<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Models\Profile;

final class ResumeController
{
    public function download(string $slug): never
    {
        $profile = (new Profile())->findPublicBySlug($slug);
        $resume = null;

        foreach ($profile['links'] ?? [] as $link) {
            if ($link['kind'] === 'resume') {
                $resume = $link;
                break;
            }
        }

        if ($resume === null) {
            $this->notFound();
        }

        if (!str_starts_with($resume['url'], '/files/curriculos/')) {
            header('Cache-Control: no-store');
            header('Location: ' . $resume['url'], true, 302);
            exit;
        }

        $publicDirectory = dirname(__DIR__, 2) . '/public';
        $resumeDirectory = realpath($publicDirectory . '/files/curriculos');
        $file = realpath($publicDirectory . $resume['url']);

        if ($resumeDirectory === false || $file === false || !is_file($file)
            || !str_starts_with($file, $resumeDirectory . DIRECTORY_SEPARATOR)) {
            $this->notFound();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="curriculo-' . $slug . '.pdf"');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: public, max-age=60');
        header('X-Content-Type-Options: nosniff');
        readfile($file);
        exit;
    }

    private function notFound(): never
    {
        Response::view('message', [
            'title' => 'Currículo indisponível',
            'message' => 'O arquivo deste perfil não está disponível no momento.',
        ], 404);
    }
}
