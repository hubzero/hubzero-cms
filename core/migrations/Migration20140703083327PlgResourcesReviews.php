<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting state=3 on resource reviews/comments
**/
class Migration20140703083327PlgResourcesReviews extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__resource_ratings', 'state')) {
            $query = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->whereIn('category', ['review']);
            if ($ids = $query->loadColumn()) {
                $ids = array_map('intval', $ids);


                $this->db->getQuery(true)
                    ->update('#__resource_ratings')
                    ->set(['state' => 3])
                    ->whereIn('id', $ids)
                    ->execute();
            }
        }

        if ($schema->hasColumn('#__item_comments', 'state')) {
            $query = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->whereIn('category', ['itemcomment', 'reviewcomment']);
            if ($ids = $query->loadColumn()) {
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

        if ($schema->hasColumn('#__resource_ratings', 'state')) {
            $this->db->getQuery(true)
                ->update('#__resource_ratings')
                ->set(['state' => 1])
                ->where('state', '=', 3)
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
