<?php

namespace Differ\Formatters\Stylish;

function makeIndent($n = 1): string
{
    return str_repeat(' ', $n * 2);
}

function stringifyValue($value, $depth): string
{
    $typeOfValue = gettype($value);

    switch ($typeOfValue) {
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'NULL':
            return 'null';
        case 'array':
            return 'Array';
        case 'object':
            $objectVars = get_object_vars($value);
            $keys = array_keys($objectVars);

            $data = array_map(function ($key) use ($value, $depth): string {
                $indent = makeIndent($depth);
                $formattedValue = stringifyValue($value->$key, $depth + 2);
                return "$key: $formattedValue";
            }, $keys);
            $beginIndent = makeIndent($depth + 3);
            $endIndent = makeIndent($depth + 1);
            $formattedData = implode("\n$beginIndent", $data);
            return "{\n{$beginIndent}{$formattedData}\n{$endIndent}}";
        default:
            return $value;
    }
}

function renderAst($ast, $depth = 0): string
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

function format($ast, $depth = 0): string
{
    $a = renderAst($ast, $depth + 1);
    $beginIndent = makeIndent($depth);
    return "{\n{$beginIndent}{$a}\n}";
}
