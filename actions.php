<?php

class actions
{
    public static function execute()
    {
        Header('X-Accel-Buffering: no');// nginx-1.5.6 及其以上版本支持

        $config = App::getConfig();

        $rateLimit = $config['site']['rateLimit'];

        if (isset($rateLimit['enable']) && $rateLimit['enable'])
        {
            $option = [
                'ip' => $_SERVER['REMOTE_ADDR'],
                'ua' => $_SERVER['HTTP_USER_AGENT']
            ];
            $rateLimitClass = 'ratelimit\\' . $rateLimit['provider']['class'];

            if (empty($rateLimit['minute']))
            {
                $rateLimit['minute'] = 100; //每分钟限制100次
            }

            /**
             * @var RateLimitInterface $rateLimitInterface
             */
            $rateLimitInterface = new $rateLimitClass($option);
            if ($rateLimitInterface->allow($rateLimit['minute']) === FALSE)
            {
                exit('<script>parent.alert("操作太频繁了")</script>');
            }
        }

        $commands = $config['site']['commands'];

        $param = $_GET + $_POST;
        $host = isset($param['host']) ? $param['host'] : '';
        $cmd  = isset($param['cmd']) ? $param['cmd'] : '';

        $host = gethostbyname($host);

        if (isset($commands[$cmd]))
        {
            call_user_func(array(__CLASS__, $cmd), $host, $commands[$cmd]);

            echo '<script>parent.req_complete()</script>';
        }
    }

    protected static function host($host, $cmd)
    {
        $args = [
            'cmd' => $cmd,
            'ip'  => $host,
        ];

        echo '<script>parent.update_start()</script>';
        ob_flush();
        flush();

        $response = command\host::execute($args);
        $response = trim($response);
        echo "<script>parent.update_view('{$response}')</script>";
        ob_flush();
        flush();
    }

    protected static function ping($host, $cmd)
    {
        $args = [
            'cmd' => $cmd,
            'ip'  => $host,
        ];

        echo '<script>parent.update_start()</script>';
        ob_flush();
        flush();

        command\ping::execute($args, function($line){
            echo '<script>parent.update_view("' . trim($line) . '")</script>';
            ob_flush();
            flush();
        });
    }

    protected static function traceroute($host, $cmd)
    {
        $args = [
            'cmd' => $cmd,
            'ip'  => $host,
        ];

        echo '<script>parent.update_start()</script>';
        ob_flush();
        flush();

        command\traceroute::execute($args, function($line, $json) {

            echo '<script>parent.update_view("' . trim($line) . '", ' . json_encode($json) . ')</script>';
            ob_flush();
            flush();
        });
    }

    protected static function ping6($host, $cmd)
    {
        $args = [
            'cmd' => $cmd,
            'ip'  => $host,
        ];

        echo '<script>parent.update_start()</script>';
        ob_flush();
        flush();

        command\ping::execute($args, function($line){
            echo '<script>parent.update_view("' . trim($line) . '")</script>';
            ob_flush();
            flush();
        });
    }
}