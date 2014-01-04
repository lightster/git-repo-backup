<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lstr\Git\RepositoryMirrorer\Logger;
use Lstr\Git\RepositoryMirrorer\RepositoryMirrorer;
use Lstr\Github\Api\Exception as ApiException;

if (!isset($argv[1])) {
    echo "Usage: {$argv[0]} path-to-config\n";
    exit(100);
}

$config_file = $argv[1];
if (!is_file($config_file)) {
    echo "Could not locate config file: {$config_file}\n";
    exit(101);
}

$config = require $config_file;
if (!is_array($config)) {
    echo "\nConfig file did not return an array: {$config_file}\n";
    var_export($config);
    exit(102);
}

try {
    $mirrorer  = new RepositoryMirrorer($config['repository_lists']);
    $logger    = new Logger(!empty($config['verbose']));
    $mirrorer->setLogger($logger);
    $exit_code = $mirrorer->run();

    exit($exit_code);
} catch (ApiException $ex) {
    echo "ApiException (" . get_class($ex) .  "): " . $ex->getMessage() . "\n";
    exit(2);
} catch (Exception $ex) {
    echo "Exception: (" . get_class($ex) .  "): " . $ex->getMessage() . "\n";
    exit(3);
}
