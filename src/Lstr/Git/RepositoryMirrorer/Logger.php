<?php

namespace Lstr\Git\RepositoryMirrorer;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    private $is_verbose;



    public function __construct($is_verbose = false)
    {
        $this->is_verbose = (bool)$is_verbose;
    }



    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->is_verbose
            && (LogLevel::INFO === $level|| LogLevel::DEBUG === $level)
        ) {
            fwrite(STDOUT, "{$message}\n");
        } else {
            fwrite(STDERR, "{$message}\n");
        }
    }
}
