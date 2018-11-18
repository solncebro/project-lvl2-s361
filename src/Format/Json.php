<?php

namespace Gendiff\Differ\Format;

function diffToJson($diff, $space = '')
{
    $structuredData = array_reduce($diff, function ($acc, $parent) use ($space) {
        $type = $parent['type'];
        $children = $parent['children'];
        $key = $parent['key'];
        
        if ($parent['nested']) {
            $acc[$key] = diffToJson($children);
        } else {
            if ($type === 'changed' && !is_null($parent['newChildren'])) {
                $children = $parent['newChildren'];
            }
            $acc[$key] = $children;
        }
        
        return $acc;
    }, []);

    return $structuredData;
}
