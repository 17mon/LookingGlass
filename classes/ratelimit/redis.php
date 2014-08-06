<?php

namespace ratelimit;

class redis implements RateLimitInterface
{
    private $key;

    public function __construct(array $option)
    {
        $this->key = md5('looking-' . isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
    }

    public function allow($limit)
    {

    }
}