<?php

declare(strict_types=1);

namespace MrRio;

use Psy\Output\ShellOutput;

class ShellInspector
{
    /**
     * Custom presenter for ShellWrap objects in PsySH
     *
     * @param mixed $value The value to present
     * @param ShellOutput $output The output interface
     * @return bool Whether the value was handled
     */
    public static function present(mixed $value, ShellOutput $output): bool
    {
        if ($value instanceof ShellWrap) {
            $output->writeln(strval($value));
            return true;
        }

        return false;
    }
}
