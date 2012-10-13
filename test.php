<?php

// Require the class
require 'ShellWrap.php';

$sh = new \MrRio\ShellWrap();

$sh->git()->pull('origin master');

$sh->curl('http://snapshotmedia.co.uk', array(
	'o' => 'page.html'
));

$ls = explode("\n", $sh->ls('*'));
print_r($ls);

echo $sh->sort($sh->du($sh->glob("*"), "-sb"), "-rn");


// Run a raw command
echo $sh('ls -l');


?>