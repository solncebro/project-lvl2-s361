<?php

namespace Gendiff\Differ;

use function Gendiff\Decoder\decode;

function genDiff($pathToFile1, $pathToFile2)
{
    $fileTextArray1 = readFileToArray($pathToFile1);
    $fileTextArray2 = readFileToArray($pathToFile2);
    
    if (is_string($fileTextArray1) || is_string($fileTextArray2)) {
        return "Can't convert one or two files to array. Terminating...";
    }

    $arrayMergedKeys = array_keys(array_merge($fileTextArray1, $fileTextArray2));

    $arr = array_reduce($arrayMergedKeys, function ($acc, $key) use ($fileTextArray1, $fileTextArray2) {
        if (array_key_exists($key, $fileTextArray2) && array_key_exists($key, $fileTextArray1)) {
            if ($fileTextArray1[$key] === $fileTextArray2[$key]) {
                $value = boolToString($fileTextArray1[$key]);
                $acc[] = "    {$key}: {$value}";
                return $acc;
            }
        }

        if (array_key_exists($key, $fileTextArray2)) {
            $value = boolToString($fileTextArray2[$key]);
            $acc[] = "  + {$key}: {$value}";
        }

        if (array_key_exists($key, $fileTextArray1)) {
            $value = boolToString($fileTextArray1[$key]);
            $acc[] = "  - {$key}: {$value}";
        }

        return $acc;
    }, []);

    return "{" . PHP_EOL . implode(PHP_EOL, $arr) . PHP_EOL . "}";
}

function readFileToArray(string $pathToFile)
{
    if (!is_readable($pathToFile)) {
        return("Can't read file {$pathToFile}. Terminating..." . PHP_EOL);
    }

    $textFile = file_get_contents($pathToFile);
    if (empty($textFile)) {
        return "The file {$pathToFile} is empty. Terminating..." . PHP_EOL;
    }

    $arrayFile = decode($textFile, getExtension($pathToFile));
    if (is_null($arrayFile)) {
        return "Can't decode file {$pathToFile} to array. Terminating..." . PHP_EOL;
    }

    return $arrayFile;
}

function getExtension($pathToFile)
{
    $fileInfo = new \SplFileInfo($pathToFile);
    return $fileInfo->getExtension();
}

function boolToString($value) : string
{
    if (gettype($value) === 'boolean') {
        return $value ? "true" : "false";
    }
    return $value;
}
