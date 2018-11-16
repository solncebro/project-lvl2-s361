<?php

namespace Gendiff\Decoder;

use Symfony\Component\Yaml\Yaml;

function decode($textFile, $extensionFile)
{
    if ($extensionFile === 'json') {
        return json_decode($textFile, true);
    } elseif ($extensionFile === 'yml') {
        $yaml = Yaml::parse($textFile);
        return $yaml;
    } else {
        return null;
    }
}
