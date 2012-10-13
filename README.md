ShellWrap
==================

What is it?
------------------

It's a way to use powerful underlying Unix tools while feeling slightly less dirty.

Exceptions are thrown if the executable has an error.

Examples
------------------

// Get a list of files in the current directory, and list them biggest first

echo $sh->sort($sh->du($sh->glob("*"), "-sb"), "-rn");


This is such a bad idea
------------------------

It's decidedly better than magic quotes. 