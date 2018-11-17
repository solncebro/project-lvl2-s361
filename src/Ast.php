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

    if (!array_key_exists($key, $dataBefore)) {
        $action = 'added';

        if (is_array($dataAfter[$key])) {
            $nested = true;
            $children = makeAstDiff($dataAfter[$key], $dataAfter[$key]);
        } else {
            $children = $dataAfter[$key];
        }
    } elseif (!array_key_exists($key, $dataAfter)) {
        $action = 'removed';
        
        if (is_array($dataBefore[$key])) {
            $nested = true;
            $children = makeAstDiff($dataBefore[$key], $dataBefore[$key]);
        } else {
            $children = $dataBefore[$key];
        }
    } elseif (is_array($dataBefore[$key]) && is_array($dataAfter[$key])) {
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

    return makeNode($action, $key, $nested, $children, $newChildren);
}

function makeDiff1($key, $dataBefore, $dataAfter)
{
    if (!array_key_exists($key, $dataBefore)) {
        if (is_array($dataAfter[$key])) {
            return makeNode('added', $key, true, makeAstDiff($dataAfter[$key], $dataAfter[$key]));
        }
        return makeNode('added', $key, false, $dataAfter[$key]);
    }

    if (!array_key_exists($key, $dataAfter)) {
        if (is_array($dataBefore[$key])) {
            return makeNode('removed', $key, true, makeAstDiff($dataBefore[$key], $dataBefore[$key]));
        }

        return makeNode('removed', $key, false, $dataBefore[$key]);
    }

    if (is_array($dataBefore[$key]) && is_array($dataAfter[$key])) {
        return makeNode('nested', $key, true, makeAstDiff($dataBefore[$key], $dataAfter[$key]));
    }

    if ($dataBefore[$key] === $dataAfter[$key]) {
        if (is_array($dataBefore[$key])) {
            return makeNode('unchanged', $key, true, makeAstDiff($dataBefore[$key], $dataBefore[$key]));
        }

        return makeNode('unchanged', $key, false, $dataBefore[$key]);
    }

    return makeNode('changed', $key, false, $dataBefore[$key], $dataAfter[$key]);
}
