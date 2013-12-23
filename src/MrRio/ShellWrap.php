<?php

namespace MrRio;

use MrRio\ShellWrapException;

class ShellWrap
{
    /**
     * If set to true, will output standard output, if set to a function, will send through function
     * @var boolean
     */
    static public $displayStdout = false;

    /**
     * If set to true, will output stderr to standard output, if set to a function, will send through function
     * @var boolean
     */
    static public $displayStderr = false;

    /**
     * If set to true, will throw an exception on error
     * @var boolean
     */
    static public $exceptionOnError = true;


    private static $output = array();
    private static $prepend = array();
    private static $stdin = null;

    public static $exec_string;

    public function __construct($prepend = null)
    {
        self::$prepend = $prepend;
    }

    public function __toString()
    {
        return self::$output;
    }

    /**
     * Check if array is associative, thanks to
     * http://stackoverflow.com/questions/173400/#4254008
     **/
    private static function __isAssociative($array)
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }

    private static function __run($arguments)
    {
        // Unwind the args, figure out which ones were passed in as an array
        self::$stdin = null;

        foreach ($arguments as $arg_key => $argument) {
            // If it's being passed in as an object, then pipe into stdin
            if (is_object($argument)) {

                // If it's a anonymous function, then push stdout into it
                if (get_class($argument) == 'Closure') {
                    self::$displayStdout = $argument;
                    unset($arguments[$arg_key]);
                } else {
                    self::$stdin = strval($argument);
                    unset($arguments[$arg_key]);
                }

            } elseif (is_array($argument)) {
                if (self::__isAssociative($argument)) {

                    // Ok, so we're passing in arguments

                    $output = '';

                    foreach ($argument as $key => $val) {

                        if ($output != '') {
                            $output .= ' ';
                        }

                        // If you pass 'false', it'll ignore the arg altogether
                        if ($val !== false) {
                            // Figure out if it's a long or short commandline arg
                            if (strlen($key) == 1) {
                                $output .= '-';
                            } else {
                                $output .= '--';
                            }
                            $output .= $key;

                            // If you just pass in 'true', it'll just add the arg
                            if ($val !== true) {

                                $output .= ' ' . escapeshellarg($val);
                            }

                        }
                    }

                    $arguments[$arg_key] = $output;
                } else {

                    // We're passing in an array, but it's not --key=val style

                    $arguments[$arg_key] = implode(' ', $argument);
                }
            }
        }

        $shell = implode(' ', $arguments);

        $output = array();
        $return_var = null;

        // Set exec_string for testing purposes
        self::$exec_string = $shell;

        // Prepend the path

        $parts = explode(' ', $shell);
        $parts[0] = exec('which ' . $parts[0]);

        if ($parts[0] != '') {
            $shell = implode(' ', $parts);
        }

        $descriptor_spec = array(
            0 => array('pipe', 'r'), // Stdout
            1 => array('pipe', 'w'), // Stdin
            2 => array('pipe', 'w') // Stderr
        );

        $process = proc_open($shell, $descriptor_spec, $pipes);

        if (is_resource($process)) {

            fwrite($pipes[0], self::$stdin);
            fclose($pipes[0]);

            $output = '';

            while(! feof($pipes[1])) {
                $stdout = fgets($pipes[1], 1024);
                if (strlen($stdout) == 0) break;

                if (self::$displayStdout === true) {
                    echo $stdout;
                }

                $outputFunction = self::$displayStdout;
                if (is_callable($outputFunction)) {
                    $outputFunction($stdout);
                }

                $output .= $stdout;
            }

            $error_output = trim(stream_get_contents($pipes[2]));

            if (self::$displayStderr) {
                echo $error_output;
            }
            self::$output = $output;

            fclose($pipes[1]);
            fclose($pipes[2]);

            $return_value = proc_close($process);

            if ($return_value != 0) {

                if (self::$exceptionOnError) {
                    throw new ShellWrapException($error_output, $return_value);
                } else {
                    return $error_output;
                }
            }

        } else {
            throw new ShellWrapException('Process failed to spawn');
        }

        //exec($shell, $output, $return_var);

    }

    // Raw arguments

    public function __invoke()
    {
        $arguments = func_get_args();
        $this->__run($arguments);

        return $this;
    }

    public function __call($name, $arguments)
    {
        array_unshift($arguments, $name);
        $this->__run($arguments);

        return $this;
    }

    public static function __callStatic($name, $arguments)
    {
        array_unshift($arguments, $name);
        if (isset(self::$prepend)) {
            $arguments = array_merge(self::$prepend, $arguments);
        }

        self::__run($arguments);

        return new self();
    }

}
