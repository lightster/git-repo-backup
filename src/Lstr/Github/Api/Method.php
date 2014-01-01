<?php

namespace Lstr\Github\Api;

abstract class Method
{
    private $caller;



    public function __construct(Caller $caller)
    {
        $this->caller = $caller;
    }



    protected function getCaller()
    {
        return $this->caller;
    }



    abstract public function run(array $args = null);
}
