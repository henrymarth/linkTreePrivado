<?php

use App\Core\View;

$pageTitle = $profile['name'] . ' | Meus links';
$pageDescription = $profile['bio'] ?: 'Encontre os links de ' . $profile['name'] . '.';
$baseUrl = rtrim(getenv('APP_URL') ?: '', '/');
$canonicalUrl = $baseUrl !== '' ? $baseUrl . '/' . rawurlencode($profile['slug']) : null;
$presentations = [
    'github' => ['icon' => 'github', 'description' => 'Código, projetos e contribuições'],
    'linkedin' => ['icon' => 'linkedin', 'description' => 'Minha trajetória profissional'],
    'resume' => ['icon' => 'file-text', 'description' => 'Minha trajetória em PDF'],
    'link' => ['icon' => 'link', 'description' => 'Saiba mais'],
];

require __DIR__ . '/partials/head.php';
?>
<main class="profile" aria-labelledby="profile-name">
    <header class="profile-header">
        <p class="eyebrow">Um pouco sobre mim</p>
        <div class="avatar">
            <span aria-hidden="true"><?= View::escape(View::initials($profile['name'])) ?></span>
            <?php if ($profile['avatar_url'] !== null): ?>
                <img src="<?= View::escape($profile['avatar_url']) ?>" alt="" width="80" height="80" decoding="async">
            <?php endif; ?>
        </div>
        <h1 id="profile-name"><?= View::escape($profile['name']) ?></h1>
        <?php if (!empty($profile['bio'])): ?>
            <p class="bio"><?= nl2br(View::escape($profile['bio'])) ?></p>
        <?php endif; ?>
    </header>

    <?php if ($profile['links'] !== []): ?>
        <nav class="link-list" aria-label="Links de <?= View::escape($profile['name']) ?>">
            <?php foreach ($profile['links'] as $link):
                $presentation = $presentations[$link['kind']] ?? $presentations['link'];
                $isResume = $link['kind'] === 'resume';
                $href = $isResume ? '/curriculo/' . rawurlencode($profile['slug']) : $link['url'];
                $description = $link['description'] ?: $presentation['description'];
                ?>
                <a class="link-card<?= $isResume ? ' link-card-featured' : '' ?>"
                   href="<?= View::escape($href) ?>"
                   <?php if ($isResume): ?>
                       download="curriculo-<?= View::escape($profile['slug']) ?>.pdf"
                   <?php else: ?>
                       target="_blank" rel="noopener noreferrer"
                   <?php endif; ?>
                   aria-label="<?= View::escape($link['title'] . ($isResume ? ' (PDF)' : ' (abre em nova aba)')) ?>">
                    <span class="link-icon"><?= View::icon($presentation['icon']) ?></span>
                    <span class="link-copy">
                        <span class="link-title"><?= View::escape($link['title']) ?></span>
                        <span class="link-description"><?= View::escape($description) ?></span>
                    </span>
                    <?= View::icon($isResume ? 'download' : 'arrow-up-right', 'link-action') ?>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php else: ?>
        <p class="empty-state">Novas conexões chegam em breve. Volte mais tarde para encontrar os links deste perfil.</p>
    <?php endif; ?>

    <footer class="profile-footer">
        <details class="qr-details">
            <summary><?= View::icon('qr-code') ?><span>Ver meu QR code</span></summary>
            <div class="qr-content">
                <img src="/qr/<?= View::escape($profile['slug']) ?>" alt="QR code para abrir o perfil de <?= View::escape($profile['name']) ?>" width="224" height="224" loading="lazy">
                <p>Aponte a câmera para abrir este perfil.</p>
                <a class="text-link" href="/qr/<?= View::escape($profile['slug']) ?>" download="qr-<?= View::escape($profile['slug']) ?>.svg">Baixar QR code <?= View::icon('download') ?></a>
            </div>
        </details>
    </footer>
</main>
</body>
</html>
