<?php

namespace Lstr\Github\Api\MethodCall;

use Lstr\Github\Api\MethodCall;

class GetSpecificRepo extends MethodCall
{
    protected function getRequiredArgumentsList()
    {
        return array(
            'owner',
            'repo',
        );
    }


    protected function process(array $args)
    {
        return $this->getCaller()->performGet(
            '/repos/' . $args['owner'] . '/' . $args['repo']
        );
    }
}
