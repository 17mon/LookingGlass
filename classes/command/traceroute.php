<?php

namespace command;

class traceroute implements \CommandInterface
{
    public static function execute($args, Callable $callback = NULL)
    {
        $ret = [];
        self::tracert($args['cmd'], $args['ip'], function($line) use (&$ret, $callback){
            if (is_null($callback))
            {
                $ret[] = $line;
            }
            else
            {
                $callback($line, self::parse($line));
            }
        });

        return $ret;
    }

    private static function tracert($cmd, $ip, $callback)
    {
        $hops = 30;
        $as = 0;
        if ($as === 0)
        {
            $handle = popen("{$cmd} -n -m {$hops} {$ip} 2>&1", 'r');
        }
        else
        {
            $handle = popen("{$cmd} -n -A -m {$hops} {$ip} 2>&1", 'r');
        }

        while (feof($handle) === FALSE)
        {
            $line = fgets($handle);

            $int = (int)$line;
            if ($int > 0 && preg_match('/(\d+\.\d+\.\d+\.\d+)/is', trim($line), $match) > 0)
            {
                $route_ip = $match[1];
                $host = host::execute(['ip' => $route_ip, 'cmd' => 'host']);
                $host1 = '';
                foreach (explode("\n", $host) AS $value)
                {
                    if (strpos($value, 'domain name pointer') !== FALSE)
                    {
                        $host1 = $value;
                        break;
                    }
                }
                if ($host1 != '' && preg_match('/pointer\s(.+)\.$/is', trim($host1), $match) > 0)
                {
                    $line = str_replace($route_ip, "{$match[1]} ($route_ip)", $line);
                }
                else
                {
                    $line = str_replace($route_ip, "{$route_ip} ($route_ip)", $line);
                }
            }

            $callback(trim($line));
        }

        pclose($handle);
    }

    private static function parse($string)
    {
        $string = trim($string);
        $int = intval($string);

        if ($int == 0)
        {
            $tmp = preg_match('/^traceroute.*?\((.*?)\)/', $string, $match);
            if ($tmp > 0 && isset($match[1]))
            {
                $ip = $match[1];
                echo "<script>parent.resp_ip('" . $ip . ")</script>";
                ob_flush();
                flush();

                return [];
            }
        }

        $route = [];
        $string = str_replace('[*]', '[_]', $string);
        $string = str_replace('!X', '', $string);
        $string = str_replace('!N ', '', $string);
        $string = str_replace('!H ', '', $string);
        $array = preg_split('/(\s+ms\s*)|(\*)/', preg_replace('/^\d+\s/', '', $string), -1);
        $array = array_map('trim', $array);
        foreach (array_slice($array, 0, 3) AS $value)
        {
            $match = [];
            if (preg_match('/^(?:(.+?)\s+)?\((\d+\.\d+\.\d+\.\d+)\)\s+(?:\[(.*?)\]\s+)?([\.|\d]+?)$/', trim($value), $match) > 0)
            {
                $last = $match;
            }
            elseif (isset($last))
            {
                $last[4] = $value;
                $match   = $last;
            }

            array_shift($match);
            if (count($match) == 4)
            {
                $match = array_combine(['host', 'ip', 'as', 'time'], $match);
                if (empty($match['host']))
                {
                    $match['host'] = '*';
                }
                $route[] = array_map(function($v){return trim($v);}, $match);
            }
            else
            {
                $route[] = [
                    'host' => '*',
                    'ip'   => '*',
                    'as'   => '*',
                    'time' => '*',
                ];
            }
        }

        foreach ($route AS $k => $v)
        {
            if ($v['as'] === '_')
            {
                $route[$k]['as'] = '*';
            }
            else
            {
                if (is_array($route[$k]['as']))
                {
                    //
                }
                elseif (stripos($route[$k]['as'], '/') !== FALSE)
                {
                    $route[$k]['as'] = explode('/', strtr($route[$k]['as'], ['>' => '*', '<' => '*']));
                }

                $route[$k]['as'] = implode(' / ', (array)$route[$k]['as']);
            }

            if (empty($v['time']) === TRUE)
            {
                $route[$k] = array(
                    'host' => '*',
                    'ip'   => '*',
                    'as'   => '*',
                    'time' => '*',
                );
            }
            else
            {
                //
            }
        }

        foreach ($route AS $k => $v)
        {
            if ($v['time'] != '*' && empty($v['time']) === FALSE)
            {
                $v['time'] = round($v['time'], 1);

                $route[$k] = $v;
            }

            if ($v['ip'] == '*')
            {
                $v['as'] = '*';
                $v['area'] = '';
            }
            else
            {
                $v['area'] = implode(' ', \IP::find($v['ip']));
            }

            $route[$k] = $v;
        }
/*
        if (count($route) == 3 && $route[0]['host'] === $route[1]['host'] && $route[1]['host'] === $route[2]['host'])
        {
            if ($route[0]['host'] !== '*')
            {
                $route[0]['time'] = $route[0]['time'] . " / " . $route[1]['time']. " / " . $route[2]['time'];
            }
            unset($route[1], $route[2]);
        }
*/
        if (count($route) == 3)
        {
            $_hosts = [];
            for ($i = 0; $i < count($route); $i++)
            {
                $_hosts[] = $route[$i]['host'];
            }
            if (count(array_unique($_hosts)) == 1)
            {
                if ($route[0]['host'] !== '*')
                {
                    $route[0]['time'] = $route[0]['time'] . " / " . $route[1]['time']. " / " . $route[2]['time'];
                }

                unset($route[1], $route[2]);
            }
        }

        return $route;
    }
}