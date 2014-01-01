<?php

namespace Lstr\Github\Api\Exception;

use Lstr\Github\Api\Exception;

class HttpCode extends Exception
{
    protected $url;
    protected $http_code;
    protected $response;



    public function __construct($url, $http_code, $response)
    {
        parent::__construct($response['message'], $http_code);

        $this->url       = $url;
        $this->http_code = $http_code;
        $this->response  = $response;
    }
}
