<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lstr\Git\RepositoryMirrorer\RepositoryMirrorer;
use Lstr\Github\Api\Exception as ApiException;

$config = require 'config.local.php';

try {
    $mirrorer  = new RepositoryMirrorer($config['repository_lists']);
    $exit_code = $mirrorer->run();

    exit($exit_code);
} catch (ApiException $ex) {
    echo "ApiException (" . get_class($ex) .  "): " . $ex->getMessage() . "\n";
    exit(2);
} catch (Exception $ex) {
    echo "Exception: (" . get_class($ex) .  "): " . $ex->getMessage() . "\n";
    exit(3);
}
