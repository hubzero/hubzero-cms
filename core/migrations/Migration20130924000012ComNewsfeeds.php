<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for newsfeeds table changes
 */
class Migration20130924000012ComNewsfeeds extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->setTableEngine('#__newsfeeds', 'MYISAM');

        $schema->table('#__newsfeeds')->alter()
            // Modify existing columns
            ->modifyColumnIfExists('id', function ($column) {
                $column->integer()->unsigned()->notNull()->autoIncrement();
            })
            ->modifyColumnIfExists('name', function ($column) {
                $column->string(100)->notNull()->default('');
            })
            ->modifyColumnIfExists('alias', function ($column) {
                $column->string(255)->notNull()->default('');
            })
            ->modifyColumnIfExists('link', function ($column) {
                $column->string(200)->notNull()->default('');
            })
            ->modifyColumnIfExists('numarticles', function ($column) {
                $column->integer()->unsigned()->notNull()->default(1);
            })
            ->modifyColumnIfExists('cache_time', function ($column) {
                $column->integer()->unsigned()->notNull()->default(3600);
            })
            ->modifyColumnIfExists('checked_out', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            // Add new columns
            ->addColumnIfNotExists('access', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->addColumnIfNotExists('language', function ($column) {
                $column->char(7)->notNull()->default('');
            })
            ->addColumnIfNotExists('params', function ($column) {
                $column->text()->notNull();
            })
            ->addColumnIfNotExists('created', function ($column) {
                $column->datetime()->notNull()->default('0000-00-00 00:00:00');
            })
            ->addColumnIfNotExists('created_by', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->addColumnIfNotExists('created_by_alias', function ($column) {
                $column->string(255)->notNull()->default('');
            })
            ->addColumnIfNotExists('modified', function ($column) {
                $column->datetime()->notNull()->default('0000-00-00 00:00:00');
            })
            ->addColumnIfNotExists('modified_by', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->addColumnIfNotExists('metakey', function ($column) {
                $column->text()->notNull();
            })
            ->addColumnIfNotExists('metadesc', function ($column) {
                $column->text()->notNull();
            })
            ->addColumnIfNotExists('metadata', function ($column) {
                $column->text()->notNull();
            })
            ->addColumnIfNotExists('xreference', function ($column) {
                $column->string(50)->notNull()->default('');
            })
            ->addColumnIfNotExists('publish_up', function ($column) {
                $column->datetime()->notNull()->default('0000-00-00 00:00:00');
            })
            ->addColumnIfNotExists('publish_down', function ($column) {
                $column->datetime()->notNull()->default('0000-00-00 00:00:00');
            })
            // Index operations
            ->dropIndex('catid')
            ->dropIndex('published')
            ->addIndex('idx_access', 'access')
            ->addIndex('idx_checkout', 'checked_out')
            ->addIndex('idx_state', 'published')
            ->addIndex('idx_catid', 'catid')
            ->addIndex('idx_createdby', 'created_by')
            ->addIndex('idx_language', 'language')
            ->addIndex('idx_xreference', 'xreference')
            ->execute();
    }
}
