<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lstr\Github\Api\Api;
use Lstr\Github\Api\Exception as ApiException;

$config = require 'config.local.php';

$api = new Api($config['users']['lightster']);

try {
    foreach ($api->getUserRepos()->getData() as $repo) {
        echo "{$repo['full_name']}\n";
    }
    foreach ($api->getUserOrgs()->getData() as $user_org) {
        $organization = $user_org['login'];

        echo "\n\n";
        echo "== {$organization} ==\n";
        foreach ($api->getOrgsRepos(array('organization' => $organization))->getData() as $repo) {
            echo "{$repo['full_name']}\n";
        }
    }
} catch (ApiException $ex) {
    echo "ApiException (" . get_class($ex) .  "): " . $ex->getMessage() . "\n";
} catch (Exception $ex) {
    echo "Exception: (" . get_class($ex) .  "): " . $ex->getMessage() . "\n";
}
