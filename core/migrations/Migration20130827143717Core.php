<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming metrics_author_cluster if it exists, creating it otherwise
  *
**/
class Migration20130827143717Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('metrics_author_cluster') && !$schema->tableExists('#__metrics_author_cluster')) {
            $schema->renameTable('metrics_author_cluster', '#__metrics_author_cluster');
        } elseif (
            !$schema->tableExists('metrics_author_cluster')
            && !$schema->tableExists('#__metrics_author_cluster')
        ) {
            $schema->createTable('#__metrics_author_cluster')
                ->string('authorid', 60)->default('0')
                ->integer('classes')->default(0)
                ->integer('users')->default(0)
                ->integer('schools')->default(0)
                ->primaryKey('authorid')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('metrics_author_cluster') && $schema->tableExists('#__metrics_author_cluster')) {
            $schema->renameTable('#__metrics_author_cluster', 'metrics_author_cluster');
        }
    }
}
