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
    if (!array_key_exists($type, $parsers)) {
        $availableTypes = implode(', ', array_keys($parsers));
        throw new \Exception("File extension {$type} does not support! Supported extensions are: {$availableTypes}");
    }
    return $parsers[$type]($data);
}
