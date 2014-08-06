<?php

/**
 * @bootstrap.css http://cdn.staticfile.org/twitter-bootstrap/3.1.1/css/bootstrap.min.css http://cdn.staticfile.org/twitter-bootstrap/3.1.1/css/bootstrap-theme.min.css
 * @bootstrap.js http://cdn.staticfile.org/twitter-bootstrap/3.1.1/js/bootstrap.min.js
 */

class Application
{
    public static function run()
    {
        self::registerAutoload();

        self::initRequest();
        self::dispatch();
    }

    private static function initRequest()
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        $url = parse_url($request_uri);
        if (isset($url['path']))
        {
            $_SERVER['SCRIPT_NAME'] = $url['path'];
        }
        if (isset($url['query']))
        {
            $_SERVER['QUERY_STRING'] = $url['query'];

            parse_str($url['query'], $_GET);
        }
    }

    public static function getConfig()
    {
        static $config;

        if (is_null($config))
        {
            $config = include __DIR__ . '/config.php';
        }

        return $config;
    }

    public static function dispatch()
    {
        $uri = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/';
        $act = trim($uri, '/');

        require_once __DIR__ . '/actions.php';

        if ($act == 'execute')
        {
            actions::execute();
        }
        else
        {
            ob_start();
            $conf = self::getConfig();
            extract($conf);
            include __DIR__ . '/template.phtml';
            echo ob_get_clean();
        }
    }

    public static function h($str)
    {
        echo htmlspecialchars($str);
    }

    public static function registerAutoload()
    {
        spl_autoload_register(function($class){

            if (isset(self::$classMap[$class]))
            {
                return require __DIR__ . self::$classMap[$class];
            }

            $file = __DIR__ . '/classes/' . str_replace('\\', '/', $class) . '.php';
            if (is_file($file) === FALSE)
            {
                return FALSE;
            }

            return require $file;
        });
    }

    protected static $classMap = array(
        'IP' => '/vendor/IP.class.php'
    );
}

class App extends Application
{

}

App::run();