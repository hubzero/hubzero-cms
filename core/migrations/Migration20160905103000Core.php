<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add missing fields to #__resource_stats_cluster table
 *
*/
class Migration20160905103000Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__resource_stats_clusters')) {
            return;
        }

        if (!$schema->hasColumn('#__resource_stats_clusters', 'clustersize')) {
            $schema->addColumn('#__resource_stats_clusters', 'clustersize')
                ->string(255)
                ->notNull()
                ->default('')
                ->after('resid')
                ->execute();
        }

        if (!$schema->hasColumn('#__resource_stats_clusters', 'cluster_start')) {
            $schema->addColumn('#__resource_stats_clusters', 'cluster_start')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->after('clustersize')
                ->execute();
        }

        if (!$schema->hasColumn('#__resource_stats_clusters', 'cluster_end')) {
            $schema->addColumn('#__resource_stats_clusters', 'cluster_end')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->after('cluster_start')
                ->execute();
        }

        if (!$schema->hasColumn('#__resource_stats_clusters', 'institution')) {
            $schema->addColumn('#__resource_stats_clusters', 'institution')
                ->string(255)
                ->notNull()
                ->default('')
                ->after('cluster_end')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        /* This is a repair migration. A down method would be invalid */
        /* as this change should have happened in Migration20120101000001Core.php */
        /* Repair is only needed on some hubs, perhaps predating that migration.  */
    }
}
