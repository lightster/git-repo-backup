<?php

namespace Lstr\Github\Api\MethodCall;

use Lstr\Github\Api\MethodCall;

class GetUserRepos extends MethodCall
{
    public function run()
    {
        return $this->getCaller()->performGet(
            '/user/repos'
        );
    }
}
