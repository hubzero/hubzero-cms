<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add `parent` column to activity_logs and `starred` to activity_recipients
 *
*/
class Migration20161128165818Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__activity_logs')) {
            if (!$schema->hasColumn('#__activity_logs', 'parent')) {
                $schema->addColumn('#__activity_logs', 'parent')->integer()->notNull()->default(0)->execute();
            }

            $schema->addIndex('#__activity_logs', 'idx_parent', 'parent');

            if ($schema->hasColumn('#__activity_logs', 'description')) {
                $schema->modifyColumn('#__activity_logs', 'description')->text()->nullable()->default(null)->execute();
            }
        }

        if ($schema->tableExists('#__activity_recipients')) {
            if (!$schema->hasColumn('#__activity_recipients', 'starred')) {
                $schema->addColumn('#__activity_recipients', 'starred')
                    ->tinyInteger()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            $schema->addIndex('#__activity_recipients', 'idx_starred', 'starred');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__activity_logs')) {
            $schema->dropIndex('#__activity_logs', 'idx_parent');

            if ($schema->hasColumn('#__activity_logs', 'parent')) {
                $schema->dropColumn('#__activity_logs', 'parent');
            }

            if ($schema->hasColumn('#__activity_logs', 'description')) {
                $schema->modifyColumn('#__activity_logs', 'description')
                    ->string(250)
                    ->nullable()
                    ->default(null)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__activity_recipients')) {
            $schema->dropIndex('#__activity_recipients', 'idx_starred');

            if ($schema->hasColumn('#__activity_recipients', 'starred')) {
                $schema->dropColumn('#__activity_recipients', 'starred');
            }
        }
    }
}
