<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lstr\Github\Api\Api;
use Lstr\Github\Api\Exception as ApiException;

use Lstr\Git\RepositoryMirror;

$config = require 'config.local.php';

try {
    $repos = array();

    foreach ($config['github'] as $config_name => $backup_set) {
        $api = new Api($backup_set);

        $accessible_repos = array();
        foreach ($api->getUserRepos()->getData() as $repo) {
            $accessible_repos[$repo['full_name']] = $repo;
        }
        foreach ($api->getUserOrgs()->getData() as $user_org) {
            $organization = $user_org['login'];

            foreach ($api->getOrgsRepos(array('organization' => $organization))->getData() as $repo) {
                $accessible_repos[$repo['full_name']] = $repo;
            }
        }

        foreach ($backup_set['repos'] as $pattern_name => $repo_pattern) {
            $repo_names = array_keys($accessible_repos);

            if (!empty($repo_pattern['include_pattern'])) {
                $repo_names = preg_grep(
                    $repo_pattern['include_pattern'],
                    $repo_names
                );
            }
            if (!empty($repo_pattern['exclude_pattern'])) {
                $repo_names = preg_grep(
                    $repo_pattern['exclude_pattern'],
                    $repo_names,
                    PREG_GREP_INVERT
                );
            }

            if (!empty($repo_pattern['exclude_forks'])) {
                $repo_names = array_filter($repo_names, function ($repo_name) use ($accessible_repos) {
                    return !$accessible_repos[$repo_name]['fork'];
                });
            }

            if (empty($repo_names)) {
                $message = "Pattern matched no repos:\n"
                    . "'{$config_name}:{$pattern_name}' => "
                    . var_export($repo_pattern, true)
                    . "\n\n";
                fwrite(STDERR, $message);
            }

            foreach ($repo_names as $repo_name) {
                if (!isset($repos[$repo_name])) {
                    $repos[$repo_name] = array(
                        'repo_name'    => $repo_name,
                        'config_name'  => $config_name,
                        'backup_set'   => $backup_set,
                        'pattern_name' => $pattern_name,
                        'repo_pattern' => $repo_pattern,
                    );
                } else {
                    $other_match = $repos[$repo_name];
                    $message = "Repo '{$repo_name}' already matched by other pattern set.\n"
                        . "'{$other_match['config_name']}:{$other_match['pattern_name']}' => "
                        . var_export($other_match['repo_pattern'], true)
                        . "\n"
                        . "'{$config_name}:{$pattern_name}' => "
                        . var_export($repo_pattern, true)
                        . "\n\n";
                    fwrite(STDERR, $message);
                }
            }
        }
    }

    foreach ($repos as $repo_name => $repo) {
        $destination_location = str_replace(
            '{{REPO_NAME}}',
            $repo_name,
            $repo['backup_set']['destination_location']
        );
        $repo_mirror = new RepositoryMirror($destination_location);

        try {
            $remote_url = str_replace(
                '{{REPO_NAME}}',
                $repo_name,
                $repo['backup_set']['clone_url']
            );

            echo "Is '{$destination_location}' setup? ";
            if ($repo_mirror->isRemoteSetup()) {
                echo "Yes\n";
                echo "Does existing remote match '{$remote_url}'? ";
                $repo_mirror->checkRemoteUrlAgainst($remote_url);
                echo "Yes\n";
                echo "Running update ... ";
                $repo_mirror->update();
                echo "done\n";
            } else {
                echo "No\n";
                echo "Setting up using '{$remote_url}' ... ";
                $repo_mirror->setup($remote_url);
                echo "done\n";
            }

            echo "\n";
        } catch (Exception $ex) {
            echo "An exception occurred while mirroring repository '{$repo_name}':\n";
            echo $ex->getMessage() . "\n\n\n\n";
        }
    }
    //echo var_export(array_keys($repos), true) . "\n";
} catch (ApiException $ex) {
    echo "ApiException (" . get_class($ex) .  "): " . $ex->getMessage() . "\n";
} catch (Exception $ex) {
    echo "Exception: (" . get_class($ex) .  "): " . $ex->getMessage() . "\n";
}
