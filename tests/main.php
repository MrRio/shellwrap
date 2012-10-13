<?php

require_once 'PHPUnit/Autoload.php';
require_once '../ShellWrap.php';

use \MrRio\ShellWrap as sh;

class ShellWrapTest extends PHPUnit_Framework_TestCase {

	public function testLsAgainstGlob() {
		$output = sh::ls();

		// @TODO: Need to figure out a nicer way of doing this
		// Needs to be an object for the piping, but then gets converted to a string
		// when __toString is fired.
		$output = trim(strval($output));

		$output = explode("\n", $output);
		$glob_output = glob('*');

		$this->assertEquals($glob_output, $output);
		
	}


	public function testDate() {

		// Pass in a date
		$output = sh::date(array(
			'date' => '2012-10-10 10:00:00'
		));
		$output = trim(strval($output));

		// This is the default output 
		// @TODO: Check to make sure it's always UTC
		$date = 'Wed Oct 10 10:00:00 UTC 2012';

		$this->assertEquals($date, $output);

		$date_exec = "date --date '2012-10-10 10:00:00'";

		$this->assertEquals($date_exec, sh::$exec_string);

	}

	public function testCurlCommand() {

		echo sh::curl('http://example.com/', array(
			'output' => 'page.html',
			'silent' => false,
			'location' => true
		));
		
		// @TODO: Make this less horrible, save files in a temp dir

		$this->assertFileExists('page.html');

		sh::rm('page.html');

		$this->assertFileNotExists('page.html');
	}

}