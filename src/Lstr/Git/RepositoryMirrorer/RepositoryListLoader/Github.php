<?php

namespace Lstr\Git\RepositoryMirrorer\RepositoryListLoader;

use Lstr\Git\RepositoryMirrorer\RepositoryList;
use Lstr\Github\Gateway\Github as GithubGateway;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class GitHub implements LoggerAwareInterface, LoaderInterface
{
    private $config_name;
    private $config;
    private $logger;



    public function setLogger(LoggerInterface $logger)
    {
        $this->logger      = $logger;
    }



    public function setConfig($config_name, array $config)
    {
        $this->config_name = $config_name;
        $this->config      = $config;
    }



    public function addRepositories(RepositoryList $repository_list)
    {
        $config_name      = $this->config_name;
        $config           = $this->config;

        $github           = new GithubGateway($config);
        $auth_user        = $github->getAuthenticatedUser();
        $accessible_repos = $auth_user->getAllRepositories();

        $destination_template = $config['destination_location'];
        $clone_url_template   = $config['clone_url'];

        foreach ($config['patterns'] as $pattern_name => $pattern) {
            $repo_names = array_keys($accessible_repos);

            if (!empty($pattern['include_pattern'])) {
                $repo_names = preg_grep(
                    $pattern['include_pattern'],
                    $repo_names
                );
            }
            if (!empty($pattern['exclude_pattern'])) {
                $repo_names = preg_grep(
                    $pattern['exclude_pattern'],
                    $repo_names,
                    PREG_GREP_INVERT
                );
            }

            if (!empty($pattern['exclude_forks'])) {
                $repo_names = array_filter($repo_names, function ($repo_name) use ($accessible_repos) {
                    return !$accessible_repos[$repo_name]->isFork();
                });
            }

            if (empty($repo_names)) {
                $message = "Pattern matched no repos:\n"
                    . "'{$config_name}:{$pattern_name}' => "
                    . var_export($pattern, true)
                    . "\n";
                $this->logger->error($message);
            }

            foreach ($repo_names as $repo_name) {
                $destination = str_replace(
                    '{{REPO_NAME}}',
                    $repo_name,
                    $destination_template
                );
                $remote_url = str_replace(
                    '{{REPO_NAME}}',
                    $repo_name,
                    $clone_url_template
                );

                $repository_list->addRepository(
                    $destination,
                    $remote_url,
                    "{$config_name}:{$pattern_name}:{$repo_name}"
                );
            }
        }
    }
}
