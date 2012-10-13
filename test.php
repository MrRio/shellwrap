<?php

// Require the class
require 'ShellWrap.php';

use \MrRio\ShellWrap as sh;

echo '<pre>';
echo sh::ls()->thingy();


echo '<pre>';

// This does no magic path finding

$sh = new sh();

//echo $sh('ls', '-l *');


exit();

echo sh::ls('-l');

echo sh::curl('http://snapshotmedia.co.uk', array(
	'o' => 'page.html',
	'silent' => true
), 'summat', array('and', 'some'));

echo sh::git('pull', array('hard' => true), 'HEAD');



//$ret = $sh->ls('-l');

sh::git()->pull('origin master');

sh::curl('http://snapshotmedia.co.uk', array(
	'o' => 'page.html'
));

$ls = explode("\n", $sh::ls('*'));

echo sh::sort(sh::du(sh::glob("*"), "-sb"), "-rn");

// Run a raw command
echo $sh('ls -l');


?>