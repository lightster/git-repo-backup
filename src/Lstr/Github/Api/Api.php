<?php

namespace Lstr\Github\Api;

class Api
{
    private $config;
    private $caller;



    public function __construct(array $config)
    {
        $this->config = $config;
        $this->caller = new Caller($config);
    }



    public function __call($method, array $args)
    {
        $method     = ucfirst($method);
        $class      = 'Lstr\\Github\\Api\\MethodCall\\' . $method;

        $method_args = array();
        if (isset($args[0])) {
            $method_args = $args[0];
        }

        $api_method = new $class($this->caller, $method_args);
        return $api_method->run();
    }
}
