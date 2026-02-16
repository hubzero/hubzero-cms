<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming fulltext index on #__blog_entries
 *
*/
class Migration20140822154900ComBlog extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__blog_entries')) {
            $schema->dropIndex('#__blog_entries', 'jos_blog_entries_title_content_ftidx');

            $schema->addFulltextIndex('#__blog_entries', 'ftidx_title_content', ['title', 'content']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__blog_entries')) {
            $schema->dropIndex('#__blog_entries', 'ftidx_title_content');

            $schema->addFulltextIndex('#__blog_entries', 'jos_blog_entries_title_content_ftidx', ['title', 'content']);
        }
    }
}
