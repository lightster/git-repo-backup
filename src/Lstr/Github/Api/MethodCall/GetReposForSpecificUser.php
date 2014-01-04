<?php

namespace Lstr\Github\Api\MethodCall;

use Lstr\Github\Api\MethodCall;

class GetReposForSpecificUser extends MethodCall
{
    protected function getRequiredArgumentsList()
    {
        return array(
            'user',
        );
    }


    protected function process(array $args)
    {
        return $this->getCaller()->performGet(
            '/users/' . $args['user'] . '/repos'
        );
    }
}
