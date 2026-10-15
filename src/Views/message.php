<?php

use App\Core\View;

$pageTitle = $title . ' | Meus links';
$pageDescription = $message;
require __DIR__ . '/partials/head.php';
?>
<main class="profile message" aria-labelledby="message-title">
    <span class="message-icon"><?= View::icon('link') ?></span>
    <p class="eyebrow">Meus links</p>
    <h1 id="message-title"><?= View::escape($title) ?></h1>
    <p class="bio"><?= View::escape($message) ?></p>
    <?php if (($_SERVER['REQUEST_URI'] ?? '/') !== '/'): ?>
        <a class="text-link" href="/">Voltar ao início</a>
    <?php endif; ?>
</main>
</body>
</html>
