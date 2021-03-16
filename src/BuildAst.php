<?php

namespace Differ\BuildAst;

use function Functional\sort;

/**
 * @return array<int|string|bool|array|\stdClass>
 */

function buildAst(object $data1, object $data2)
{
    $varsOfContent1 = get_object_vars($data1);
    $varsOfContent2 = get_object_vars($data2);
    $mergedKeys = array_merge(
        array_keys($varsOfContent1),
        array_keys($varsOfContent2)
    );
    $unionOfKeys = array_unique($mergedKeys);
    $sortedKeys = sort($unionOfKeys, fn($left, $right) => strcmp($left, $right), true);
    return array_map(function ($key) use ($data1, $data2) {
        if (!property_exists($data1, $key)) {
            return [
                'type' => 'added',
                'key' => $key,
                'value' => $data2->$key
            ];
        }
        if (!property_exists($data2, $key)) {
            return [
                'type' => 'removed',
                'key' => $key,
                'value' => $data1->$key
            ];
        }
        if (is_object($data1->$key) && is_object($data2->$key)) {
            return [
                'type' => 'nested',
                'key' => $key,
                'children' => buildAst($data1->$key, $data2->$key)
            ];
        }
        if ($data1->$key !== $data2->$key) {
            return [
                'type' => 'changed',
                'key' => $key,
                'newValue' => $data2->$key,
                'oldValue' => $data1->$key,
            ];
        }
        return [
            'type' => 'unchanged',
            'key' => $key,
            'value' => $data1->$key
        ];
    }, $sortedKeys);
}
