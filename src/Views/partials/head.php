<?php

use App\Core\View;
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="description" content="<?= View::escape($pageDescription) ?>">
    <meta name="theme-color" content="#f3f6fa" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#111b2d" media="(prefers-color-scheme: dark)">
    <title><?= View::escape($pageTitle) ?></title>
    <meta property="og:title" content="<?= View::escape($pageTitle) ?>">
    <meta property="og:description" content="<?= View::escape($pageDescription) ?>">
    <meta property="og:type" content="profile">
    <?php if (!empty($canonicalUrl)): ?>
        <link rel="canonical" href="<?= View::escape($canonicalUrl) ?>">
        <meta property="og:url" content="<?= View::escape($canonicalUrl) ?>">
    <?php endif; ?>
    <?php if (!empty($profile['avatar_url'])): ?>
        <?php $socialImageUrl = str_starts_with($profile['avatar_url'], '/') && !empty($baseUrl)
            ? $baseUrl . $profile['avatar_url']
            : $profile['avatar_url']; ?>
        <meta property="og:image" content="<?= View::escape($socialImageUrl) ?>">
    <?php endif; ?>
    <link rel="icon" href="/assets/favicon.svg?v=2" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/styles.css?v=2">
</head>
<body>
