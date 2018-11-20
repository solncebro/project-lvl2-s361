<?php

namespace Gendiff\DifferTest;

use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff($expected, $result, $filePath1, $filePath2, $format)
    {
        //var_dump(\Gendiff\Differ\gendiff($filePath1, $filePath2, $format), $result);
        $this->assertEquals($expected, $result === \Gendiff\Differ\gendiff($filePath1, $filePath2, $format));
    }

    public function additionProvider()
    {
        $directory = __DIR__ . '/fixtures/';

        $result = file_get_contents($directory . 'result.txt');
        $result2 = file_get_contents($directory . 'result2.txt');
        $resultPlain = file_get_contents($directory . 'resultPlain.txt');
        $resultJson = file_get_contents($directory . 'resultJson.json');

        return [
            [true, $result, $directory . 'before.json', $directory . 'after.json', 'pretty'],
            [false, $result, $directory . 'before.json', $directory . 'after2.json', 'pretty'],
            [false, 'fake1', $directory . 'before.json', $directory . 'after.json', 'pretty'],
            [true, $result, $directory . 'before.yml', $directory . 'after.yml', 'pretty'],
            [false, $result, $directory . 'before.yml', $directory . 'after2.yml', 'pretty'],
            [false, 'fake2', $directory . 'before.yml', $directory . 'after.yml', 'pretty'],
            [true, $result2, $directory . 'before2.json', $directory . 'after2.json', 'pretty'],
            [false, 'fake3', $directory . 'before2.json', $directory . 'after2.json', 'pretty'],
            [true, $result2, $directory . 'before2.yml', $directory . 'after2.yml', 'pretty'],
            [false, 'fake3', $directory . 'before2.yml', $directory . 'after2.yml', 'pretty'],
            [true, $resultPlain, $directory . 'before2.json', $directory . 'after2.json', 'plain'],
            [false, 'fake4', $directory . 'before2.json', $directory . 'after2.json', 'plain'],
            [true, $resultPlain, $directory . 'before2.yml', $directory . 'after2.yml', 'plain'],
            [false, 'fake4', $directory . 'before2.yml', $directory . 'after2.yml', 'plain']
        ];
    }
}
