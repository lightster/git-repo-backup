<?php

namespace Lstr\Github\Api\Method;

use Lstr\Github\Api\Method;

class GetUserRepos extends Method
{
    public function run()
    {
        return $this->getCaller()->performGet(
            '/user/repos'
        );
    }
}
