<?php

namespace Gendiff\Ast;

function boolToString($value)
{
    return $value ? "true" : "false";
}

function makeNode($type, $key, $nested, $children, $newChildren)
{
    return [
        'type' => $type,
        'key' => $key,
        'nested' => $nested,
        'children' => is_bool($children) ? boolToString($children) : $children,
        'newChildren' => is_bool($newChildren) ? boolToString($newChildren) : $newChildren
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
    $nested = false;
    $newChildren = null;

    if (!array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)) {
        $action = 'added';

        if (is_array($dataAfter[$key])) {
            $nested = true;
            $children = makeAstDiff($dataAfter[$key], $dataAfter[$key]);
        } else {
            $children = $dataAfter[$key];
        }
    } elseif (array_key_exists($key, $dataBefore) && !array_key_exists($key, $dataAfter)) {
        $action = 'removed';
        
        if (is_array($dataBefore[$key])) {
            $nested = true;
            $children = makeAstDiff($dataBefore[$key], $dataBefore[$key]);
        } else {
            $children = $dataBefore[$key];
        }
    } else {
        if (is_array($dataBefore[$key]) && is_array($dataAfter[$key])) {
            $action = 'unchanged';
            $nested = true;
            $children = makeAstDiff($dataBefore[$key], $dataAfter[$key]);
        } elseif ($dataBefore[$key] === $dataAfter[$key]) {
            $action = 'unchanged';

            if (is_array($dataBefore[$key])) {
                $nested = true;
                $children = makeAstDiff($dataBefore[$key], $dataBefore[$key]);
            } else {
                $children = $dataBefore[$key];
            }
        } else {
            $action = 'changed';
            $children = $dataBefore[$key];
            $newChildren = $dataAfter[$key];
        }
    }
    
    return makeNode($action, $key, $nested, $children, $newChildren);
}
