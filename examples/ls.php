#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use MrRio\ShellWrap as sh;

echo sh::ls();
