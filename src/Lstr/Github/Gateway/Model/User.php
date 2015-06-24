<?php

namespace Lstr\Github\Gateway\Model;

use Exception;
use Lstr\Github\Api\Api;
use Pimple;

class User
{
    private $container;
    private $data;

    private $user_public_orgs       = array();
    private $user_public_repos      = array();
    private $user_orgs_public_repos = array();
    private $user_repos             = array();



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



    public function getOrganization($organization, $data_or_loader = null)
    {
        $repository = $this->container['root']->getOrganization(
            $organization,
            $data_or_loader
        );

        return $repository;
    }



    public function getPublicOrganizations($data_or_loader = null)
    {
        $self = $this;
        return $this->container['lazy_loader']->lazyLoad(
            $this->user_public_orgs,
            'all',
            $data_or_loader ?: function (Pimple $container) use ($self) {
                return $container['api']->getOrgsForSpecificUser(array(
                    'user' => $self->getLogin(),
                ))->getData();
            },
            function (Pimple $container, $data) use ($self) {
                $orgs = array();
                foreach ($data as $org) {
                    $orgs[$org['login']] = $self->getOrganization(
                        $org['login'],
                        $org
                    );
                }
                return $orgs;
            }
        );
    }



    public function getUserRepository($repo_name, $data_or_loader = null)
    {
        $repository = $this->container['root']->getRepository(
            "{$this->getLogin()}/{$repo_name}",
            $data_or_loader
        );
        $repository->getOwner($this);

        return $repository;
    }



    public function getUserPublicRepositories($data_or_loader = null)
    {
        $self = $this;
        return $this->container['lazy_loader']->lazyLoad(
            $this->user_public_repos,
            'all',
            $data_or_loader ?: function (Pimple $container) use ($self) {
                return $container['api']->getReposForSpecificUser(array(
                    'user' => $self->getLogin(),
                ))->getData();
            },
            function (Pimple $container, $data) use ($self) {
                $repos = array();
                foreach ($data as $repo) {
                    $repos[$repo['full_name']] = $self->getUserRepository(
                        $repo['name'],
                        $repo
                    );
                }
                return $repos;
            }
        );
    }



    public function getOrganizationUserPublicRepositories($organization = null)
    {
        $self      = $this;
        $all_repos = array();

        if (null !== $organization) {
            $organizations = array($this->getOrganization($organization));
        } else {
            $organizations = $this->getPublicOrganizations();
        }

        foreach ($organizations as $org) {
            $all_repos = $all_repos + $this->container['lazy_loader']->lazyLoad(
                $this->user_orgs_public_repos,
                $org->getLogin(),
                function (Pimple $container) use ($org) {
                    return $org->getOrganizationUserPublicRepositories();
                }
            );
        }

        return $all_repos;
    }



    public function getAllPublicRepositories()
    {
        return $this->getUserPublicRepositories()
            + $this->getOrganizationUserPublicRepositories();
    }



    public function getAllRepositories($data_or_loader = null)
    {
        if ($this !== $this->container['root']->getAuthenticatedUser()) {
            throw new Exception(
                "Private repos can only be retrieved for the authenticated user."
            );
        }

        $self = $this;
        return $this->container['lazy_loader']->lazyLoad(
            $this->user_repos,
            'all',
            $data_or_loader ?: function (Pimple $container) use ($self) {
                return $container['api']->getReposForAuthenticatedUser()->getData();
            },
            function (Pimple $container, $data) use ($self) {
                $repos = array();
                foreach ($data as $repo) {
                    $repos[$repo['full_name']] = $self->getUserRepository(
                        $repo['name'],
                        $repo
                    );
                }
                return $repos;
            }
        );
    }
}
