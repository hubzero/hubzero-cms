<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting access value on accounts that have invalid values (0)
 *
*/
class Migration20160915110400ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__users')) {
            $config = null;
            if ($schema->tableExists('#__extensions')) {
                $config = $this->db->getQuery(true)
                    ->select('params')
                    ->from('#__extensions')
                    ->where('element', '=', 'com_members')
                    ->value('params');
            }

            $access = 1;
            if ($config) {
                $config = json_decode($config);
                if (is_object($config)) {
                    $access = (int)$config->privacy;
                    $access = $access ?: 1;
                }
            }

            $this->db->getQuery(true)
                ->update('#__users')
                ->set(['access' => (int)$access])
                ->where('access', '=', 0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // No down
    }
}
