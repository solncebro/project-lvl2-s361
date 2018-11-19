<?php

namespace Gendiff\Differ\Formatters;

function diffToPlain($diff, $nodePath = [])
{
    $actionMessages = array_reduce($diff, function ($acc, $node) use ($nodePath) {
        $type = $node['type'];
        $nodePath[] = $node['name'];
        
        switch ($type) {
            case 'nested':
                $acc = array_merge($acc, diffToPlain($node['oldValue'], $nodePath));
                return $acc;
            case 'removed':
                $details = "";
                break;
            case 'added':
                $value = is_array($node['newValue']) ? 'complex value' : $node['newValue'];
                $details = " with value: '{$value}'";
                break;
            case 'changed':
                $details = ". From '{$node['oldValue']}' to '{$node['newValue']}'";
                break;
            default:
                return $acc;
        }

        $path = implode(".", $nodePath);
        $acc[] = "Property '{$path}' was {$type}{$details}";
        
        return $acc;
    }, []);

    return $actionMessages;
}
