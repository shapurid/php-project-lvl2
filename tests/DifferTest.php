<?php

namespace Differ\Tests;

use Exception;
use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @return string|false
     */

    public function getExpectedDiff(string $format)
    {
        $formatHandlers = [
            'stylish' => file_get_contents(dirname(__DIR__) . '/tests/fixtures/resultStylish.txt'),
            'plain' => file_get_contents(dirname(__DIR__) . '/tests/fixtures/resultPlain.txt'),
            'json' => file_get_contents(dirname(__DIR__) . '/tests/fixtures/resultJson.txt'),
        ];
        if (!array_key_exists($format, $formatHandlers)) {
            throw new Exception('This format does not support!');
        }
        return $formatHandlers[$format];
    }

    /**
     * @return array<string>
     */

    public function getPathsOfFiles(string $nameOfFile1, string $nameOfFile2)
    {
        $pathOfFile1 = dirname(__DIR__) . "/tests/fixtures/$nameOfFile1";
        $pathOfFile2 = dirname(__DIR__) . "/tests/fixtures/$nameOfFile2";
        return [$pathOfFile1, $pathOfFile2];
    }

    public function testJsonStylish(): void
    {
        [$pathOfFile1, $pathOfFile2] = $this->getPathsOfFiles('before.json', 'after.json');
        $data = genDiff($pathOfFile1, $pathOfFile2);
        $this->assertEquals($this->getExpectedDiff('stylish'), $data);
    }
    public function testYmlYamlStylish(): void
    {
        [$pathOfFile1, $pathOfFile2] = $this->getPathsOfFiles('before.yml', 'after.yaml');
        $data = genDiff($pathOfFile1, $pathOfFile2);
        $this->assertEquals($this->getExpectedDiff('stylish'), $data);
    }
    public function testJsonPlain(): void
    {
        [$pathOfFile1, $pathOfFile2] = $this->getPathsOfFiles('before.json', 'after.json');
        $data = genDiff($pathOfFile1, $pathOfFile2, 'plain');
        $this->assertEquals($this->getExpectedDiff('plain'), $data);
    }
    public function testYmlYamlPlain(): void
    {
        [$pathOfFile1, $pathOfFile2] = $this->getPathsOfFiles('before.yml', 'after.yaml');
        $data = genDiff($pathOfFile1, $pathOfFile2, 'plain');
        $this->assertEquals($this->getExpectedDiff('plain'), $data);
    }
    public function testJsonJson(): void
    {
        [$pathOfFile1, $pathOfFile2] = $this->getPathsOfFiles('before.json', 'after.json');
        $data = genDiff($pathOfFile1, $pathOfFile2, 'json');
        $this->assertEquals($this->getExpectedDiff('json'), $data);
    }
    public function testYmlYamlJson(): void
    {
        [$pathOfFile1, $pathOfFile2] = $this->getPathsOfFiles('before.yml', 'after.yaml');
        $data = genDiff($pathOfFile1, $pathOfFile2, 'json');
        $this->assertEquals($this->getExpectedDiff('json'), $data);
    }
}
