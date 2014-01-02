<?php

namespace Lstr\Github\Api\MethodCall;

use Lstr\Github\Api\MethodCall;

class GetOrgsRepos extends MethodCall
{
    public function run()
    {
        $args = $this->checkArguments(
            array(
                'organization',
            ),
            array(
            )
        );

        return $this->getCaller()->performGet(
            '/orgs/' . $args['organization'] . '/repos'
        );
    }
}
