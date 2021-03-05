<?php

namespace Differ\BuildAst;

use function Funct\Collection\union;
use function Funct\Collection\sortBy;

function buildAst(\stdClass $data1, \stdClass $data2): array
{
    $varsOfContent1 = get_object_vars($data1);
    $varsOfContent2 = get_object_vars($data2);
    $unionOfKeys = union(
        array_keys($varsOfContent1),
        array_keys($varsOfContent2)
    );
    return array_map(function ($key) use ($data1, $data2) {
        if (
            (property_exists($data1, $key) && property_exists($data2, $key))
            && (is_object($data1->$key) && is_object($data2->$key))
        ) {
            return [
                'type' => 'nested',
                'key' => $key,
                'children' => buildAst($data1->$key, $data2->$key)
            ];
        } elseif (!property_exists($data1, $key)) {
            return [
                'type' => 'added',
                'key' => $key,
                'value' => $data2->$key
            ];
        } elseif (!property_exists($data2, $key)) {
            return [
                'type' => 'removed',
                'key' => $key,
                'value' => $data1->$key
            ];
        } elseif ($data1->$key !== $data2->$key) {
            return [
                'type' => 'changed',
                'key' => $key,
                'newValue' => $data2->$key,
                'oldValue' => $data1->$key,
            ];
        } else {
            return [
                'type' => 'unchanged',
                'key' => $key,
                'value' => $data1->$key
            ];
        };
    }, $unionOfKeys);
}
