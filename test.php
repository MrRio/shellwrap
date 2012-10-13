<?php

// Require the class
require 'ShellWrap.php';

$sh = new \MrRio\ShellWrap();

echo '<pre>';

// This does no magic path finding
echo $sh('ls', '-l');

echo $sh->ls('-l');

echo $sh->curl('http://snapshotmedia.co.uk', array(
	'o' => 'page.html',
	'silent' => true
), 'summat', array('and', 'some'));

echo $sh->git('pull', array('hard' => true), 'HEAD');


//$ret = $sh->ls('-l');

exit();
$sh->git()->pull('origin master');

$sh->curl('http://snapshotmedia.co.uk', array(
	'o' => 'page.html'
));

$ls = explode("\n", $sh->ls('*'));

echo $sh->sort($sh->du($sh->glob("*"), "-sb"), "-rn");

// Run a raw command
echo $sh('ls -l');


?>