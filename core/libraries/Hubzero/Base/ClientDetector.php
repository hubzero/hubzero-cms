<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base;

use Hubzero\Config\Repository;
use Hubzero\Database\MysqlDatabaseConnection;
use Hubzero\Http\Request;

/**
 * Client detector
 *
 * Inspired by Laravel's environment detector
 * http://laravel.com
 */
class ClientDetector
{
    /**
     * Request URI
     */
    private $request = null;

    /**
     * Create a new application instance.
     *
     * @return  void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Detect the application's current client.
     *
     * @param   array  $environments
     * @return  object
     */
    public function detect($environments)
    {
        if ($this->detectConsoleClient($environments)) {
            return ClientManager::client('cli', true);
        }

        return $this->detectWebClient($environments);
    }

    /**
     * Determine client for a web request.
     *
     * @param   array   $environments
     * @return  object
     */
    protected function detectWebClient($environments)
    {
        $default = ClientManager::client('site', true);

        // Check if the site is installed (database config exists)
        // If not, always return the Install client
        if (!$this->isInstalled()) {
            return ClientManager::client('install', true);
        }

        // To determine the current client, we'll simply iterate through the possible
        // clients and look for the one that matches the path for the request we
        // are currently processing here, then return back that client.
        foreach ($environments as $environment => $url) {
            if ($client = ClientManager::client($environment, true)) {
                if ($client->name == 'cli') {
                    continue;
                }

                // Legacy check based on file path
                //    Ex: JPATH_API would be set from ROOT/api/index.php
                // @TODO: Remove need for this code
                $const = 'JPATH_' . strtoupper($environment);

                if (
                    defined($const)
                    && defined('JPATH_BASE')
                    && JPATH_BASE == constant($const)
                ) {
                    return $client;
                }

                if (
                    defined($const)
                    && !defined(JPATH_BASE)
                    && JPATH_ROOT . $this->request->base(true) == constant($const)
                ) {
                    return $client;
                }

                // Check based on request path
                //    Ex: http://somehub.org/api
                if (
                    $this->request->segment(1) == $url
                    || $this->request->segment(1) == $client->name
                    || $this->request->segment(1) == $client->url
                ) {
                    return $client;
                }
            }
        }

        return $default;
    }

    /**
     * Determine if the client is command-line
     *
     * @param   array   $environments
     * @return  bool
     */
    protected function detectConsoleClient($environments)
    {
        return (php_sapi_name() == 'cli');
    }

    /**
     * Determine if the site is installed
     *
     * Checks for the existence of database configuration and verifies
     * the database has users (meaning setup is complete).
     *
     * @return  bool
     */
    protected function isInstalled()
    {
        // Load config directly since the Config facade
        // is not yet available during client detection
        $config = new Repository(PATH_APP);

        $host = $config->get('database.host');
        $db   = $config->get('database.db');
        $user = $config->get('database.user');

        if (empty($host) || empty($db) || empty($user)) {
            return false;
        }

        $dbConfig = $config->extract('database')->toArray();

        // Try to connect and check for users
        try {
            $pdo = MysqlDatabaseConnection::connectOrFail($dbConfig);
            $prefix = $dbConfig['dbprefix'] ?? 'jos_';
            $stmt = $pdo->query("SELECT COUNT(*) FROM `{$prefix}users`");

            if ($stmt && $stmt->fetchColumn() > 0) {
                return true;
            }
        } catch (\Exception $e) {
            // Can't connect or query - consider not installed
            return false;
        }

        return false;
    }
}
