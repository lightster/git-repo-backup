<?php

namespace Lstr\Git\RepositoryMirrorer\RepositoryListLoader;

use Lstr\Git\RepositoryMirrorer\RepositoryList;

interface LoaderInterface
{
    public function setConfig($config_name, array $config);
    public function addRepositories(RepositoryList $repository_list);
}
