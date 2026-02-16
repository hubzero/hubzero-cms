<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting state=3 on reported KB comments
  *
**/
class Migration20140702122251ComKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__faq_comments', 'state')) {
            $ids = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->whereIn('category', ['kb'])
                ->loadColumn();

            if ($ids) {
                $ids = array_map('intval', $ids);

                $this->db->getQuery(true)
                    ->update('#__faq_comments')
                    ->set(['state' => 3])
                    ->whereIn('id', $ids)
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

        if ($schema->hasColumn('#__faq_comments', 'state')) {
            $this->db->getQuery(true)
                ->update('#__faq_comments')
                ->set(['state' => 1])
                ->where('state', '=', 3)
                ->execute();
        }
    }
}
