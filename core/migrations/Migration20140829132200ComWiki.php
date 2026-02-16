<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices to wiki tables
  *
**/
class Migration20140829132200ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wiki_comments')) {
            $schema->addIndex('#__wiki_comments', 'idx_pageid', 'pageid');

            $schema->addIndex('#__wiki_comments', 'idx_version', 'version');

            $schema->addIndex('#__wiki_comments', 'idx_status', 'status');
        }

        if ($schema->tableExists('#__wiki_attachments')) {
            $schema->addIndex('#__wiki_attachments', 'idx_pageid', 'pageid');
        }

        if ($schema->tableExists('#__wiki_page')) {
            $schema->addIndex('#__wiki_page', 'idx_group_cn', 'group_cn');

            $schema->addIndex('#__wiki_page', 'idx_state', 'state');
        }

        if ($schema->tableExists('#__wiki_version')) {
            $schema->dropIndex('#__wiki_version', 'jos_wiki_version_pageid_idx');

            $schema->addIndex('#__wiki_version', 'idx_pageid', 'pageid');

            $schema->addIndex('#__wiki_version', 'idx_approved', 'approved');
        }

        if ($schema->tableExists('#__wiki_page_author')) {
            $schema->addIndex('#__wiki_page_author', 'idx_page_id', 'page_id');

            $schema->addIndex('#__wiki_page_author', 'idx_user_id', 'user_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wiki_comments')) {
            $schema->dropIndex('#__wiki_comments', 'idx_pageid');

            $schema->dropIndex('#__wiki_comments', 'idx_version');

            $schema->dropIndex('#__wiki_comments', 'idx_status');
        }

        if ($schema->tableExists('#__wiki_attachments')) {
            $schema->dropIndex('#__wiki_attachments', 'idx_pageid');
        }

        if ($schema->tableExists('#__wiki_page')) {
            $schema->dropIndex('#__wiki_page', 'idx_group_cn');

            $schema->dropIndex('#__wiki_page', 'idx_state');
        }

        if ($schema->tableExists('#__wiki_version')) {
            $schema->dropIndex('#__wiki_version', 'idx_pageid');

            $schema->addIndex('#__wiki_version', 'jos_wiki_version_pageid_idx', 'pageid');

            $schema->dropIndex('#__wiki_version', 'idx_approved');
        }

        if ($schema->tableExists('#__wiki_page_author')) {
            $schema->dropIndex('#__wiki_page_author', 'idx_page_id');

            $schema->dropIndex('#__wiki_page_author', 'idx_user_id');
        }
    }
}
