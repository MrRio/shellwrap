#!/usr/bin/env php
<?php
require_once 'vendor/autoload.php';

use Guumaster\ShellWrapper as sh;

$sh = new sh();

try {
    $curl = $sh->curl('https://raw.github.com/guumaster/sh/master/README.md' );
    $grep = $sh->grep('ASDFInstallation', $curl);
    echo "match\n";
    echo $grep;
} catch (Exception $e) {
    #echo $e->getMessage();
    #echo $e->getCode();
    echo "no match\n";
}
