<?php

namespace Gendiff\Differ\Formatters;

function diffToPretty($diff, $space = '')
{
    $structuredData = array_reduce($diff, function ($acc, $node) use ($space) {
        switch ($node['type']) {
            case 'nested':
                $acc[] = addNestedStrings($space, '    ', $node['name'], $node['oldValue']);
                break;
            case 'unchanged':
                $acc[] = makeString($space, '    ', $node['name'], $node['oldValue']);
                break;
            case 'added':
                $acc[] = makeString($space, '  + ', $node['name'], $node['newValue']);
                break;
            case 'removed':
                $acc[] = makeString($space, '  - ', $node['name'], $node['oldValue']);
                break;
            case 'changed':
                $acc[] = makeString($space, '  - ', $node['name'], $node['oldValue']);
                $acc[] = makeString($space, '  + ', $node['name'], $node['newValue']);
                break;
        }

        return $acc;
    }, []);

    return implode('', $structuredData);
}

function makeString($space, $specialSpace, $name, $children)
{
    if (is_array($children)) {
        return addNestedStrings($space, $specialSpace, $name, $children);
    }

    $colon = $name ? ': ' : '';
    return $space . $specialSpace . $name . $colon . $children . PHP_EOL;
}

function addNestedStrings($space, $specialSpace, $name, $children)
{
    $acc = [];
    $acc[] = makeString($space, $specialSpace, $name, '{');
    $acc[] = diffToPretty($children, $space . '    ');
    $acc[] = makeString($space, '    ', '', '}');
    
    return implode('', $acc);
}
