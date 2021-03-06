<?php

namespace Lstr\Github\Api\MethodCall;

use Lstr\Github\Api\MethodCall;

class GetReposForAuthenticatedUser extends MethodCall
{
    protected function getOptionalArgumentsList()
    {
        return ['page'];
    }

    protected function process(array $args)
    {
        return $this->getCaller()->performGet(
            '/user/repos',
            $args
        );
    }
}
