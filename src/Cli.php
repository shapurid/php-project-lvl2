<?php

namespace App\Cli;

const DOC = <<<DOC

Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]

DOC;

function genDiff()
{
    \Docopt::handle(DOC, ['version' => '0.0.1']);
}
