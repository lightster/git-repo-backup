<?php

namespace Lstr\Git\RepositoryMirrorer;

class RepositoryList
{
    private $repositories = array();



    public function addRepository($destination, $remote_url, $description = null)
    {
        $this->repositories[] = array(
            'destination' => $destination,
            'remote_url'  => $remote_url,
            'description' => $description,
        );
    }



    public function getRepositories()
    {
        return $this->repositories;
    }
}
