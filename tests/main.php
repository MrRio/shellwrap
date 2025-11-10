<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MrRio\ShellWrap;
use MrRio\ShellWrapException;
use PHPUnit\Framework\TestCase;

class ShellWrapTest extends TestCase
{
    public function testLsAgainstGlob(): void
    {
        $output = ShellWrap::ls();

        // @TODO: Need to figure out a nicer way of doing this
        // Needs to be an object for the piping, but then gets converted to a string
        // when __toString is fired.
        $output = trim(strval($output));

        $output = explode("\n", $output);
        $glob_output = glob('*');

        $this->assertEquals($glob_output, $output);
    }

    // public function testDate(): void
    // {
    //     // needed in non-english environments
    //     putenv('LC_ALL=en_US.utf8');
    //
    //     // Pass in a date
    //     $output = ShellWrap::date([
    //         'utc' => true,
    //         'date' => '2012-10-10 10:00:00'
    //     ]);
    //     $output = trim(strval($output));
    //
    //     // This is the default output
    //     // @TODO: Check to make sure it's always UTC
    //     $date = 'Wed Oct 10 10:00:00 UTC 2012';
    //
    //     $this->assertEquals($date, $output);
    //
    //     $date_exec = "date --utc --date '2012-10-10 10:00:00'";
    //
    //     $this->assertEquals($date_exec, ShellWrap::$exec_string);
    // }

    public function testCurlCommand(): void
    {
        ShellWrap::curl('http://example.com/', [
            'output' => 'page.html',
            'silent' => false,
            'location' => true
        ]);

        // @TODO: Make this less horrible, save files in a temp dir
        $this->assertFileExists('page.html');

        ShellWrap::rm('page.html');

        $this->assertFileDoesNotExist('page.html');
    }

    /**
     * Using 'echo' to test long and short arguments
     */
    public function testLongAndShortArgs(): void
    {
        // Call ShellWrap directly as echo is reserved
        $sh = new ShellWrap();
        $result = $sh('echo', [
            'test' => true,
            'key' => 'value',
            'other' => 'value with spaces',
            's' => true,
            'should_not_be_echod' => false
        ]);

        $expected = "echo --test --key 'value' --other 'value with spaces' -s";
        $this->assertEquals($expected, trim(ShellWrap::$exec_string ?? ''));

        // Expect the echo result to remove the shell escaping
        $expected = "--test --key value --other value with spaces -s";
        $this->assertEquals($expected, trim((string) $result));
    }

    public function testRaiseException(): void
    {
        $this->expectException(ShellWrapException::class);

        $sh = new ShellWrap();
        try {
            $sh(__DIR__ . '/fixtures/fail.sh', 1);
        } catch (ShellWrapException $e) {
            $this->assertEquals("[1]: foo bar\n", (string) $e);
            $this->assertEquals(1, $e->getCode());
            throw $e;
        }
    }

    public function testNotRaiseException(): void
    {
        $sh = new ShellWrap();
        $result = $sh(__DIR__ . '/fixtures/fail.sh', 0);
        $this->assertInstanceOf(ShellWrap::class, $result);
    }
}
