<?php

namespace Gendiff\Differ\Formatters;

function diffToPretty($diff, $space = '')
{
    $structuredData = array_reduce($diff, function ($acc, $node) use ($space) {
        switch ($node['type']) {
            case 'nested':
                $acc[] = addStringElement($space, '    ', $node, $node['oldValue']);
                break;
            case 'unchanged':
                $acc[] = addStringElement($space, '    ', $node, $node['oldValue']);
                break;
            case 'added':
                $acc[] = addStringElement($space, '  + ', $node, $node['newValue']);
                break;
            case 'removed':
                $acc[] = addStringElement($space, '  - ', $node, $node['oldValue']);
                break;
            case 'changed':
                $acc[] = addStringElement($space, '  - ', $node, $node['oldValue']);
                $acc[] = addStringElement($space, '  + ', $node, $node['newValue']);
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

function addStringElement($space, $specialSpace, $node, $value)
{
    $acc = [];
    if (!is_array($node['children'])) {
        $acc[] = makeString($space, $specialSpace, $node['name'], $value);
    } else {
        $acc[] = makeString($space, $specialSpace, $node['name'], '{');
        $acc[] = diffToPretty($node['children'], $space . '    ');
        $acc[] = makeString($space, '    ', '', '}');
    }
    
    return implode('', $acc);
}
