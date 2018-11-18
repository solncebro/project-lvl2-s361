<?php

namespace Gendiff\Differ\Format;

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
