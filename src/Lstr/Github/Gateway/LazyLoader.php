<?php

namespace Lstr\Github\Gateway;

use Lstr\Github\Api\Api;
use Pimple;

class LazyLoader
{
    private $container;



    public function __construct(Pimple $container)
    {
        $this->container = $container;
    }



    public function lazyLoad(
        array & $loaded,
        $key,
        $data_or_loader,
        $object_builder = null
    ) {
        if (array_key_exists($key, $loaded)) {
            return $loaded[$key];
        }

        if (is_callable($data_or_loader)) {
            $data = call_user_func(
                $data_or_loader,
                $this->container
            );
        } else {
            $data = $data_or_loader;
        }

        if (null === $object_builder) {
            $loaded[$key] = $data;
        } else {
            $loaded[$key] = call_user_func(
                $object_builder,
                $this->container,
                $data
            );
        }

        return $loaded[$key];
    }
}
