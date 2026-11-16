<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Mysessions;

use Hubzero\Module\Module;
use Hubzero\Facades\Component;
use Hubzero\Facades\User;

/**
 * Module class for displaying a user's sessions
 */
class Mysessions extends Module
{
    /**
     * @var  \Hubzero\Database\Driver|null
     */
    protected $database;
    /**
     * @var  bool|null
     */
    protected $error;
    /**
     * @var  array<mixed>|null
     */
    protected $sessions;
    /**
     * @var  \Hubzero\Config\Registry|null
     */
    protected $toolsConfig;

    /**
     * Display module content
     *
     * @return  bool|null
     */
    public function display()
    {
        // Include mw libraries

        // Get database object
        $this->database = \Hubzero\Facades\App::get('db');

        // Get a connection to the middleware database
        $mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

        // Get tool paras
        $this->toolsConfig = Component::params('com_tools');

        // Set ACL for com_tools
        $authorized = User::authorise('core.manage', 'com_tools');

        // Ensure we have a connection to the middleware
        // A guest has no username to look sessions up by (getRecords() returns
        // false for one, and the layout counts the result)
        $this->error = false;
        if (
            User::isGuest()
            || !$mwdb
            || !$mwdb->connected()
            || !$this->toolsConfig->get('mw_on')
            || ($this->toolsConfig->get('mw_on') > 1 && !$authorized)
        ) {
            $this->error = true;
            return false;
        }

        // Run middleware command to create screenshots
        // only take snapshots if screenshots are on and
        // it's a valid account
        if (
            $this->params->get('show_screenshots', 1)
            && strstr(User::get('email'), '@') != '@invalid'
        ) {
            $cmd = "/bin/sh "
                . Component::path('com_tools')
                . "/scripts/mw screenshot "
                . User::get('username')
                . " 2>&1 </dev/null";
            exec($cmd, $results, $status);
        }

        // Get sessions
        $session = new \Components\Tools\Tables\Session($mwdb);
        $this->sessions = $session->getRecords(User::get('username'), '', false) ?: [];

        // Output module
        require $this->getLayoutPath();

        return null;
    }
}
