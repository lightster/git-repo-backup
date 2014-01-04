<?php

namespace Lstr\Git\RepositoryMirrorer;

use Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class RepositoryMirrorer
{
    private $logger;
    private $config;



    public function __construct($config)
    {
        $this->config = $config;
        $this->logger = new Logger();
    }



    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }



    public function run()
    {
        $list_loader     = new RepositoryListLoader($this->config);
        $list_loader->setLogger($this->logger);
        $repository_list = $list_loader->getRepositoryList();

        $exit_code = 0;

        foreach ($repository_list->getRepositories() as $repo) {
            $repo_mirror = new RepositoryMirror($repo['destination']);
            $repo_mirror->setLogger($this->logger);

            try {
                $remote_url = $repo['remote_url'];

                $this->logger->debug("Is '{$repo['destination']}' setup?");
                if ($repo_mirror->isRemoteSetup()) {
                    $this->logger->debug("  Yes");
                    $this->logger->debug("Does existing remote match '{$remote_url}'?");
                    $repo_mirror->checkRemoteUrlAgainst($remote_url);
                    $this->logger->debug("  Yes");
                    $this->logger->debug("Running update ...");
                    $repo_mirror->update();
                    $this->logger->debug("  done");
                } else {
                    $this->logger->debug("  No");
                    $this->logger->debug("Setting up using '{$remote_url}' ... ");
                    $repo_mirror->setup($remote_url);
                    $this->logger->debug("  done");
                }

                $this->logger->debug("");
            } catch (Exception $ex) {
                $this->logger->alert("An exception occurred while mirroring repository '{$remote_url}':");
                $this->logger->alert($ex->getMessage() . "\n\n\n");
                $exit_code = 1;
            }
        }

        return $exit_code;
    }
}
