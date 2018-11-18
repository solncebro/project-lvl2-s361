<?php

namespace Gendiff\Differ;

use function Gendiff\Decoder\decode;
use function Gendiff\Ast\makeAstDiff;

function genDiff($pathToFile1, $pathToFile2, $format)
{
    $fileText1 = readFile($pathToFile1);
    $fileText2 = readFile($pathToFile2);
    $diff = makeAstDiff($fileText1, $fileText2);
    
    if (mb_strtolower($format) === "plain") {
        $text = \Gendiff\Differ\Format\diffToPlain($diff);
        return implode(PHP_EOL, $text) . PHP_EOL;
    } elseif (mb_strtolower($format) === "json") {
        $data = \Gendiff\Differ\Format\diffToJson($diff);
        return json_encode($data);
    }

    $text = \Gendiff\Differ\Format\diffToPretty($diff);
    return '{' . PHP_EOL . $text . '}' . PHP_EOL;
}

function readFile(string $pathToFile)
{
    if (!is_readable($pathToFile)) {
        throw new \Exception("Can't read file {$pathToFile}. Terminating..." . PHP_EOL);
    }

    $textFile = file_get_contents($pathToFile);
    if (empty($textFile)) {
        throw new \Exception("The file {$pathToFile} is empty. Terminating..." . PHP_EOL);
    }

    $pathInfo = pathinfo($pathToFile);
    $decodedFile = decode($textFile, $pathInfo['extension']);
    if (is_null($decodedFile)) {
        throw new \Exception("Can't decode file {$pathToFile} to array. Terminating..." . PHP_EOL);
    }

    return $decodedFile;
}
