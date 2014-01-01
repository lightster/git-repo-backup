<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lstr\Github\Api\Api;

$config = require 'config.local.php';

$api = new Api($config['users']['lightster']);

var_dump($api->getUserRepos());