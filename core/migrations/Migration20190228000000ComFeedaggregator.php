<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for un-installing com_feedaggregator and associated extensions
 *
*/
class Migration20190228000000ComFeedaggregator extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deleteComponentEntry('feedaggregator');

        $schema->dropTable('#__feedaggregator_feeds');
        $schema->dropTable('#__feedaggregator_posts');

        $this->deletePluginEntry('newsletter', 'feedaggregator');

        $this->deleteModuleEntry('mod_feedaggregator');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->addComponentEntry('feedaggregator');

        if (!$schema->tableExists('#__feedaggregator_feeds')) {
            $schema->createTable('#__feedaggregator_feeds')
                ->integer('id', ['autoIncrement' => true])
                ->string('url', 255)->nullable()
                ->date('created')->nullable()
                ->string('name', 255)->nullable()
                ->string('description', 255)->nullable()
                ->string('enabled', 45)
                ->primaryKey('id')
                ->uniqueIndex('id_UNIQUE', 'id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__feedaggregator_posts')) {
            $schema->createTable('#__feedaggregator_posts')
                ->integer('id', ['autoIncrement' => true])
                ->string('title', 255)->nullable()
                ->datetime('created')->nullable()
                ->string('created_by', 255)->nullable()
                ->integer('feed_id')
                ->string('status', 45)->nullable()
                ->text('description')->nullable()
                ->string('url', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $this->addPluginEntry('newsletter', 'feedaggregator');

        $this->addModuleEntry('mod_feedaggregator');
    }
}
