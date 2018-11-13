<?php

namespace Differ;

//  $pathToFile1 = "/Users/andreikholkin/Sites/gendiff/before.json";
//  $pathToFile2 = "/Users/andreikholkin/Sites/gendiff/after.json";

//  genDiff($pathToFile1, $pathToFile2);

function genDiff($pathToFile1, $pathToFile2)
{
    $filesArrays = readFileToArray($pathToFile1, $pathToFile2);
    if (is_string($filesArrays)) {
        return $filesArrays;
    }

    [$arrayFile1, $arrayFile2] = $filesArrays;

    $arrayMergedKeys = array_keys(array_merge($arrayFile1, $arrayFile2));

    $arr = array_reduce($arrayMergedKeys, function ($acc, $key) use ($arrayFile1, $arrayFile2) {
        if (array_key_exists($key, $arrayFile2) && array_key_exists($key, $arrayFile1)) {
            if ($arrayFile1[$key] === $arrayFile2[$key]) {
                $acc[] = "    {$key}: {$arrayFile1[$key]}";
                return $acc;
            }
        }

        if (array_key_exists($key, $arrayFile2)) {
            $value = strval($arrayFile2[$key]);
            $acc[] = "  + {$key}: {$value}";
        }

        if (array_key_exists($key, $arrayFile1)) {
            $acc[] = "  - {$key}: {$arrayFile1[$key]}";
        }

        return $acc;
    }, []);

    return "{" . PHP_EOL . implode(PHP_EOL, $arr) . PHP_EOL . "}";
}

function readFileToArray(string $pathToFile1, string $pathToFile2)
{
    if (!is_readable($pathToFile1) || !is_readable($pathToFile2)) {
        return "Can't read one or two files. Terminating..." . PHP_EOL;
    }

    $textFile1 = readJson($pathToFile1);
    $textFile2 = readJson($pathToFile2);

    if (empty($textFile1) || empty($textFile2)) {
        return "One or two files are empty. Terminating..." . PHP_EOL;
    }

    $arrayFile1 = json_decode($textFile1, true);
    $arrayFile2 = json_decode($textFile2, true);

    if (is_null($arrayFile1) || is_null($arrayFile2)) {
        return "Can't decode JSON to array. Terminating..." . PHP_EOL;
    }

    $normalizedFile1 = boolToString($arrayFile1);
    $normalizedFile2 = boolToString($arrayFile2);

    return [$normalizedFile1, $normalizedFile2];
}

function readJson(string $pathToFile) : string
{
    $file = new \SplFileObject($pathToFile);
    $text = '';

    while ($file->eof()) {
        $arr = $file->fgets();
    }

    foreach ($file as $line => $value) {
        $text .= $value;
    }
 
    return $text;
}

function boolToString(array $arr) : array
{
    foreach ($arr as $key => $value) {
        if (gettype($value) === "boolean") {
            $arr[$key] = $value ? "true" : "false";
        }
    }

    return $arr;
}
