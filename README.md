ShellWrap
==================

What is it?
------------------

It's a beautiful way to use powerful Linux/Unix tools in PHP. Easily and logically pipe commands together,
capture errors as PHP Exceptions and use a simple yet powerful syntax. Works with any command line tool automagically.

Features 
------------------

* Flexible and sexy syntax.
* Exceptions are thrown if the executable returns an error.
* Paths to binaries are automatically resolved.
* All arguments are properly escaped.


Examples
------------------

```php
<?php 
require_once 'vendor/autoload.php';
use MrRio\ShellWrap as sh;

// List all files in current dir
echo sh::ls();

// Checkout a branch in git
sh::git('checkout', 'master');

// You can also pipe the output of one command, into another
// This downloads example.com through cURL, follows location, then pipes through grep to 
// filter for 'html'
echo sh::grep('html', sh::curl('http://example.com', array(
	'location' => true
)));

// Touch a file to create it
sh::touch('file.html');

// Remove file
sh::rm('file.html');

// Remove file again (this fails, and throws an exception because the file doesn't exist)

try {
	sh::rm('file.html');
} catch (ShellWrapException $e) {
	echo 'Caught failing sh::rm() call';
}


// This throws an exception, as 'invalidoption' is not a valid argument
try {
	echo sh::ls(array('invalidoption' => true));
} catch (ShellWrapException $e) {
	echo 'Caught failing sh::ls() call';
}

// Commands can be written multiple ways
sh::git('reset', array('hard' => true), 'HEAD');
sh::git('reset', '--hard', 'HEAD');
sh::git(array('reset', '--hard', 'HEAD'));

// Arguments passed in are automatically escaped, this expands to
// date --date '2012-10-10 10:00:00'
echo sh::date(array(
	'date' => '2012-10-10 10:00:00'
));

// If arg keys are one letter, is assumes one dash prefixing it
// date -d '2012-10-10 10:00:00'
echo sh::date(array(
	'd' => '2012-10-10 10:00:00'
));


?>
```


Interactive Shell
-----------------

ShellWrap also ships with an interactive shell mode. You can access this by typing:

```
./bin/shellwrap
```

Warning
--------

Don't use any user inputted data with these commands. Even with very paranoid filtering, you 
can't know all the potential pitfalls of each command you're using. Use your noggin. 

Acknowledgements
--------------------

Inspired by the Python project [sh by Andrew Moffat](http://pypi.python.org/pypi/sh)
