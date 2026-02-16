<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating access values and normalizing state column names
**/
class Migration20160325174400ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__forum_sections') && $schema->hasColumn('#__forum_sections', 'access')) {
            $this->db->getQuery(true)
                ->from('#__forum_sections')
                ->increment('access');
        }

        if ($schema->tableExists('#__forum_categories') && $schema->hasColumn('#__forum_categories', 'access')) {
            $this->db->getQuery(true)
                ->from('#__forum_categories')
                ->increment('access');
        }

        if ($schema->tableExists('#__forum_posts') && $schema->hasColumn('#__forum_posts', 'access')) {
            $this->db->getQuery(true)
                ->from('#__forum_posts')
                ->increment('access');
        }

        if (
            $schema->tableExists('#__forum_attachments')
            && $schema->hasColumn('#__forum_attachments', 'status')
            && !$schema->hasColumn('#__forum_attachments', 'state')
        ) {
            $schema->renameColumn('#__forum_attachments', 'status', 'state')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__forum_sections') && $schema->hasColumn('#__forum_sections', 'access')) {
            $this->db->getQuery(true)
                ->from('#__forum_sections')
                ->decrement('access');
        }

        if ($schema->tableExists('#__forum_categories') && $schema->hasColumn('#__forum_categories', 'access')) {
            $this->db->getQuery(true)
                ->from('#__forum_categories')
                ->decrement('access');
        }

        if ($schema->tableExists('#__forum_posts') && $schema->hasColumn('#__forum_posts', 'access')) {
            $this->db->getQuery(true)
                ->from('#__forum_posts')
                ->decrement('access');
        }

        if (
            $schema->tableExists('#__forum_attachments')
            && !$schema->hasColumn('#__forum_attachments', 'status')
            && $schema->hasColumn('#__forum_attachments', 'state')
        ) {
            $schema->renameColumn('#__forum_attachments', 'state', 'status')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }
}
