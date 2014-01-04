<?php

namespace Lstr\Github\Gateway\Model;

use Lstr\Github\Api\Api;
use Pimple;

class Organization
{
    private $container;
    private $data;

    private $org_repos = array();



    public function __construct(Pimple $container, array $data)
    {
        $this->container   = $container;
        $this->data        = $data;
    }



    protected function getContainer()
    {
        return $this->container;
    }



    public function getLogin()
    {
        return $this->data['login'];
    }



    public function getOrganizationRepository($repo_name, $data_or_loader = null)
    {
        $repository = $this->container['root']->getRepository(
            $repo_name,
            $data_or_loader
        );
        $repository->getOwner($this);

        return $repository;
    }



    public function getOrganizationRepositories()
    {
        $self = $this;
        return $this->container['lazy_loader']->lazyLoad(
            $this->org_repos,
            'all',
            function (Pimple $container) use ($self) {
                return $container['api']->getReposForSpecificOrg(array(
                    'org' => $self->getLogin(),
                ))->getData();
            },
            function (Pimple $container, $data) use ($self) {
                $repos = array();
                foreach ($data as $repo) {
                    $repos[$repo['full_name']] = $self->getOrganizationRepository(
                        $repo['full_name'],
                        $repo
                    );
                }
                return $repos;
            }
        );
    }
}
