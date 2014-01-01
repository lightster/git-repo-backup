<?php

namespace Lstr\Github\Api\Exception;

use Lstr\Github\Api\Exception;

class Curl extends Exception
{
    protected $curl_handle;
    protected $message;
    protected $error_number;
    protected $output;



    public function __construct($curl_handle, $message, $error_number, $output)
    {
        $this->curl_handle  = $curl_handle;
        $this->message      = $message;
        $this->error_number = $error_number;
        $this->output       = $output;
    }
}
