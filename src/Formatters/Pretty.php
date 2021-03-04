<?php

namespace App\Formatters\Pretty;

function makeIndent($n = 1)
{
    return str_repeat(' ', $n * 2);
}

function stringifyValue($value, $depth)
{
    $typeOfValue = gettype($value);

    switch ($typeOfValue) {
        case 'boolean':
            return $value ? 'true' : 'false';
            break;
        case 'NULL':
            return 'null';
        case 'array':
            return 'Array';
            break;
        case 'object':
            $objectVars = get_object_vars($value);
            $keys = array_keys($objectVars);

            $data = array_map(function ($key) use ($value, $depth) {
                $indent = makeIndent($depth);
                $formattedValue = stringifyValue($value->$key, $depth + 2);
                return "\"$key\": $formattedValue";
            }, $keys);
            $beginIndent = makeIndent($depth + 3);
            $endIndent = makeIndent($depth + 1);
            $formattedData = implode("\n$beginIndent", $data);
            return "{\n{$beginIndent}{$formattedData}\n{$endIndent}}";
            break;
        default:
            return "\"$value\",";
            break;
    }
}

// function buildIndent($depth, $quantityOfGaps)
// {
//     $spaceMultiplier = $depth * $quantityOfGaps;
//     return str_repeat(" ", $spaceMultiplier);
// }

// function stringifyValue($value, $depth)
// {
//     if (is_bool($value)) {
//         return $value ? 'true' : 'false';
//     }
//     if (is_null($value)) {
//         return 'null';
//     }
//     if (!is_object($value)) {
//         return (string) $value;
//     }
//     $indent = buildIndent($depth, 4);
//     $stringOfArray = array_map(function ($key, $item) use ($depth, $indent) {
//         $depth += 1;
//         $typeOfValueOfNode = (is_object($item)) ? stringifyValue($item, $depth) : $item;
//         return $indent . "    " . "{$key}: " . $typeOfValueOfNode . PHP_EOL;
//     }, array_keys(get_object_vars($value)), get_object_vars($value));
//     return '{' . PHP_EOL . implode("", $stringOfArray) . $indent . '}';
// }

function format($ast, $depth = 0)
{
    $nodeHandlers = [
        'added' =>
            function ($depth, $node) {
                ['key' => $key, 'value' => $value] = $node;
                $indent = makeIndent($depth);
                $stringifiedValue = stringifyValue($value, $depth);
                return "$indent+ \"$key\": $stringifiedValue";
            },
        'removed' =>
            function ($depth, $node) {
                ['key' => $key, 'value' => $value] = $node;
                $indent = makeIndent($depth);
                $stringifiedValue = stringifyValue($value, $depth);
                return "$indent- \"$key\": $stringifiedValue";
            },
        'unchanged' =>
            function ($depth, $node) {
                ['key' => $key, 'value' => $value] = $node;
                $indent = makeIndent($depth);
                $stringifiedValue = stringifyValue($value, $depth);
                return "$indent  \"$key\": $stringifiedValue";
            },
        'changed' =>
            function ($depth, $node) {
                [
                    'key' => $key,
                    'newValue' => $newValue,
                    'oldValue' => $oldValue
                ] = $node;
                $indent = makeIndent($depth);
                $stringifiedNewValue = stringifyValue($newValue, $depth);
                $stringifiedOldValue = stringifyValue($oldValue, $depth);
                return "$indent+ \"$key\": $stringifiedNewValue\n$indent- \"$key\": $stringifiedOldValue ";
            },
        'nested' =>
            function ($depth, $node, $fn) {
                ['key' => $key, 'children' => $children] = $node;
                $beginIndent = makeIndent($depth);
                $endIndent = makeIndent($depth + 1);
                $formattedChildren = $fn($children, $depth + 2);
                return "$beginIndent  \"$key\":  {\n{$formattedChildren}\n{$endIndent}}";
            }
    ];

    $elementsOfDiff = array_map(function ($node) use ($nodeHandlers, $depth) {
        ['type' => $type] = $node;
        $handleNode = $nodeHandlers[$type];
        return $handleNode($depth, $node, fn($children, $d) => format($children, $d));
    }, $ast);
    return implode("\n", $elementsOfDiff);
}
