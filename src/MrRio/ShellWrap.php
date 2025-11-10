<?php

declare(strict_types=1);

namespace MrRio;

use Closure;

class ShellWrap
{
    /**
     * If set to true, will output standard output, if set to a function, will send through function
     * @var bool|callable
     */
    public static mixed $displayStdout = false;

    /**
     * If set to true, will output stderr to standard output, if set to a function, will send through function
     * @var bool|callable
     */
    public static mixed $displayStderr = false;

    /**
     * If set to true, will throw an exception on error
     */
    public static bool $exceptionOnError = true;

    private static string $output = '';

    private static ?array $prepend = null;

    private static ?string $stdin = null;

    public static ?string $exec_string = null;

    public function __construct(?array $prepend = null)
    {
        self::$prepend = $prepend;
    }

    public function __toString(): string
    {
        return self::$output;
    }

    /**
     * Check if array is associative
     */
    private static function isAssociative(array $array): bool
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }

    private static function run(array $arguments): void
    {
        // Unwind the args, figure out which ones were passed in as an array
        self::$stdin = null;
        $closureOut = false;

        foreach ($arguments as $arg_key => $argument) {
            // If it's being passed in as an object, then pipe into stdin
            if (is_object($argument)) {
                // If it's a anonymous function, then push stdout into it
                if ($argument instanceof Closure) {
                    $closureOut = $argument;
                    unset($arguments[$arg_key]);
                } else {
                    self::$stdin = strval($argument);
                    unset($arguments[$arg_key]);
                }
            } elseif (is_array($argument)) {
                if (self::isAssociative($argument)) {
                    // Ok, so we're passing in arguments
                    $output = '';

                    foreach ($argument as $key => $val) {
                        if ($output !== '') {
                            $output .= ' ';
                        }

                        // If you pass 'false', it'll ignore the arg altogether
                        if ($val !== false) {
                            // Figure out if it's a long or short commandline arg
                            if (strlen((string) $key) === 1) {
                                $output .= '-';
                            } else {
                                $output .= '--';
                            }
                            $output .= $key;

                            // If you just pass in 'true', it'll just add the arg
                            if ($val !== true) {
                                $output .= ' ' . escapeshellarg((string) $val);
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

        // Set exec_string for testing purposes
        self::$exec_string = $shell;

        // Prepend the path
        $parts = explode(' ', $shell);
        $which = exec('which ' . $parts[0]);

        if ($which !== '') {
            $parts[0] = $which;
            $shell = implode(' ', $parts);
        }

        $descriptorSpec = [
            0 => ['pipe', 'r'], // Stdin
            1 => ['pipe', 'w'], // Stdout
            2 => ['pipe', 'w']  // Stderr
        ];

        $process = proc_open($shell, $descriptorSpec, $pipes);

        if (!is_resource($process)) {
            throw new ShellWrapException('Process failed to spawn');
        }

        fwrite($pipes[0], self::$stdin ?? '');
        fclose($pipes[0]);

        $output = '';

        while (!feof($pipes[1])) {
            $stdout = fgets($pipes[1], 1024);
            if ($stdout === false || strlen($stdout) === 0) {
                break;
            }

            if (self::$displayStdout === true) {
                echo $stdout;
            }

            if (is_callable(self::$displayStdout)) {
                (self::$displayStdout)($stdout);
            }

            if (is_callable($closureOut)) {
                $closureOut($stdout);
            }

            $output .= $stdout;
        }

        $errorOutput = trim(stream_get_contents($pipes[2]));

        if (self::$displayStderr) {
            echo $errorOutput;
        }

        self::$output = $output;

        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnValue = proc_close($process);

        if ($returnValue !== 0) {
            if (self::$exceptionOnError) {
                throw new ShellWrapException($errorOutput, $returnValue);
            }
        }
    }

    public function __invoke(mixed ...$arguments): self
    {
        self::run($arguments);
        return $this;
    }

    public function __call(string $name, array $arguments): self
    {
        array_unshift($arguments, $name);
        self::run($arguments);
        return $this;
    }

    public static function __callStatic(string $name, array $arguments): self
    {
        array_unshift($arguments, $name);

        if (isset(self::$prepend)) {
            $arguments = array_merge(self::$prepend, $arguments);
        }

        self::run($arguments);

        return new self();
    }
}
