<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\App;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Utility\Composer;

/**
 * Repository class for adding and removing composer package repositories
 **/
class Repository extends Base implements CommandInterface
{
    /**
     * Default (required) command - just call help
     *
     * @return  void
     **/
    public function execute()
    {
        $this->output = $this->output->getHelpOutput();
        $this->help();
        $this->output->render();
        return;
    }

    /**
     * Show packages
     *
     * @museDescription Shows a list of active repositories
     *
     * @return  void
     **/
    public function show()
    {
        $this->requireComposerJson();

        $repositories = Composer::getRepositoryConfigs();
        $this->output->addRawFromAssocArray($repositories);
    }

    /**
     * Add a repository
     *
     * @museDescription Adds a repository
     *
     * @return  void
     **/
    public function add()
    {
        $this->requireComposerJson();

        //Add via composer.json for now
    }

    /**
     * Remove a repository
     *
     * @museDescription Removes a repository
     *
     * @return  void
     **/
    public function remove()
    {
        $this->requireComposerJson();

        //Remove via composer.json for now
    }

    /**
     * Shows help text for repository command
     *
     * @return  void
     **/
    public function help()
    {
        $this->helpOutput()
            ->addOverview('Add, remove, and update repositories for packages')
            ->addTasks($this);
    }

    /**
     * Stop with a message when the hub has no app/composer.json
     *
     * The Composer helper these tasks use read it unchecked, so a hub without
     * one ended the command in an uncaught exception and its stack trace.
     *
     * @return  void
     **/
    private function requireComposerJson()
    {
        if (!is_file(PATH_APP . '/composer.json')) {
            $this->output->error(
                'This hub has no app/composer.json; its packages and repositories are listed there.',
            );
        }
    }
}
