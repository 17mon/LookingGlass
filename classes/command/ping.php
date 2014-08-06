<?php

namespace command;

class ping implements \CommandInterface
{
    public static function execute($args, Callable $callback = NULL)
    {
        $ret = [];
        self::pingStart($args['cmd'], $args['ip'], function($line) use(&$ret, $callback){
            if (is_null($callback))
            {
                $ret[] = $line;
            }
            else
            {
                $callback($line);
            }
        });

        return $ret;
    }

    protected static function pingStart($cmd, $ip, $callback)
    {
        $handle = popen("{$cmd} -n -c 4 {$ip} 2>&1", 'r');

        while (feof($handle) === FALSE)
        {
            $line = fgets($handle);

            $callback($line);
        }

        pclose($handle);
    }
}