<?php

namespace Differ\Cli;

use Docopt;

use function Differ\Differ\genDiff;

const DOC = <<<DOCS

Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]

DOCS;

function run(): void
{
    $args = Docopt::handle(DOC, ['version' => '0.0.1']);

    [
      '<firstFile>' => $pathOfFile1,
      '<secondFile>' => $pathOfFile2,
      '--format' => $format
    ] = $args;

    echo genDiff($pathOfFile1, $pathOfFile2, $format);
}
