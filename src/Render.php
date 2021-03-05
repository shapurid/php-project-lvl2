<?php

namespace App\Render;

use Exception;
use App\Formatters;

function render($data, $format = 'stylish')
{
    $formatters = [
        'json' =>
            fn($data) => json_encode($data, JSON_PRETTY_PRINT),
        'stylish' =>
            fn($data) => Formatters\Stylish\format($data),
        'plain' =>
            fn($data) => Formatters\Plain\format($data)
    ];
    if (!array_key_exists($format, $formatters)) {
        throw new Exception('This format does not support!');
    }
    return $formatters[$format]($data);
}
