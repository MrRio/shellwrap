#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MrRio\ShellWrap as sh;

$sh = new sh();

try {
    $curl = $sh->curl('https://raw.github.com/guumaster/sh/master/README.md');
    $grep = $sh->grep('ASDFInstallation', $curl);
    echo "match\n";
    echo $grep;
} catch (Exception $e) {
    // echo $e->getMessage();
    // echo $e->getCode();
    echo "no match\n";
}
