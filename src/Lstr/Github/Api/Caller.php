<?php

namespace Lstr\Github\Api;

use Lstr\Github\Api\Exception;

class Caller
{
    private $config;
    private $api_key;
    private $username;



    public function __construct(array $config)
    {
        $this->config    = $config;

        if (empty($this->config['api_key'])) {
            throw new Exception("'api_key' is a required config option.");
        } elseif (empty($this->config['user_agent'])) {
            throw new Exception("'user_agent' is a required config option.");
        }
    }



    public function performGet($uri, array $args = null, array $headers = null)
    {
        $headers  = ($headers ?: array()) + array(
            "User-Agent: {$this->config['user_agent']}",
            'Authorization: token ' . $this->config['api_key'],
        );
        $args_str = '';
        if (is_array($args)) {
            $separator = '?';
            foreach ($args as $key => $val) {
                $args_str .= $separator . urlencode($key) . '=' . urlencode($val);
                $separator = '&';
            }
        }
        $full_url = "https://api.github.com" . $uri . $args_str;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $output = curl_exec($ch);

        if ($error_number = curl_errno($ch)) {
            throw new Exception\Curl(
                $ch,
                curl_error($ch),
                $error_number,
                $output
            );
        }

        $response_data = json_decode($output, true);
        $http_code     = $this->checkHttpCode($ch, $full_url, $response_data);

        curl_close($ch);

        $return = new Response(
            $http_code,
            $response_data
        );

        return $return;
    }



    protected function checkHttpCode($ch, $url, $response)
    {
        $http_code      = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $http_exception = null;
        if ($http_code === 401) {
            $http_exception = '\Lstr\Github\Api\Exception\Unauthorized';
        } elseif ($http_code === 403) {
            $http_exception = '\Lstr\Github\Api\Exception\Forbidden';
        } elseif ($http_code === 404) {
            $http_exception = '\Lstr\Github\Api\Exception\NotFound';
        } elseif (!(200 <= $http_code && $http_code < 400)) {
            $http_exception = '\Lstr\Github\Api\Exception\HttpCode';
        }

        if ($http_exception) {
            throw new $http_exception(
                $url,
                $http_code,
                $response
            );
        }

        return $http_code;
    }
}
