<?php

namespace Differ\Render;

use Exception;
use Differ\Formatters;

/**
 * @param array<int|string|bool|array|\stdClass> $ast
 * @return string|false
 */

function render($ast, string $format)
{
    $formatters = [
        'json' =>
            fn($ast) => json_encode($ast, JSON_PRETTY_PRINT),
        'stylish' =>
            fn($ast) => Formatters\Stylish\format($ast),
        'plain' =>
            fn($ast) => Formatters\Plain\format($ast)
    ];
    if (!array_key_exists($format, $formatters)) {
        throw new Exception("Format {$format} does not support!");
    }
    return $formatters[$format]($ast);
}
