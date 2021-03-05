<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

/**
 * @param string|false $data
 */

function parse($data, string $type): \stdClass
{
    $parsers = [
        'json' =>
            fn($data) => json_decode($data),
        'yml' =>
            fn($data) => Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP),
        'yaml' =>
            fn($data) => Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP),
    ];
    return $parsers[$type]($data);
}
