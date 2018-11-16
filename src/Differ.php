<?php

namespace Gendiff\Differ;

use function Gendiff\Decoder\decode;
use Gendiff\Exceptions\ReadError;
use Gendiff\Exceptions\FileEmpty;
use Gendiff\Exceptions\DecodeError;

function genDiff($pathToFile1, $pathToFile2)
{
    $fileText1 = readFile($pathToFile1);
    $fileText2 = readFile($pathToFile2);

    $arrayMergedKeys = array_keys(array_merge($fileText1, $fileText2));

    $arr = array_reduce($arrayMergedKeys, function ($acc, $key) use ($fileText1, $fileText2) {
        if (array_key_exists($key, $fileText2) && array_key_exists($key, $fileText1)) {
            if ($fileText1[$key] === $fileText2[$key]) {
                $value = boolToString($fileText1[$key]);
                $acc[] = "    {$key}: {$value}";
                return $acc;
            }
        }

        if (array_key_exists($key, $fileText2)) {
            $value = boolToString($fileText2[$key]);
            $acc[] = "  + {$key}: {$value}";
        }

        if (array_key_exists($key, $fileText1)) {
            $value = boolToString($fileText1[$key]);
            $acc[] = "  - {$key}: {$value}";
        }

        return $acc;
    }, []);

    return "{" . PHP_EOL . implode(PHP_EOL, $arr) . PHP_EOL . "}";
}

function readFile(string $pathToFile)
{
    if (!is_readable($pathToFile)) {
        throw new ReadError("Can't read file {$pathToFile}. Terminating..." . PHP_EOL);
    }

    $textFile = file_get_contents($pathToFile);
    if (empty($textFile)) {
        throw new FileEmpty("The file {$pathToFile} is empty. Terminating..." . PHP_EOL);
    }

    $pathInfo = pathinfo($pathToFile);
    $decodedFile = decode($textFile, $pathInfo['extension']);
    if (is_null($decodedFile)) {
        throw new DecodeError("Can't decode file {$pathToFile} to array. Terminating..." . PHP_EOL);
    }

    return $decodedFile;
}

function boolToString($value) : string
{
    if (gettype($value) === 'boolean') {
        return $value ? "true" : "false";
    }
    return $value;
}
