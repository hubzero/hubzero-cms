<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding scope field to migrations table
 *
 */
class Migration20140716182939Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__migrations') && !$schema->hasColumn('#__migrations', 'scope')) {
            $schema->addColumn('#__migrations', 'scope')->string(255)->notNull()->default('');

            $this->db->getQuery(true)
                ->update('#__migrations')
                ->set(['scope' => PATH_ROOT . DS . 'migrations'])
                ->execute();
        }

        if ($schema->tableExists('migrations') && !$schema->hasColumn('migrations', 'scope')) {
            $schema->addColumn('migrations', 'scope')->string(255)->notNull()->default('');

            $this->db->getQuery(true)
                ->update('migrations')
                ->set(['scope' => PATH_ROOT . DS . 'migrations'])
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__migrations') && $schema->hasColumn('#__migrations', 'scope')) {
            $schema->dropColumn('#__migrations', 'scope');
        }

        if ($schema->tableExists('migrations') && $schema->hasColumn('migrations', 'scope')) {
            $schema->dropColumn('migrations', 'scope');
        }
    }
}
