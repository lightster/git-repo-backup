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
        } elseif (empty($this->config['username'])) {
            throw new Exception("'username' is a required config option.");
        }
    }



    public function performGet($uri, array $args = null, array $headers = null)
    {
        $headers  = ($headers ?: array()) + array(
            "User-Agent: Lstr-Github-Api (username: {$this->config['username']})",
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

        $output = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($error_number = curl_errno($ch)) {
            throw new Exception\Curl(
                $ch,
                curl_error($ch),
                $error_number,
                $output
            );
        }

        $return = array(
            'http_code' => $http_code,
            'output'    => json_decode($output, true),
        );

        curl_close($ch);

        return $return;
    }
}
