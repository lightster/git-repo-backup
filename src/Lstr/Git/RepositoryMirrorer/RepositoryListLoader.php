<?php

namespace Lstr\Git\RepositoryMirrorer;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class RepositoryListLoader
{
    private $config;
    private $logger;
    private $repository_list;



    public function __construct(array $config = array())
    {
        $this->config = $config;
        $this->logger = new NullLogger();
    }



    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }



    public function getRepositoryList()
    {
        if (null !== $this->repository_list) {
            return $this->repository_list;
        }

        $this->repository_list = new RepositoryList();

        foreach ($this->config as $config_name => $config) {
            $list_loader = new $config['list_loader_class']();
            if ($list_loader instanceof LoggerAwareInterface) {
                $list_loader->setLogger($this->logger);
            }
            $list_loader->setConfig($config_name, $config);

            $list_loader->addRepositories($this->repository_list);
        }

        return $this->repository_list;
    }
}
