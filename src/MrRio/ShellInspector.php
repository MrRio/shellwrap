<?php

namespace MrRio;

class ShellInspector
{
    /**
     * Override the public dump function, so we can display plain
     * text from ShellWrap instead of an object dump.
     **/
    public function _dump($value)
    {
        if (is_object($value) && get_class($value) == 'MrRio\ShellWrap') {
            echo strval($value);
            // Return zero, as this is the exit code
            // Otherwise an exception was thrown
            return 0;
        }

        return parent::_dump($value);
    }
}
