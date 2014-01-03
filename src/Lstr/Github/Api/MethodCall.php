<?php

namespace Lstr\Github\Api;

abstract class MethodCall
{
    private $caller;
    private $unprocessed_args;

    private $response;



    public function __construct(Caller $caller, array $args = array())
    {
        $this->caller = $caller;
        $this->unprocessed_args   = $args;
    }



    protected function getCaller()
    {
        return $this->caller;
    }



    protected function getUnprocessedArguments()
    {
        return $this->unprocessed_args;
    }



    protected function checkArguments(
        array $unprocessed_args,
        array $required_args,
        array $optional_args
    ) {
        $args = array();

        foreach ($required_args as $key) {
            if (!array_key_exists($key, $unprocessed_args)) {
                throw new Exception("Argument '{$key}' is required but not provided");
            }

            $args[$key] = $unprocessed_args[$key];
        }

        foreach ($optional_args as $key => $value) {
            $use_default = true;
            // If the key is numeric, then '$value' is actually the argument
            // name, there is no default value, and the argument should not
            // be returned unless the value is in $unprocessed_args
            if (is_int($key)) {
                $use_default = false;
                $key         = $value;
            }

            if (array_key_exists($key, $unprocessed_args)) {
                $args[$key] = $unprocessed_args[$key];
            } elseif ($use_default) {
                $args[$key] = $value;
            }
        }

        return $args;
    }



    protected function getRequiredArgumentsList()
    {
        return array();
    }



    protected function getOptionalArgumentsList()
    {
        return array();
    }



    abstract protected function process(array $args);



    public function run()
    {
        if (null !== $this->response) {
            return $this->response;
        }

        $processed_args = $this->checkArguments(
            $this->getUnprocessedArguments(),
            $this->getRequiredArgumentsList(),
            $this->getOptionalArgumentsList()
        );

        $this->response = $this->process($processed_args);

        return $this->response;
    }
}
