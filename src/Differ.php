<?php

namespace Differ\Differ;

use Exception;

use function Differ\Parsers\parse;
use function Differ\Render\render;
use function Differ\BuildAst\buildAst;

function getFileContent(string $pathToFile): \stdClass
{
    $fileContent = file_get_contents($pathToFile, true);
    $format = pathinfo($pathToFile, PATHINFO_EXTENSION);
    return parse($fileContent, $format);
}

/**
 * @return string|false
 */

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish')
{
    $absolutePathToFile1 = (string) realpath($pathToFile1);
    $absolutePathToFile2 = (string) realpath($pathToFile2);
    if (!file_exists($absolutePathToFile1)) {
        throw new Exception("File '{$pathToFile1}' does not exists!");
    }
    if (!file_exists($absolutePathToFile2)) {
        throw new Exception("File '{$pathToFile2}' does not exists!");
    }
    $content1 = getFileContent($absolutePathToFile1);
    $content2 = getFileContent($absolutePathToFile2);

    $ast = buildAst($content1, $content2);
    print_r($ast);
    return render($ast, $format);
}
