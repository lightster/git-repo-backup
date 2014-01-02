<?php

namespace Lstr\Github\Api;

abstract class Method
{
    private $caller;
    private $args;



    public function __construct(Caller $caller, array $args = null)
    {
        $this->caller = $caller;
        $this->args   = $args;
    }



    protected function getCaller()
    {
        return $this->caller;
    }



    protected function getArgs()
    {
        return $this->args;
    }



    abstract public function run();
}
