<?php

namespace Gendiff\DifferTest;

use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    private $dir = __DIR__ . '/fixtures/';

    public function testReadError()
    {
        try {
            $result = \Gendiff\Differ\gendiff('notExistFile.json', 'notExistFile2.json', 'pretty');
            $this->fail('ReadError exception expected, but not happen');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testFileEmpty()
    {
        try {
            $result = \Gendiff\Differ\gendiff($this->dir . 'before.json', $this->dir . 'empty.json', 'pretty');
            $this->fail('FileEmpty exception expected, but not happen');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testDecodeError()
    {
        try {
            $result = \Gendiff\Differ\gendiff($this->dir . 'before.json', $this->dir . 'result.txt', 'pretty');
            $this->fail('DecodeError exception expected, but not happen');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
