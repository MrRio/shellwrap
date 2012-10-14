ShellWrap 0.1
==================

What is it?
------------------

It's a beautiful way to use powerful Linux/Unix tools in PHP. Easily and logically pipe commands together,
capture errors as PHP Exceptions and use a simple yet powerful syntax. 

Features 
------------------

* Exceptions are thrown if the executable returns an error.
* Paths to binaries are automatically resolved
* All arguments are properly escaped

Future Ideas
-------------------

* Easy logging of command ran and their outputs
* sudo support
* Advanced piping options

Examples
------------------

```php
<?php 
require 'ShellWrap.php';
use \MrRio\ShellWrap as sh;

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

?>
```

Acknowledgements
--------------------

Inspired by the Python project [sh by Andrew Moffat](http://pypi.python.org/pypi/sh)