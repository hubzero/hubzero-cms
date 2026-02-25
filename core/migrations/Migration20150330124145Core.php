<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing the default database driver
 *
*/
class Migration20150330124145Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // Try old Joomla-style configuration.php format
        $this->updateOldConfigFormat();

        // Try new app/config/database.php format
        $this->updateNewConfigFormat();
    }

    /**
     * Update old Joomla-style configuration.php format
     *
     * @return void
     **/
    private function updateOldConfigFormat()
    {
        $configFile = PATH_ROOT . DS . 'configuration.php';

        // Skip if configuration.php doesn't exist or is empty
        if (!file_exists($configFile) || filesize($configFile) === 0) {
            return;
        }

        $configuration = file_get_contents($configFile);

        // Skip if not using the old Joomla-style class format
        if (strpos($configuration, 'var $dbtype') === false) {
            return;
        }

        // Skip if already using pdo
        if (preg_match('/var \$dbtype[\s]*=[\s]*[\'"]pdo[\'"]/', $configuration)) {
            return;
        }

        $configuration = preg_replace('/(var \$dbtype[\s]*=[\s]*[\'"]*)mysql([\'"]*)/', '$1pdo$2', $configuration);

        // The configuration file typically doesn't even give write permissions to the owner
        $permissions = substr(decoct(fileperms($configFile)), 2);

        if (substr($permissions, 1, 1) != '6') {
            \Hubzero\Facades\App::get('filesystem')->setPermissions(
                $configFile,
                substr_replace($permissions, '6', 1, 1)
            );
        }

        if (!file_put_contents($configFile, $configuration)) {
            $this->setError('Unable to write configuration.php: permission denied', 'warning');
            return;
        }

        // Change permissions back to what they were before
        \Hubzero\Facades\App::get('filesystem')->setPermissions($configFile, $permissions);
    }

    /**
     * Update new app/config/database.php format
     *
     * @return void
     **/
    private function updateNewConfigFormat()
    {
        $configFile = PATH_APP . DS . 'config' . DS . 'database.php';

        // Skip if database.php doesn't exist
        if (!file_exists($configFile)) {
            return;
        }

        $configuration = file_get_contents($configFile);

        // Skip if not using array format with dbtype
        if (strpos($configuration, "'dbtype'") === false && strpos($configuration, '"dbtype"') === false) {
            return;
        }

        // Skip if already using pdo
        if (preg_match('/[\'"]dbtype[\'"]\s*=>\s*[\'"]pdo[\'"]/', $configuration)) {
            return;
        }

        // Replace mysql with pdo in array format
        $configuration = preg_replace(
            '/([\'"]dbtype[\'"]\s*=>\s*[\'"])mysql([\'"])/',
            '$1pdo$2',
            $configuration
        );

        if (!file_put_contents($configFile, $configuration)) {
            $this->setError('Unable to write app/config/database.php: permission denied', 'warning');
            return;
        }
    }
}
