#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MrRio\ShellWrap as sh;

echo sh::ls();
