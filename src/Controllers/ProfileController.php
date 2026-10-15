<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Models\Profile;

final class ProfileController
{
    public function show(string $slug, bool $asJson = false): never
    {
        $profile = (new Profile())->findPublicBySlug($slug);

        if ($profile === null) {
            if ($asJson) {
                Response::json(['error' => 'Perfil não encontrado.'], 404);
            }

            Response::view('message', [
                'title' => 'Perfil não encontrado',
                'message' => 'Este perfil não está disponível. Confira o endereço recebido.',
            ], 404);
        }

        $cacheControl = 'public, max-age=60, s-maxage=60, stale-while-revalidate=30';

        if ($asJson) {
            Response::json(['data' => $profile], 200, $cacheControl);
        }

        Response::view('profile', ['profile' => $profile], 200, $cacheControl);
    }
}
