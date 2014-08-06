<?php

interface CommandInterface
{
    public static function execute($args, Callable $callback = NULL);
}