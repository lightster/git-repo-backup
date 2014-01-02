<?php

namespace Lstr\Github\Api;

class Response
{
    protected $http_code;
    protected $data;



    public function __construct($http_code, $data)
    {
        $this->http_code = $http_code;
        $this->data      = $data;
    }



    public function getHttpCode()
    {
        return $this->caller;
    }



    public function getData()
    {
        return $this->data;
    }
}
