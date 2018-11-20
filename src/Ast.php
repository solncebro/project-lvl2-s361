<?php

namespace Gendiff\Ast;

function boolToString($value)
{
    return $value ? "true" : "false";
}

function makeNode($type, $name, $oldValue, $newValue, $children = null)
{
    if (is_array($newValue) && $type === 'added') {
        $children = makeAstDiff($newValue, $newValue);
    }
    
    if (is_array($oldValue) && $type === 'removed') {
        $children = makeAstDiff($oldValue, $oldValue);
    }

    return [
        'type' => $type,
        'name' => $name,
        'oldValue' => is_bool($oldValue) ? boolToString($oldValue) : $oldValue,
        'newValue' => is_bool($newValue) ? boolToString($newValue) : $newValue,
        'children' => $children
    ];
}

function makeAstDiff($dataBefore, $dataAfter)
{
    $mergedKeys = array_keys(array_merge($dataBefore, $dataAfter));

    return array_map(function ($key) use ($dataBefore, $dataAfter) {
        return makeDiff($key, $dataBefore, $dataAfter);
    }, $mergedKeys);
}

function makeDiff($key, $dataBefore, $dataAfter)
{
    $children = null;

    if (!array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)) {
        $type = 'added';
        $oldValue = null;
        $newValue = $dataAfter[$key];
    } elseif (array_key_exists($key, $dataBefore) && !array_key_exists($key, $dataAfter)) {
        $type = 'removed';
        $oldValue = $dataBefore[$key];
        $newValue = null;
    } elseif (is_array($dataBefore[$key]) && is_array($dataAfter[$key])) {
        $type = 'nested';
        $children = makeAstDiff($dataBefore[$key], $dataAfter[$key]);
        $oldValue = $dataBefore[$key];
        $newValue = $dataAfter[$key];
    } elseif ($dataBefore[$key] === $dataAfter[$key]) {
        $type = 'unchanged';
        $oldValue = $dataBefore[$key];
        $newValue = null;
    } else {
        $type = 'changed';
        $oldValue = $dataBefore[$key];
        $newValue = $dataAfter[$key];
    }
    
    return makeNode($type, $key, $oldValue, $newValue, $children);
}
