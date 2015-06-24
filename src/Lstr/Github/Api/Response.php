<?php

namespace Lstr\Github\Api;

class Response
{
    protected $http_code;
    protected $data;
    protected $raw_headers;



    public function __construct($http_code, $data, $raw_headers)
    {
        $this->http_code = $http_code;
        $this->data      = $data;
        $this->raw_headers = $raw_headers;
    }



    public function getHttpCode()
    {
        return $this->caller;
    }



    public function getData()
    {
        return $this->data;
    }



    public function getRawHeaders()
    {
        return $this->raw_headers;
    }
}
