<?php

namespace Gendiff\DifferTest;

use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff($expected, $result, $filePath1, $filePath2)
    {
        $this->assertEquals($expected, $result === \Gendiff\Differ\gendiff($filePath1, $filePath2));
    }

    public function additionProvider()
    {
        $directory = __DIR__ . '/fixtures/';

        $result = file_get_contents($directory . 'result.txt');
        $result2 = file_get_contents($directory . 'result2.txt');

        return [
            [true, $result, $directory . 'before.json', $directory . 'after.json'],
            [false, $result, $directory . 'before.json', $directory . 'after2.json'],
            [false, 'fake1', $directory . 'before.json', $directory . 'after.json'],
            [true, $result, $directory . 'before.yml', $directory . 'after.yml'],
            [false, $result, $directory . 'before.yml', $directory . 'after2.yml'],
            [false, 'fake2', $directory . 'before.yml', $directory . 'after.yml'],
            [true, $result2, $directory . 'before2.json', $directory . 'after2.json'],
            [false, 'fake3', $directory . 'before2.json', $directory . 'after2.json'],
            [true, $result2, $directory . 'before2.yml', $directory . 'after2.yml'],
            [false, 'fake3', $directory . 'before2.yml', $directory . 'after2.yml']
        ];
    }
}
