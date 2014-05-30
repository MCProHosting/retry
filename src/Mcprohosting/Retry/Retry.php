<?php


namespace Mcprohosting\Retry;

class Retry
{
    /**
     * Creates a new Retry instance via a static call.
     *
     * @param string $method
     * @param string $arguments
     * @return self
     */
    public static function __callStatic($method, $arguments)
    {
        return call_user_func_array(array(new Runner, $method), $arguments);
    }
} 
