<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting status=3 on wiki comments
  *
**/
class Migration20140703100727ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__wiki_comments', 'status')) {
            // Old flagged state was 1. Change it to 3.
            $this->db->getQuery(true)
                ->update('#__wiki_comments')
                ->set(['status' => 3])
                ->where('status', '=', 1)
                ->execute();

            // Mark all published entries as 1
            $this->db->getQuery(true)
                ->update('#__wiki_comments')
                ->set(['status' => 1])
                ->where('status', '=', 0)
                ->execute();

            $ids = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->whereIn('category', ['wiki', 'wikicomment'])
                ->loadColumn();

            if ($ids) {
                $ids = array_map('intval', $ids);

                $this->db->getQuery(true)
                    ->update('#__wiki_comments')
                    ->set(['status' => 3])
                    ->whereIn('id', $ids)
                    ->execute();
            }
        }

        $this->addPluginEntry('support', 'wiki');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__wiki_comments', 'status')) {
            $this->db->getQuery(true)
                ->update('#__wiki_comments')
                ->set(['status' => 0])
                ->where('status', '=', 1)
                ->execute();

            $this->db->getQuery(true)
                ->update('#__wiki_comments')
                ->set(['status' => 1])
                ->where('status', '=', 3)
                ->execute();
        }

        $this->deletePluginEntry('support', 'wiki');
    }
}
