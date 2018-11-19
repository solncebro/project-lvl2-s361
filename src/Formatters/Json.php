<?php

namespace Gendiff\Differ\Formatters;

function diffToJson($diff)
{
    $structuredData = array_reduce($diff, function ($acc, $node) {
        $key = $node['name'];
        
        switch ($node['type']) {
            case 'nested':
            case 'unchanged':
                $acc[$key] = is_array($node['oldValue']) ? diffToJson($node['oldValue']) : $node['oldValue'];
                break;
            case 'changed':
            case 'added':
                $acc[$key] = is_array($node['newValue']) ? diffToJson($node['newValue']) : $node['newValue'];
                break;
        }
        return $acc;
    }, []);

    return $structuredData;
}
