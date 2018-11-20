<?php

namespace Gendiff\Differ\Formatters;

function diffToPretty($diff, $space = '')
{
    $structuredData = array_reduce($diff, function ($acc, $node) use ($space) {
        switch ($node['type']) {
            case 'nested':
                $acc[] = addStringElement($space, '    ', $node['name'], $node['children']);
                break;
            case 'unchanged':
                $acc[] = addStringElement($space, '    ', $node['name'], $node['oldValue']);
                break;
            case 'added':
                $acc[] = addStringElement($space, '  + ', $node['name'], $node['newValue']);
                break;
            case 'removed':
                $acc[] = addStringElement($space, '  - ', $node['name'], $node['oldValue']);
                break;
            case 'changed':
                $acc[] = addStringElement($space, '  - ', $node['name'], $node['oldValue']);
                $acc[] = addStringElement($space, '  + ', $node['name'], $node['newValue']);
                break;
        }

        return $acc;
    }, []);

    return implode('', $structuredData);
}

function makeString($space, $specialSpace, $name, $children)
{
    $colon = $name ? ': ' : '';
    return $space . $specialSpace . $name . $colon . $children . PHP_EOL;
}

function addStringElement($space, $specialSpace, $name, $value)
{
    $acc = [];
    if (!is_array($value)) {
        $acc[] = makeString($space, $specialSpace, $name, $value);
    } else {
        $acc[] = makeString($space, $specialSpace, $name, '{');

        if ($specialSpace !== '    ') {
            $acc[] = arrayToString($value, $space . '    ');
        } else {
            $acc[] = diffToPretty($value, $space . '    ');
        }
        
        $acc[] = makeString($space, '    ', '', '}');
    }
    
    return implode('', $acc);
}

function arrayToString($data, $space)
{
    $space .= '    ';

    return implode('', array_map(function ($key) use ($data, $space) {
        if (is_array($data[$key])) {
            $value = PHP_EOL . arrayToString($data[$key], $space . '    ');
        } else {
            $value = ' ' . $data[$key];
        }
        return "{$space}{$key}:{$value}" . PHP_EOL;
    }, array_keys($data)));
}
