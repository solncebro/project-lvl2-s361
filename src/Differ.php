<?php

namespace Gendiff\Differ;

use function Gendiff\Decoder\decode;
use function Gendiff\Ast\makeAstDiff;

function genDiff($pathToFile1, $pathToFile2, $format)
{
    $fileText1 = readFile($pathToFile1);
    $fileText2 = readFile($pathToFile2);
    $diff = makeAstDiff($fileText1, $fileText2);
    
    if ($format === "plain") {
        $text = diffToPlain($diff);
        return implode(PHP_EOL, $text) . PHP_EOL;
    }

    $text = diffToPretty($diff);
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

function diffToPretty($diff, $space = '')
{
    $structuredData = array_reduce($diff, function ($acc, $parent) use ($space) {
        $type = $parent['type'];
        $children = $parent['children'];

        if ($type === 'removed' || $type === 'changed') {
            $specialSpace = '  - ';
        } elseif ($type === 'added') {
            $specialSpace = '  + ';
        } else {
            $specialSpace = '    ';
        }
        
        if ($parent['nested']) {
            $acc[] = makeString($space, $specialSpace, $parent['key'], '{');
            $acc[] = diffToPretty($children, $space . '    ');
            $acc[] = makeString($space, '    ', '', '}');
        } else {
            $acc[] = makeString($space, $specialSpace, $parent['key'], $children);
            if (!is_null($parent['newChildren'])) {
                $acc[] = makeString($space, '  + ', $parent['key'], $parent['newChildren']);
            }
        }
        
        return $acc;
    }, []);

    return implode('', $structuredData);
}

function makeString($space, $specialSpace, $key, $children)
{
    $colon = $key ? ': ' : '';
    return $space . $specialSpace . $key . $colon . $children . PHP_EOL;
}

function diffToPlain($diff, $parentPath = [])
{
    $actionMessages = array_reduce($diff, function ($acc, $parent) use ($parentPath) {
        $type = $parent['type'];
        $children = $parent['children'];
        $parentPath[] = $parent['key'];
        
        if ($type === 'unchanged') {
            if (!$parent['nested']) {
                return $acc;
            }

            $acc = array_merge($acc, diffToPlain($children, $parentPath));
        } else {
            if ($type === 'added') {
                if ($parent['nested']) {
                    $children = 'complex value';
                }
                $details = " with value: '{$children}'";
            } elseif ($type === 'changed') {
                $details = ". From '{$children}' to '{$parent['newChildren']}'";
            } else {
                $details = "";
            }

            $path = implode(".", $parentPath);
            $acc[] = "Property '{$path}' was {$type}{$details}";
        }
        
        return $acc;
    }, []);

    return $actionMessages;
}
