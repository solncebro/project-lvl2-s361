<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        vfsStream::setup('home');
        $file1 = vfsStream::url('home/before.json');
        file_put_contents($file1, '{
            "host": "hexlet.io",
            "timeout": 50,
            "proxy": "123.234.53.22"
          }');

        $file2 = vfsStream::url('home/after.json');
        file_put_contents($file2, '{
            "timeout": 20,
            "verbose": true,
            "host": "hexlet.io"
          }');

          
        $result = [
            "{",
            "    host: hexlet.io",
            "  + timeout: 20",
            "  - timeout: 50",
            "  - proxy: 123.234.53.22",
            "  + verbose: true",
            "}"
        ];

        $this->assertEquals(implode(PHP_EOL, $result), \Differ\gendiff($file1, $file2));

        $file3 = "after2.json";
        $this->assertNotEquals(implode(PHP_EOL, $result), \Differ\gendiff($file1, $file3));

        $result2 = [];
        $this->assertNotEquals(implode(PHP_EOL, $result2), \Differ\gendiff($file1, $file2));
    }
}
