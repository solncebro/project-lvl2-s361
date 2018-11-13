<?php

namespace DifferTest;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $file1 = vfsStream::url('home/before.json');
        file_put_contents($file, '{
            "host": "hexlet.io",
            "timeout": 50,
            "proxy": "123.234.53.22"
          }');

        $file2 = vfsStream::url('home/after.json');
        file_put_contents($file, '{
            "timeout": 20,
            "verbose": true,
            "host": "hexlet.io"
          }');

        $result = '{
            host: hexlet.io
          + timeout: 20
          - timeout: 50
          - proxy: 123.234.53.22
          + verbose: true
        }';

        $this->assertEquals($result, \Differ\gendiff($file1, $file2));
    }
}
