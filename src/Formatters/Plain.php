<?php

namespace Differ\Formatters\Plain;

/**
 * @param \stdClass|string|int|null|bool|array<int|string|bool|array|\stdClass> $value
 */

function stringifyValue($value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (is_array($value) || is_object($value)) {
        return '[complex value]';
    }
    if (is_int($value)) {
        return (string) $value;
    }
    return "'$value'";
}

/**
 * @param array<int|string|bool|array|\stdClass> $ast
 */

function format($ast, string $path = ''): string
{
    $nodeHandlers = [
        'added' =>
            function ($pathOfProperty, $node): string {
                ['value' => $value] = $node;
                $stringifiedValue = stringifyValue($value);
                return "Property '$pathOfProperty' was added with value: $stringifiedValue";
            },
        'removed' =>
            fn($pathOfProperty) => "Property '$pathOfProperty' was removed",
        'unchanged' =>
            fn() => '',
        'changed' =>
            function ($pathOfProperty, $node): string {
                ['newValue' => $newValue, 'oldValue' => $oldValue] = $node;
                $stringifiedNewValue = stringifyValue($newValue);
                $stringifiedOldValue = stringifyValue($oldValue);
                return "Property '$pathOfProperty' was updated. From $stringifiedOldValue to $stringifiedNewValue";
            },
        'nested' =>
            function ($pathOfProperty, $node, $fn) {
                ['children' => $children] = $node;
                return $fn($children, $pathOfProperty);
            }
    ];

    $elementsOfDiff = array_map(function ($node) use ($nodeHandlers, $path) {
        ['type' => $type, 'key' => $key] = $node;
        $pathOfProperty = strlen($path) > 0 ? "$path.$key" : $key;
        $handleNode = $nodeHandlers[$type];
        return $handleNode($pathOfProperty, $node, fn($children, $d) => format($children, $d));
    }, $ast);
    $elementWithoutEmptyStrings = array_filter($elementsOfDiff, fn($el) => strlen($el) > 0);
    return implode("\n", $elementWithoutEmptyStrings);
}
