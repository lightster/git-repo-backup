# Git Repo Backup

Git Repo Backup will create backups of your remote git repositories.  Git Repo Backup makes use of `git clone --mirror` and `git remote update`.

Git Repo Backup is configurable to use the GitHub API automatically adding new repos to the backup rotation as they are created on GitHub.

## Dependencies

1.  [Composer](http://getcomposer.org)
2.  [Git 1.6+](http://git-scm.com/)
3.  [PHP 5.3+](http://php.net)

Other dependencies are included via Composer.

## Get it Working

1. Clone git-repo-backup to the machine you want the directories.

        git clone git@github.com:lightster/git-repo-backup.git
        cd git-repo-backup
        
2. Install dependencies via Composer.

        composer install
        
3. Create a config file.
    
        <?php
        return array(
            // set 'verbose' to false after everything is working as expected
            'verbose'      => true,
            'repository_lists' => 
                'github:lightster' => array(
                    'list_loader_class' => 'Lstr\\Git\\RepositoryMirrorer\\RepositoryListLoader\\Github',
                    
                    // Do NOT replace {{REPO_NAME}}. Git Repo Backup will fill that in.
                    'clone_url'         => 'git@github.com:{{REPO_NAME}}.git',
                    'destination_location' => '/home/lightster/backup/github/' . date('H') . '/{{REPO_NAME}}.git',
                    
                    'user_agent'        => 'Lstr-Github-Api (GITHUB_USERNAME)',
                    'api_key'           => 'API_KEY',
                    'patterns'          => array(
                        'lightster/*' => array(
                            'include_pattern' => '#^lightster/.*$#',
                            'exclude_pattern' => '#\-test\-#',
                            'exclude_forks'   => true,
                        ),
                    ),
                ),
        );

4. Run bin/mirror.php

        php path/to/git-repo-backup/bin/mirror.php path/to/config.php
        
## Config Files

Config files return a PHP array containing config options.

Config files do not need to be stored anywhere specific since the path to the config file is passed in at run time.

### verbose - boolean

If verbose is set to false or is not set at all, only errors will be outputted.

### repository_lists - array of `repository_specification` arrays

The key of each array item (e.g. 'github:lightster') is a name used only for debugging/error outputting.

The value of each array item is a `repository_specification`.

### repository_specification - array of options for specifying how to generate a repository list

* `destination_location` should be a string containing the path to where repositories should be backed up. 

    The destination location string should contain `{{REPO_NAME}}`, which will be replaced at run-time by the name of each repo being backed up.
    
* `clone_url` — The URL to use when cloning a git repository.

    The clone URL string should contain `{{REPO_NAME}}`, which will be replaced at run-time by the name of each repo being backed up.
    	
    When using Git over SSH, the SSH config file (.ssh/config) of the user Git Repo Backup is ran as will be respected.    

* `list_loader_class` should be one of:

    * `'Lstr\\Git\\RepositoryMirrorer\\RepositoryListLoader\\Github'` — Loads a list of repositories via the GitHub API

    Repository list loader classes require other configuration options in the repository specification.

## Repository List Loader Classes

### GitHub

The GitHub loader will replace `{{REPO_NAME}}` in the `destination_location` and `clone_url` (described above) with the full name of the repository being backed up, such as `lightster/git-repo-name`.

When using the GitHub loader, the following options are also required:
       
* `user_agent` — The text that is sent to GitHub in the user-agent header.

    The GitHub API [requires a user agent header](http://developer.github.com/v3/#user-agent-required) be passed to it.  The API documentation recommends using a GitHub username.

* `api_key` — The API key to use when connecting to the GitHub API.

    See GitHub's documentation for [creating an access token for command-line use](https://help.github.com/articles/creating-an-access-token-for-command-line-use) guidance on creating a token (API key).
    
* `patterns` — An array containing one or more set of repository filter patterns.

    The key of each array item (e.g. 'lightster/*') is a name used only for debugging/error outputting.

    * `include_pattern` — A regular expression that will be passed to `preg_grep`.  Any repositories that match the regular expression will be included in the list of repositories to be backed up unless the repo also matches the `exclude_pattern`.
    * `exclude_pattern` — A regular expression that will be passed to `preg_grep`. Any repositories that match the regular expression will be excluded from the list of repositories to be backed up.
    * `exclude_forks` — A boolean to determine if forked repositories should be backed up. By default forks are included.

    While none of the pattern filter options are required, at least one pattern set must be required. That is, to backup all repositories a blank pattern set must be provided:
    
        'patterns' => array(
            'all' => array(
            )
        )
    
