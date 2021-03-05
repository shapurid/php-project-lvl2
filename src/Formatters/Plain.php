<?php

namespace Differ\Formatters\Plain;

function stringifyValue($value)
{
    $typeOfValue = gettype($value);

    switch ($typeOfValue) {
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'NULL':
            return 'null';
        case 'array':
            return '[complex value]';
        case 'object':
            return '[complex value]';
        case 'integer':
            return (string) $value;
        case 'double':
            return (string) $value;
        default:
            return "'$value'";
    }
}

function format($ast, $path = '')
{
    $nodeHandlers = [
        'added' =>
            function ($pathOfProperty, $node) {
                ['value' => $value] = $node;
                $stringifiedValue = stringifyValue($value);
                return "Property '$pathOfProperty' was added with value: $stringifiedValue";
            },
        'removed' =>
            fn($pathOfProperty) => "Property '$pathOfProperty' was removed",
        'unchanged' =>
            fn() => '',
        'changed' =>
            function ($pathOfProperty, $node) {
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
    return implode("\n", $elementsOfDiff);
}
