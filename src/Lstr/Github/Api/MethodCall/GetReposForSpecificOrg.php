<?php

namespace Lstr\Github\Api\MethodCall;

use Lstr\Github\Api\MethodCall;

class GetReposForSpecificOrg extends MethodCall
{
    protected function getRequiredArgumentsList()
    {
        return array(
            'org',
        );
    }


    protected function process(array $args)
    {
        return $this->getCaller()->performGet(
            '/orgs/' . $args['org'] . '/repos'
        );
    }
}
