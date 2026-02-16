<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding state, modified, modified_by
 * fields to blog comments
 *
 */
class Migration20140702071151ComBlog extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__blog_comments', 'state')) {
            $schema->addColumn('state')->tinyInteger()->notNull()->default(0);

            $this->db->getQuery(true)
                ->update('#__blog_comments')
                ->set(['state' => 1])
                ->where('state', '=', 0)
                ->execute();

            $ids = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->whereIn('category', ['blog', 'blogcomment'])
                ->loadColumn();

            if ($ids) {
                $ids = array_map('intval', $ids);

                $this->db->getQuery(true)
                    ->update('#__blog_comments')
                    ->set(['state' => 3])
                    ->whereIn('id', $ids)
                    ->execute();
            }
        }

        if (!$schema->hasColumn('#__blog_comments', 'modified')) {
            $schema->addColumn('modified')->datetime()->notNull()->default('0000-00-00 00:00:00');
        }

        if (!$schema->hasColumn('#__blog_comments', 'modified_by')) {
            $schema->addColumn('modified_by')->integer()->notNull()->default(0);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__blog_comments', 'state')) {
            $schema->dropColumn('#__blog_comments', 'state');
        }

        if ($schema->hasColumn('#__blog_comments', 'modified')) {
            $schema->dropColumn('#__blog_comments', 'modified');
        }

        if ($schema->hasColumn('#__blog_comments', 'modified_by')) {
            $schema->dropColumn('#__blog_comments', 'modified_by');
        }
    }
}
