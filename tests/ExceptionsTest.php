<?php

namespace Gendiff\DifferTest;

use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    private $dir = __DIR__ . '/fixtures/';

    public function testReadError()
    {
        try {
            $result = \Gendiff\Differ\gendiff('notExistFile.json', 'notExistFile2.json');
            $this->fail('ReadError exception expected, but not happen');
        } catch (\Gendiff\Exceptions\ReadError $e) {
            $this->assertTrue(true);
        }
    }

    public function testFileEmpty()
    {
        try {
            $result = \Gendiff\Differ\gendiff($this->dir . 'before.json', $this->dir . 'empty.json');
            $this->fail('FileEmpty exception expected, but not happen');
        } catch (\Gendiff\Exceptions\FileEmpty $e) {
            $this->assertTrue(true);
        }
    }

    public function testDecodeError()
    {
        try {
            $result = \Gendiff\Differ\gendiff($this->dir . 'before.json', $this->dir . 'result.txt');
            $this->fail('DecodeError exception expected, but not happen');
        } catch (\Gendiff\Exceptions\DecodeError $e) {
            $this->assertTrue(true);
        }
    }
}
