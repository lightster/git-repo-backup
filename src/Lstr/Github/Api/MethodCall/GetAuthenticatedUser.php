<?php

namespace Lstr\Github\Api\MethodCall;

use Lstr\Github\Api\MethodCall;

class GetAuthenticatedUser extends MethodCall
{
    protected function process(array $args)
    {
        return $this->getCaller()->performGet(
            '/user'
        );
    }
}
