<?php

namespace App\Differ;

use Exception;

use function App\Parsers\parse;
use function App\Render\render;
use function App\BuildAst\buildAst;

function getFileContent(string $pathToFile): \stdClass
{
    $fileContent = file_get_contents($pathToFile, true);
    $format = pathinfo($pathToFile, PATHINFO_EXTENSION);
    return parse($fileContent, $format);
}

function genDiff(string $pathToFile1, string $pathToFile2, $format)
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
