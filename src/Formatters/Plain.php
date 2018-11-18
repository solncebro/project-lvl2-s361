<?php

namespace Gendiff\Differ\Formatters;

function diffToPlain($diff, $parentPath = [])
{
    $actionMessages = array_reduce($diff, function ($acc, $parent) use ($parentPath) {
        $type = $parent['type'];
        $children = $parent['children'];
        $parentPath[] = $parent['key'];
        
        $details = "";
        
        switch ($type) {
            case 'unchanged':
                if (!$parent['nested']) {
                    return $acc;
                }
                $acc = array_merge($acc, diffToPlain($children, $parentPath));
                return $acc;
            case 'added':
                if ($parent['nested']) {
                    $children = 'complex value';
                }
                $details = " with value: '{$children}'";
                break;
            case 'changed':
                $details = ". From '{$children}' to '{$parent['newChildren']}'";
                break;
        }

        $path = implode(".", $parentPath);
        $acc[] = "Property '{$path}' was {$type}{$details}";
        
        return $acc;
    }, []);

    return $actionMessages;
}
