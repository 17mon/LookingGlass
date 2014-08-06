<?php

interface RateLimitInterface
{
    public function allow($limit);
}