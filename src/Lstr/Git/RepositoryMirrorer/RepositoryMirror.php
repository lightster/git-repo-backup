<?php

namespace Lstr\Git\RepositoryMirrorer;

use Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class RepositoryMirror
{
    private $logger;
    private $destination_path;

    private $remote_url;



    public function __construct($destination_path)
    {
        $this->destination_path = $destination_path;
        $this->remote_url       = null;
        $this->logger           = new NullLogger();
    }



    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }



    public function isRemoteSetup()
    {
        if (!is_dir($this->destination_path)) {
            return false;
        }

        try {
            if ($this->getRemoteUrl()) {
                return true;
            }
        } catch (Exception $ex) {
            // do nothing; an exception mean
        }

        return false;
    }



    public function getRemoteUrl()
    {
        if (null !== $this->remote_url) {
            return $this->remote_url;
        }

        $command = "git --git-dir "
            . escapeshellarg($this->destination_path)
            . " ls-remote --get-url 2>&1";
        $results = $this->runCommand($command);

        // if there is a non-zero exit code or the exit code was not set,
        // an error occurred
        if ($results['exit_code'] || null === $results['exit_code']) {
            throw $this->generateException(
                "The remote URL could not be determined.",
                $results
            );
        }

        return $results['last_line'];
    }



    public function checkRemoteUrlAgainst($remote_url)
    {
        $existing_remote_url = $this->getRemoteUrl();

        if ($remote_url !== $existing_remote_url) {
            $message = "The existing mirror's remote URL and the "
                . "provided remote URL do not match.\n"
                . "Existing mirror remote URL:\n"
                . "  {$existing_remote_url}\n"
                . "Provided remote URL:\n"
                . "  {$remote_url}\n\n";
            throw new Exception($message);
        }

        return true;
    }



    public function setup($remote_url)
    {
        $command = "git clone -q --mirror "
            . escapeshellarg($remote_url)
            . " "
            . escapeshellarg($this->destination_path)
            . " 2>&1";
        $results = $this->runCommand($command);

        // if there is a non-zero exit code or the exit code was not set,
        // an error occurred
        if ($results['exit_code'] || null === $results['exit_code']) {
            throw $this->generateException(
                "The remote repository could not be mirrored.",
                $results
            );
        }

        $this->checkRemoteUrlAgainst($remote_url);

        return true;
    }



    public function update()
    {
        $command = "git --git-dir "
            . escapeshellarg($this->destination_path)
            . " remote update"
            . " 2>&1";
        $results = $this->runCommand($command);

        // if there is a non-zero exit code or the exit code was not set,
        // an error occurred
        if ($results['exit_code'] || null === $results['exit_code']) {
            throw $this->generateException(
                "The remote repository could not be mirrored.",
                $results
            );
        }

        return true;
    }



    protected function runCommand($command)
    {
        $results = array(
            'command'   => $command,
            'last_line' => null,
            'output'    => null,
            'exit_code' => null,
        );
        $this->logger->debug("Running:");
        $this->logger->debug("  {$command}...");
        $results['last_line'] = exec(
            $command,
            $results['output'],
            $results['exit_code']
        );
        $this->logger->debug("  done");

        return $results;
    }



    protected function generateException($message, array $results)
    {
        $ex_message = "{$message}\n"
            . "Command ran:\n"
            . "  {$results['command']}\n"
            . "Exit code:\n"
            . "  {$results['exit_code']}\n"
            . "Output:\n"
            . "  " . implode("\n  ", $results['output'])
            . "\n\n";
        return new Exception($ex_message);
    }
}
