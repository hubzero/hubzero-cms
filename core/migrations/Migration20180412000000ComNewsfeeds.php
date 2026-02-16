<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing newsfeeds tables
  *
**/
class Migration20180412000000ComNewsfeeds extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->deleteComponentEntry('newsfeeds');

        if ($schema->tableExists('#__newsfeeds')) {
            $schema->dropTable('#__newsfeeds');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->addComponentEntry('newsfeeds');

        if (!$schema->tableExists('#__newsfeeds')) {
            $schema->createTable('#__newsfeeds')
                ->integer('catid')->default(0)
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 100)->default('')
                ->string('alias', 100)->default('')
                ->string('link', 200)->default('')
                ->string('filename', 200)->nullable()
                ->tinyInteger('published')->default(0)
                ->unsignedInteger('numarticles')->default(1)
                ->unsignedInteger('cache_time')->default(3600)
                ->unsignedInteger('checked_out')->default(0)
                ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                ->integer('ordering')->default(0)
                ->tinyInteger('rtl')->default(0)
                ->unsignedTinyInteger('access')->default(0)
                ->char('language', 7)->default('')
                ->text('params')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->unsignedInteger('created_by')->default(0)
                ->string('created_by_alias', 255)->default('')
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->unsignedInteger('modified_by')->default(0)
                ->text('metakey')
                ->text('metadesc')
                ->text('metadata')
                ->string('xreference', 50)->default('')
                ->datetime('publish_up')->default('0000-00-00 00:00:00')
                ->datetime('publish_down')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->index('idx_access', 'access')
                ->index('idx_checkout', 'checked_out')
                ->index('idx_state', 'published')
                ->index('idx_catid', 'catid')
                ->index('idx_createdby', 'created_by')
                ->index('idx_language', 'language')
                ->index('idx_xreference', 'xreference')
                ->charset('utf8')
                ->execute();
        }
    }
}
