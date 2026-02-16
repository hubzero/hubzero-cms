<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting status=7 on reported wishes
 *
*/
class Migration20140702160951ComWishlist extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__wishlist_item', 'status')) {
            $ids = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->whereIn('category', ['wish'])
                ->loadColumn();

            if ($ids) {
                $ids = array_map('intval', $ids);

                $this->db->getQuery(true)
                    ->update('#__wishlist_item')
                    ->set(['status' => 7])
                    ->whereIn('id', $ids)
                    ->execute();
            }
        }

        if ($schema->hasColumn('#__item_comments', 'state')) {
            $ids = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->whereIn('category', ['itemcomment', 'wishcomment'])
                ->loadColumn();

            if ($ids) {
                $ids = array_map('intval', $ids);

                $this->db->getQuery(true)
                    ->update('#__item_comments')
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

        if ($schema->hasColumn('#__wishlist_item', 'status')) {
            $this->db->getQuery(true)
                ->update('#__wishlist_item')
                ->set(['status' => 0])
                ->where('status', '=', 7)
                ->execute();
        }

        if ($schema->hasColumn('#__item_comments', 'state')) {
            $this->db->getQuery(true)
                ->update('#__item_comments')
                ->set(['state' => 1])
                ->where('state', '=', 3)
                ->execute();
        }
    }
}
