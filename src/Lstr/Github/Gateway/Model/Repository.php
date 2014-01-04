<?php

namespace Lstr\Github\Gateway\Model;

use Exception;
use Lstr\Github\Api\Api;
use Pimple;

class Repository
{
    private $container;
    private $data;

    private $owner      = array();



    public function __construct(Pimple $container, array $data)
    {
        $this->container   = $container;
        $this->data        = $data;
    }



    protected function getContainer()
    {
        return $this->container;
    }



    public function getFullName()
    {
        return $this->data['full_name'];
    }



    public function getName()
    {
        return $this->data['name'];
    }



    public function isFork()
    {
        return $this->data['fork'];
    }



    public function getOwner($data_or_loader = null)
    {
        $self      = $this;
        $self_data = $this->data;
        return $this->container['lazy_loader']->lazyLoad(
            $this->owner,
            'only',
            $data_or_loader ?: function (Pimple $container) use ($self, $self_data) {
                if ($self_data['owner']['type'] === 'User') {
                    return $self->container['root']->getUser(
                        $self_data['owner']['login'],
                        $self_data['owner']
                    );
                } elseif ($self_data['owner']['type'] === 'Organization') {
                    return $self->container['root']->getOrganization(
                        $self_data['owner']['login'],
                        $self_data['owner']
                    );
                } else {
                    throw new Exception(
                        "Unexpected repository owner type: {$self_data['owner']['type']}"
                    );
                }
            }
        );
    }
}
