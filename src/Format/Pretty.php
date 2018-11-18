<?php

namespace Gendiff\Differ\Format;

function diffToPretty($diff, $space = '')
{
    $structuredData = array_reduce($diff, function ($acc, $parent) use ($space) {
        $type = $parent['type'];
        $children = $parent['children'];

        if ($type === 'removed' || $type === 'changed') {
            $specialSpace = '  - ';
        } elseif ($type === 'added') {
            $specialSpace = '  + ';
        } else {
            $specialSpace = '    ';
        }
        
        if ($parent['nested']) {
            $acc[] = makeString($space, $specialSpace, $parent['key'], '{');
            $acc[] = diffToPretty($children, $space . '    ');
            $acc[] = makeString($space, '    ', '', '}');
        } else {
            $acc[] = makeString($space, $specialSpace, $parent['key'], $children);
            if (!is_null($parent['newChildren'])) {
                $acc[] = makeString($space, '  + ', $parent['key'], $parent['newChildren']);
            }
        }
        
        return $acc;
    }, []);

    return implode('', $structuredData);
}

function makeString($space, $specialSpace, $key, $children)
{
    $colon = $key ? ': ' : '';
    return $space . $specialSpace . $key . $colon . $children . PHP_EOL;
}
