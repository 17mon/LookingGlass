<?php

namespace command;

class host implements \CommandInterface
{
    public static function execute($args, Callable $callback = NULL)
    {
        $str = shell_exec($args['cmd'] . ' ' . $args['ip']);
        if (is_null($callback))
        {
            return $str;
        }
        else
        {
            return $callback($str);
        }
    }
}