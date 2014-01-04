<?php

namespace Lstr\Github\Gateway;

use Lstr\Github\Gateway\Model\Github as GithubModel;

use Lstr\Github\Api\Api;
use Pimple;

class Github
{
    private $container;



    public function __construct(array $config)
    {
        $this->container                = new Pimple();
        $this->container['api']         = new Api($config);
        $this->container['lazy_loader'] = new LazyLoader($this->container);
        $this->container['root']        = new GithubModel($this->container);
    }



    public function __call($method, array $args)
    {
        return call_user_func_array(
            array($this->container['root'], $method),
            $args
        );
    }
}
