<?php

defined('WPINC') || die;

spl_autoload_register(function ($className) {
    $namespaces = [
        'GeminiLabs\\SiteReviews\\Addon\\Images\\' => __DIR__.'/plugin/',
        'GeminiLabs\\SiteReviews\\Addon\\Images\\Tests\\' => __DIR__.'/tests/',
    ];
    foreach ($namespaces as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (0 !== strncmp($prefix, $className, $len)) {
            continue;
        }
        $file = $baseDir.str_replace('\\', '/', substr($className, $len)).'.php';
        if (!file_exists($file)) {
            continue;
        }
        require $file;
        break;
    }
});
