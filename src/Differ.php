<?php

namespace Gendiff\Differ;

use function Gendiff\Decoder\decode;
use function Gendiff\Ast\makeAstDiff;

function genDiff($filePath1, $filePath2, $format = 'pretty')
{
    $data1 = getData($filePath1);
    $data2 = getData($filePath2);
    $diff = makeAstDiff($data1, $data2);
    
    return convertDiff($diff, $format);
}

function convertDiff($diff, $format)
{
    if (mb_strtolower($format) === "plain") {
        $text = \Gendiff\Differ\Formatters\diffToPlain($diff);
        return implode(PHP_EOL, $text) . PHP_EOL;
    } elseif (mb_strtolower($format) === "json") {
        $data = \Gendiff\Differ\Formatters\diffToJson($diff);
        return json_encode($data);
    } elseif (mb_strtolower($format) === "pretty") {
        $text = \Gendiff\Differ\Formatters\diffToPretty($diff);
        return '{' . PHP_EOL . $text . '}' . PHP_EOL;
    }

    throw new \Exception("Unsupported gendiff --format {$format}. Terminating..." . PHP_EOL);
}

function getData(string $filePath)
{
    if (!is_readable($filePath)) {
        throw new \Exception("Can't read file {$filePath}. Terminating..." . PHP_EOL);
    }

    $content = file_get_contents($filePath);
    if (empty($content)) {
        throw new \Exception("The file {$filePath} is empty. Terminating..." . PHP_EOL);
    }

    $pathInfo = pathinfo($filePath);
    $decodedContent = decode($content, $pathInfo['extension']);

    return $decodedContent;
}
