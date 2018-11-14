<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class TestGenDiff extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff($expected, $result, $file1, $file2)
    {
        $this->assertEquals($expected, $result == \Differ\gendiff($file1, $file2));
    }

    public function additionProvider()
    {
        $directory = __DIR__ . '/../';

        $result = implode(PHP_EOL, [
            "{",
            "    host: hexlet.io",
            "  + timeout: 20",
            "  - timeout: 50",
            "  - proxy: 123.234.53.22",
            "  + verbose: true",
            "}"
        ]);
        
        return [
            [true, $result, $directory . 'before.json', $directory . 'after.json'],
            [false, $result, $directory . 'before.json', $directory . 'after2.json'],
            [false, 'fake', $directory . 'before.json', $directory . 'after.json'],
            [true, $result, $directory . 'before.yml', $directory . 'after.yml'],
            [false, $result, $directory . 'before.yml', $directory . 'after2.yml'],
            [false, 'fake', $directory . 'before.yml', $directory . 'after.yml']
        ];
    }
}
