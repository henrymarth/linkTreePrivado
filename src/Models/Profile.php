<?php

declare(strict_types=1);

namespace App\Models;

use RuntimeException;

final class Profile
{
    public function __construct(
        private readonly string $dataFile = __DIR__ . '/../../data/profiles.json'
    ) {
    }

    public function findPublicBySlug(string $slug): ?array
    {
        $contents = @file_get_contents($this->dataFile);

        if ($contents === false) {
            throw new RuntimeException('Não foi possível ler o arquivo de perfis.');
        }

        $profiles = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($profiles)) {
            throw new RuntimeException('O arquivo de perfis deve conter um objeto JSON.');
        }

        $profile = $profiles[$slug] ?? null;

        if (!is_array($profile) || !($profile['is_active'] ?? true)) {
            return null;
        }

        if (!is_string($profile['name'] ?? null) || trim($profile['name']) === '') {
            throw new RuntimeException('O perfil deve ter um nome.');
        }

        if (!is_array($profile['links'] ?? [])) {
            throw new RuntimeException('Os links do perfil devem ser uma lista.');
        }

        $links = [];

        foreach ($profile['links'] ?? [] as $link) {
            if (!is_array($link) || !($link['is_active'] ?? true)
                || !is_string($link['title'] ?? null) || trim($link['title']) === ''
                || !is_string($link['url'] ?? null)) {
                continue;
            }

            $kind = self::linkKind(is_string($link['kind'] ?? null) ? $link['kind'] : 'link', $link['url']);

            if (!self::isSafeUrl($link['url'], $kind === 'resume')) {
                continue;
            }

            $links[] = [
                'title' => $link['title'],
                'url' => $link['url'],
                'kind' => $kind,
                'description' => is_string($link['description'] ?? null) ? $link['description'] : null,
            ];
        }

        $avatarUrl = $profile['avatar_url'] ?? null;

        return [
            'slug' => $slug,
            'name' => $profile['name'],
            'bio' => is_string($profile['bio'] ?? null) ? $profile['bio'] : null,
            'avatar_url' => is_string($avatarUrl) && self::isSafeAvatarUrl($avatarUrl) ? $avatarUrl : null,
            'links' => $links,
        ];
    }

    private static function isSafeAvatarUrl(string $url): bool
    {
        return self::isSafeUrl($url)
            || (preg_match('#^/assets/profiles/[a-zA-Z0-9][a-zA-Z0-9_.-]*[.](?:jpe?g|png|webp|avif)$#D', $url) === 1
                && !str_contains($url, '..'));
    }

    private static function linkKind(string $kind, string $url): string
    {
        if (in_array($kind, ['github', 'linkedin', 'resume'], true)) {
            return $kind;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);

        return match (true) {
            in_array($host, ['github.com', 'www.github.com'], true) => 'github',
            in_array($host, ['linkedin.com', 'www.linkedin.com'], true) => 'linkedin',
            strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf' => 'resume',
            default => 'link',
        };
    }

    private static function isSafeUrl(string $url, bool $allowLocalPdf = false): bool
    {
        if ($allowLocalPdf
            && preg_match('#^/files/curriculos/[a-zA-Z0-9][a-zA-Z0-9/_.-]*[.]pdf$#D', $url) === 1
            && !str_contains($url, '..')) {
            return true;
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        return $scheme === 'http' || $scheme === 'https';
    }
}
