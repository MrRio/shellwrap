<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MrRio\ShellWrap as sh;

class ShellWrapTest extends \PHPUnit_Framework_TestCase
{
    public function testLsAgainstGlob()
    {
        $output = sh::ls();

        // @TODO: Need to figure out a nicer way of doing this
        // Needs to be an object for the piping, but then gets converted to a string
        // when __toString is fired.
        $output = trim(strval($output));

        $output = explode("\n", $output);
        $glob_output = glob('*');

        $this->assertEquals($glob_output, $output);
    }

    // public function testDate()
    // {
    //     // needed in non-english environments
    //     putenv('LC_ALL=en_US.utf8');

    //     // Pass in a date
    //     $output = sh::date(array(
    //         'utc' => true,
    //         'date' => '2012-10-10 10:00:00'
    //     ));
    //     $output = trim(strval($output));

    //     // This is the default output
    //     // @TODO: Check to make sure it's always UTC
    //     $date = 'Wed Oct 10 10:00:00 UTC 2012';

    //     $this->assertEquals($date, $output);

    //     $date_exec = "date --utc --date '2012-10-10 10:00:00'";

    //     $this->assertEquals($date_exec, sh::$exec_string);

    // }

    public function testCurlCommand()
    {
        sh::curl('http://example.com/', array(
            'output' => 'page.html',
            'silent' => false,
            'location' => true
        ));

        // @TODO: Make this less horrible, save files in a temp dir

        $this->assertFileExists('page.html');

        sh::rm('page.html');

        $this->assertFileNotExists('page.html');
    }

    /**
     * Using 'echo' to test long and short arguments
     **/
    public function testLongAndShortArgs()
    {
        // Call ShellWrap directly as echo is reserved
        $sh = new sh();
        $result = $sh('echo', array(
            'test' => true,
            'key' => 'value',
            'other' => 'value with spaces',
            's' => true,
            'should_not_be_echod' => false
        ));

        $expected = "echo --test --key 'value' --other 'value with spaces' -s";
        $this->assertEquals($expected, trim(sh::$exec_string));

        // Expect the echo result to remove the shell escaping
        $expected = "--test --key value --other value with spaces -s";
        $this->assertEquals($expected, trim($result));
    }

}
