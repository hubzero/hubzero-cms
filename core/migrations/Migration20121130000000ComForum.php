<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing group_id to scope_id in forums
**/
class Migration20121130000000ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__forum_sections')) {
            if (
                !$schema->hasColumn('#__forum_sections', 'scope_id')
                && $schema->hasColumn('#__forum_sections', 'group_id')
            ) {
                $schema->renameColumn('#__forum_sections', 'group_id', 'scope_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
            if (!$schema->hasColumn('#__forum_sections', 'scope')) {
                $schema->addColumn('#__forum_sections', 'scope')
                    ->string(100)
                    ->notNull()
                    ->default('site')
                    ->after('state')
                    ->execute();

                $this->db->getQuery(true)
                    ->update('#__forum_sections')
                    ->set(['scope' => 'group'])
                    ->where('scope_id', '>', 0)
                    ->where('scope', '!=', 'course')
                    ->execute();

                $this->db->getQuery(true)
                    ->update('#__forum_sections')
                    ->set(['scope' => 'site'])
                    ->where('scope_id', '=', 0)
                    ->execute();
            }
        }
        if ($schema->tableExists('#__forum_categories')) {
            if (
                !$schema->hasColumn('#__forum_categories', 'scope_id')
                && $schema->hasColumn('#__forum_categories', 'group_id')
            ) {
                $schema->renameColumn('#__forum_categories', 'group_id', 'scope_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
            if (!$schema->hasColumn('#__forum_categories', 'scope')) {
                $schema->addColumn('#__forum_categories', 'scope')
                    ->string(100)
                    ->notNull()
                    ->default('site')
                    ->after('state')
                    ->execute();

                $this->db->getQuery(true)
                    ->update('#__forum_categories')
                    ->set(['scope' => 'group'])
                    ->where('scope_id', '>', 0)
                    ->where('scope', '!=', 'course')
                    ->execute();

                $this->db->getQuery(true)
                    ->update('#__forum_categories')
                    ->set(['scope' => 'site'])
                    ->where('scope_id', '=', 0)
                    ->execute();
            }
        }
        if ($schema->tableExists('#__forum_posts')) {
            if (
                !$schema->hasColumn('#__forum_posts', 'scope_id')
                && $schema->hasColumn('#__forum_posts', 'group_id')
            ) {
                $schema->renameColumn('#__forum_posts', 'group_id', 'scope_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
            if (!$schema->hasColumn('#__forum_posts', 'scope')) {
                $schema->addColumn('#__forum_posts', 'scope')
                    ->string(100)
                    ->notNull()
                    ->default('site')
                    ->after('hits')
                    ->execute();

                $this->db->getQuery(true)
                    ->update('#__forum_posts')
                    ->set(['scope' => 'group'])
                    ->where('scope_id', '>', 0)
                    ->where('scope', '!=', 'course')
                    ->execute();

                $this->db->getQuery(true)
                    ->update('#__forum_posts')
                    ->set(['scope' => 'site'])
                    ->where('scope_id', '=', 0)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__forum_sections')) {
            if (
                $schema->hasColumn('#__forum_sections', 'scope_id')
                && !$schema->hasColumn('#__forum_sections', 'group_id')
            ) {
                $schema->renameColumn('#__forum_sections', 'scope_id', 'group_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
            if ($schema->hasColumn('#__forum_sections', 'scope')) {
                $schema->dropColumn('#__forum_sections', 'scope');
            }
        }
        if ($schema->tableExists('#__forum_categories')) {
            if (
                $schema->hasColumn('#__forum_categories', 'scope_id')
                && !$schema->hasColumn('#__forum_categories', 'group_id')
            ) {
                $schema->renameColumn('#__forum_categories', 'scope_id', 'group_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
            if ($schema->hasColumn('#__forum_categories', 'scope')) {
                $schema->dropColumn('#__forum_categories', 'scope');
            }
        }
        if ($schema->tableExists('#__forum_posts')) {
            if (
                $schema->hasColumn('#__forum_posts', 'scope_id')
                && !$schema->hasColumn('#__forum_posts', 'group_id')
            ) {
                $schema->renameColumn('#__forum_posts', 'scope_id', 'group_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
            if ($schema->hasColumn('#__forum_posts', 'scope')) {
                $schema->dropColumn('#__forum_posts', 'scope');
            }
        }
    }
}
