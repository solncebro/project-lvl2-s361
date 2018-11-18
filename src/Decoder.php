<?php

namespace Gendiff\Decoder;

use Symfony\Component\Yaml\Yaml;

function decode($content, $contentType)
{
    if ($contentType === 'json') {
        return json_decode($content, true);
    } elseif ($contentType === 'yml') {
        $yaml = Yaml::parse($content);
        return $yaml;
    } else {
        throw new \Exception("Unsupported content type {$contentType}. Terminating..." . PHP_EOL);
    }
}
