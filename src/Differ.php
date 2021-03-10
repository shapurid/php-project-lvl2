<?php

namespace Differ\Differ;

use Exception;

use function Differ\Parsers\parse;
use function Differ\Render\render;
use function Differ\BuildAst\buildAst;

function getFileContent(string $pathToFile): string
{
    $absolutePathToFile = (string) realpath($pathToFile);
    if (!file_exists($absolutePathToFile)) {
        throw new Exception("File '{$pathToFile}' does not exists!");
    }
    $contentOfFile = file_get_contents($absolutePathToFile, true);
    if (!$contentOfFile) {
        throw new Exception("Can't get content of file '{$pathToFile}'!");
    }
    return $contentOfFile;
}

function getFileFormat(string $pathToFile): string
{
    return pathinfo($pathToFile, PATHINFO_EXTENSION);
}

/**
 * @return string|false
 */

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish')
{
    $contentOfFile1 = getFileContent($pathToFile1);
    $contentOfFile2 = getFileContent($pathToFile2);

    $formatOfFile1 = getFileFormat($pathToFile1);
    $formatOfFile2 = getFileFormat($pathToFile2);

    $parsedContentOfFile1 = parse($contentOfFile1, $formatOfFile1);
    $parsedContentOfFile2 = parse($contentOfFile2, $formatOfFile2);

    $ast = buildAst($parsedContentOfFile1, $parsedContentOfFile2);
    return render($ast, $format);
}
