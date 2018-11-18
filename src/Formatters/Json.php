<?php

namespace Gendiff\Differ\Formatters;

function diffToJson($diff, $space = '')
{
    $structuredData = array_reduce($diff, function ($acc, $parent) use ($space) {
        $children = $parent['children'];
        
        if (!is_null($parent['newChildren'])) {
            $children = $parent['newChildren'];
        }
        
        if ($parent['nested']) {
            $children = diffToJson($children);
        }

        $acc[$parent['key']] = $children;

        return $acc;
    }, []);

    return $structuredData;
}
