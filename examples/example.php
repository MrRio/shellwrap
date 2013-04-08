#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use MrRio\ShellWrap as sh;

// List all files in current dir
echo sh::ls();

// Touch a file to create it
sh::touch('file.html');

// Remove file
sh::rm('file.html');

// Remove file again (this fails, and throws an exception because the file doesn't exist)

try {
    sh::rm('file.html');
} catch (Exception $e) {
    echo 'Caught failing sh::rm() call';
}

// Checkout a branch in git
sh::git('checkout', 'master');

// You can also pipe the output of one command, into another
// This downloads example.com through cURL, follows location, then pipes through grep to
// filter for 'html'
echo sh::grep('html', sh::curl('http://example.com', array(
    'location' => true
)));

// This throws an exception, as 'invalidoption' is not a valid argument
try {
    echo sh::ls(array('invalidoption' => true));
} catch (Exception $e) {
    echo 'Caught failing sh::ls() call';
}
