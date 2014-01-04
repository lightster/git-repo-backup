<?php

namespace Lstr\Github\Gateway\Model;

use Lstr\Github\Api\Api;
use Pimple;

class Github
{
    private $container;

    private $authenticated_users = array();
    private $users               = array();
    private $organizations       = array();
    private $repositories        = array();



    public function __construct(Pimple $container)
    {
        $this->container   = $container;
    }



    protected function getContainer()
    {
        return $this->container;
    }



    public function getAuthenticatedUsername()
    {
        return $this->getAuthenticatedUser()->getUsername();
    }



    public function getAuthenticatedUser($data_or_loader = null)
    {
        return $this->container['lazy_loader']->lazyLoad(
            $this->authenticated_users,
            'only',
            $data_or_loader ?: function (Pimple $container) {
                return $container['api']->getAuthenticatedUser()->getData();
            },
            function (Pimple $container, $data) {
                return new User($container, $data);
            }
        );
    }



    public function getUser($username, $data_or_loader = null)
    {
        if ($username == $this->getAuthenticatedUser()) {
            return $this->getUser();
        }

        return $this->container['lazy_loader']->lazyLoad(
            $this->users,
            $username,
            $data_or_loader ?: function (Pimple $container) use ($username) {
                return $container['api']->getSpecificUser(array(
                    'user' => $username,
                ))->getData();
            },
            function (Pimple $container, $data) {
                return new User($container, $data);
            }
        );
    }



    public function getOrganization($organization, $data_or_loader = null)
    {
        return $this->container['lazy_loader']->lazyLoad(
            $this->organizations,
            $organization,
            $data_or_loader ?: function (Pimple $container) use ($organization) {
                return $container['api']->getSpecificOrg(array(
                    'org' => $organization,
                ))->getData();
            },
            function (Pimple $container, $data) {
                return new Organization($container, $data);
            }
        );
    }



    public function getRepository($full_repo_name, $data_or_loader = null)
    {
        return $this->container['lazy_loader']->lazyLoad(
            $this->repositories,
            $full_repo_name,
            $data_or_loader ?: function (Pimple $container) use ($full_repo_name) {
                list($owner, $repo) = explode("/", $full_repo_name);
                return $container['api']->getSpecificRepo(array(
                    'owner' => $owner,
                    'repo'  => $repo,
                ))->getData();
            },
            function (Pimple $container, $data) {
                return new Repository($container, $data);
            }
        );
    }
}
