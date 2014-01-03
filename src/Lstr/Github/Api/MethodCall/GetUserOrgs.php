<?php

namespace Lstr\Github\Api\MethodCall;

use Lstr\Github\Api\MethodCall;

class GetUserOrgs extends MethodCall
{
    protected function process(array $args)
    {
        return $this->getCaller()->performGet(
            '/user/orgs'
        );
    }
}
