<?php

namespace Differ\Differ;

use Exception;

use function Differ\Parsers\parse;
use function Differ\Render\render;
use function Differ\BuildAst\buildAst;

function getFileContent(string $pathToFile): \stdClass
{
    $absolutePath = realpath($pathToFile);
    $fileContent = file_get_contents($absolutePath, true);
    $format = pathinfo($pathToFile, PATHINFO_EXTENSION);
    return parse($fileContent, $format);
}

function genDiff(string $pathToFile1, string $pathToFile2, $format = 'stylish')
{
    if (!file_exists($pathToFile1)) {
        throw new Exception("File '{$pathToFile1}' does not exists!");
    }
    if (!file_exists($pathToFile2)) {
        throw new Exception("File '{$pathToFile2}' does not exists!");
    }
    $content1 = getFileContent($pathToFile1);
    $content2 = getFileContent($pathToFile2);

    $ast = buildAst($content1, $content2);
    return render($ast, $format);
}
