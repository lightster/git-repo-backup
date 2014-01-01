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
        $class      = 'Lstr\\Github\\Api\\Method\\' . $method;

        $api_method = new $class($this->caller);
        return $api_method->run($args);
    }
}
