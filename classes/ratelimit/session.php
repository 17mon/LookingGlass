<?php

namespace ratelimit;

class session implements \RateLimitInterface
{
    private $key;

    public function __construct(array $option)
    {
        $sid = md5('looking-' . json_encode($option));

        session_id($sid); // session_id 不依赖cookie,主要由客户端的ua&ip生成
        session_start();

        $this->key = date('YmdHi');

        if (empty($_SESSION['rateLimit'][$this->key]))
        {
            $_SESSION['rateLimit'][$this->key] = 1;
        }
    }

    public function allow($limit)
    {
        if ($_SESSION['rateLimit'][$this->key] >= $limit)
        {
            return FALSE;
        }

        $_SESSION['rateLimit'][$this->key] += 1;

        return TRUE;
    }

    public function __destruct()
    {
        $int = intval($this->key);
        foreach ($_SESSION['rateLimit'] AS $key => $value)
        {
            if ($key < $int)
            {
                unset($_SESSION['rateLimit'][$key]);
            }
        }
    }
}