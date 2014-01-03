<?php

namespace Lstr\Github\Api;

abstract class MethodCall
{
    private $caller;
    private $args;



    public function __construct(Caller $caller, array $args = array())
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



    protected function checkArguments(
        array $required_args,
        array $optional_args
    ) {
        $args = array();

        foreach ($required_args as $key) {
            if (!array_key_exists($key, $this->args)) {
                throw new Exception("Argument '{$key}' is required but not provided");
            }

            $args[$key] = $this->args[$key];
        }

        foreach ($optional_args as $key => $value) {
            $use_default = true;
            // If the key is numeric, then '$value' is actually the argument
            // name, there is no default value, and the argument should not
            // be returned unless the value is in $this->args
            if (is_int($key)) {
                $use_default = false;
                $key         = $value;
            }

            if (array_key_exists($key, $this->args)) {
                $args[$key] = $this->args[$key];
            } elseif ($use_default) {
                $args[$key] = $value;
            }
        }

        return $args;
    }



    abstract public function run();
}
