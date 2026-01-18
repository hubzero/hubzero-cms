<?php

// phpcs:disable PSR1.Files.SideEffects, PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding ws_proxy_host and ws_proxy_port to host table
 **/
class Migration20250220120652ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        // ADD COLUMN ws_proxy_host and ws_proxy_port to table host
        if ($mwdb->tableExists('host') && !$mwdb->tableHasField('host', 'service_host')) {
            $query = "ALTER TABLE host ADD COLUMN ws_proxy_host varchar(40) DEFAULT NULL AFTER service_host;";
            $mwdb->setQuery($query);
            $mwdb->query();

            $query = "ALTER TABLE host ADD COLUMN ws_proxy_port int DEFAULT NULL AFTER ws_proxy_host;";
            $mwdb->setQuery($query);
            $mwdb->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        // Drop column ws_proxy_host
        if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'ws_proxy_host')) {
            $query = "ALTER TABLE host DROP COLUMN ws_proxy_host;";
            $mwdb->setQuery($query);
            $mwdb->query();
        }

        // Drop column ws_proxy_port
        if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'ws_proxy_port')) {
            $query = "ALTER TABLE host DROP COLUMN ws_proxy_port;";
            $mwdb->setQuery($query);
            $mwdb->query();
        }
    }
}
