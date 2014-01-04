<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lstr\Git\RepositoryMirror;
use Lstr\Git\RepositoryMirrorer\Logger;
use Lstr\Git\RepositoryMirrorer\RepositoryListLoader;
use Lstr\Github\Api\Exception as ApiException;
use Lstr\Github\Gateway\Github;

$config = require 'config.local.php';

try {
    $repos = array();

    $logger          = new Logger(true);
    $list_loader     = new RepositoryListLoader($config['repository_lists']);
    $list_loader->setLogger($logger);
    $repository_list = $list_loader->getRepositoryList();

    foreach ($repository_list->getRepositories() as $repo) {
        $repo_mirror = new RepositoryMirror($repo['destination']);

        try {
            $remote_url = $repo['remote_url'];

            $logger->debug("Is '{$repo['destination']}' setup?");
            if ($repo_mirror->isRemoteSetup()) {
                $logger->debug("  Yes");
                $logger->debug("Does existing remote match '{$remote_url}'?");
                $repo_mirror->checkRemoteUrlAgainst($remote_url);
                $logger->debug("  Yes");
                $logger->debug("Running update ...");
                $repo_mirror->update();
                $logger->debug("  done");
            } else {
                $logger->debug("  No");
                $logger->debug("Setting up using '{$remote_url}' ... ");
                $repo_mirror->setup($remote_url);
                $logger->debug("  done");
            }

            echo "\n";
        } catch (Exception $ex) {
            $logger->alert("An exception occurred while mirroring repository '{$repo_name}':");
            $logger->alert($ex->getMessage() . "\n\n\n");
        }
    }
} catch (ApiException $ex) {
    echo "ApiException (" . get_class($ex) .  "): " . $ex->getMessage() . "\n";
} catch (Exception $ex) {
    echo "Exception: (" . get_class($ex) .  "): " . $ex->getMessage() . "\n";
}
