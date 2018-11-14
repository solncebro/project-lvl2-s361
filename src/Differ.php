<?php

namespace Differ;

use Symfony\Component\Yaml\Yaml;

function genDiff($pathToFile1, $pathToFile2)
{
    $filesContents = readFileToArray($pathToFile1, $pathToFile2);
    if (is_string($filesContents)) {
        return $filesContents;
    }

    [$arrayFile1, $arrayFile2] = $filesContents;

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

    [$textFile1, $extensionFile1] = readLinesAndExtension($pathToFile1);
    [$textFile2, $extensionFile2] = readLinesAndExtension($pathToFile2);

    if (empty($textFile1) || empty($textFile2)) {
        return "One or two files are empty. Terminating..." . PHP_EOL;
    }

    $arrayFile1 = decode($textFile1, $extensionFile1);
    $arrayFile2 = decode($textFile2, $extensionFile2);

    if (is_null($arrayFile1) || is_null($arrayFile2)) {
        return "Can't decode JSON to array. Terminating..." . PHP_EOL;
    }

    $normalizedFile1 = boolToString($arrayFile1);
    $normalizedFile2 = boolToString($arrayFile2);

    return [$normalizedFile1, $normalizedFile2];
}

function readLinesAndExtension(string $pathToFile)
{
    $file = new \SplFileObject($pathToFile);
    $extension = $file->getExtension();
    $text = '';

    while ($file->eof()) {
        $arr = $file->fgets();
    }

    foreach ($file as $line => $value) {
        $text .= $value;
    }
 
    return [$text, $extension];
}

function decode($textFile, $extensionFile)
{
    if ($extensionFile === 'json') {
        return json_decode($textFile, true);
    } elseif ($extensionFile === 'yml') {
        $yaml = Yaml::parse($textFile);
        return $yaml;
    } else {
        return "Unsuppoted file. Can't decoded to array. Terminating...";
    }
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
