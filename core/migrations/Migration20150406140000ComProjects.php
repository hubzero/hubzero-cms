<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding project repo table
 *
*/
class Migration20150406140000ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__project_repos')) {
            $schema->createTable('#__project_repos')
                ->integer('id', ['autoIncrement' => true])
                ->integer('project_id')
                ->string('name', 64)->default('')
                ->string('about', 255)->nullable()
                ->string('path', 255)->default('')
                ->integer('status')->default(0)
                ->datetime('created')
                ->integer('created_by')
                ->tinyInteger('remote')->default(0)
                ->string('engine', 100)->default('git')
                ->text('params')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('repo', ['project_id', 'name', 'path'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
