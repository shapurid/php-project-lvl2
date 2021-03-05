<?php

namespace Differ\Formatters\Stylish;

function makeIndent(int $n = 1): string
{
    return str_repeat(' ', $n * 2);
}

/**
 * @param \stdClass|string|int|null|bool|array<int|string|bool|array|\stdClass> $value
 */

function stringifyValue($value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (is_array($value)) {
        return 'Array';
    }
    if (!is_object($value)) {
        return (string) $value;
    }
    $objectVars = get_object_vars($value);
    $keys = array_keys($objectVars);

    $data = array_map(function ($key) use ($value, $depth): string {
        $a = $value->$key;
        $formattedValue = stringifyValue($a, $depth + 2);
        return "$key: $formattedValue";
    }, $keys);
    $beginIndent = makeIndent($depth + 3);
    $endIndent = makeIndent($depth + 1);
    $formattedData = implode("\n$beginIndent", $data);
    return "{\n{$beginIndent}{$formattedData}\n{$endIndent}}";
}

/**
 * @param array<int|string|bool|array|\stdClass> $ast
 */

function renderAst($ast, int $depth = 0): string
{
    $nodeHandlers = [
        'added' =>
            function ($depth, $node): string {
                ['key' => $key, 'value' => $value] = $node;
                $indent = makeIndent($depth);
                $stringifiedValue = stringifyValue($value, $depth);
                return "$indent+ $key: $stringifiedValue";
            },
        'removed' =>
            function ($depth, $node): string {
                ['key' => $key, 'value' => $value] = $node;
                $indent = makeIndent($depth);
                $stringifiedValue = stringifyValue($value, $depth);
                return "$indent- $key: $stringifiedValue";
            },
        'unchanged' =>
            function ($depth, $node): string {
                ['key' => $key, 'value' => $value] = $node;
                $indent = makeIndent($depth);
                $stringifiedValue = stringifyValue($value, $depth);
                return "$indent  $key: $stringifiedValue";
            },
        'changed' =>
            function ($depth, $node): string {
                [
                    'key' => $key,
                    'newValue' => $newValue,
                    'oldValue' => $oldValue
                ] = $node;
                $indent = makeIndent($depth);
                $stringifiedNewValue = stringifyValue($newValue, $depth);
                $stringifiedOldValue = stringifyValue($oldValue, $depth);
                return "$indent- $key: $stringifiedOldValue\n$indent+ $key: $stringifiedNewValue";
            },
        'nested' =>
            function ($depth, $node, $fn): string {
                ['key' => $key, 'children' => $children] = $node;
                $beginIndent = makeIndent($depth);
                $endIndent = makeIndent($depth + 1);
                $formattedChildren = $fn($children, $depth + 2);
                return "$beginIndent  $key: {\n{$formattedChildren}\n{$endIndent}}";
            }
    ];

    $elementsOfDiff = array_map(function ($node) use ($nodeHandlers, $depth): string {
        ['type' => $type] = $node;
        $handleNode = $nodeHandlers[$type];
        return $handleNode($depth, $node, fn($children, $d) => renderAst($children, $d));
    }, $ast);
    return implode("\n", $elementsOfDiff);
}

/**
 * @param array<int|string|bool|array|\stdClass> $ast
 */

function format($ast, int $depth = 0): string
{
    $a = renderAst($ast, $depth + 1);
    $beginIndent = makeIndent($depth);
    return "{\n{$beginIndent}{$a}\n}";
}
