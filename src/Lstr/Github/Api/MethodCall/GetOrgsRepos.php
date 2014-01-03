<?php

namespace Lstr\Github\Api\MethodCall;

use Lstr\Github\Api\MethodCall;

class GetOrgsRepos extends MethodCall
{
    protected function getRequiredArgumentsList()
    {
        return array(
            'organization',
        );
    }


    protected function process(array $args)
    {
        return $this->getCaller()->performGet(
            '/orgs/' . $args['organization'] . '/repos'
        );
    }
}
