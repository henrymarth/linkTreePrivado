<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function icon(string $name, string $class = ''): string
    {
        return '<svg class="icon ' . self::escape($class) . '" width="20" height="20" aria-hidden="true" focusable="false">'
            . '<use href="/assets/icons.svg#' . self::escape($name) . '"></use></svg>';
    }

    public static function initials(string $name): string
    {
        $parts = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = '';

        foreach (array_unique([0, count($parts) - 1]) as $index) {
            if (isset($parts[$index]) && preg_match('/^./us', $parts[$index], $match) === 1) {
                $initials .= $match[0];
            }
        }

        return strtoupper($initials) ?: 'P';
    }
}
