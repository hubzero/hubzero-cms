<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for forcing ROOT record to an ID of "1"
 *
 */
class Migration20150428103217ComCategories extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__categories')) {
            $root = $this->db->getQuery(true)
                ->select('*')
                ->from('#__categories')
                ->where('alias', '=', 'root')
                ->where('level', '=', 0)
                ->first();

            if ($root && $root->id != 1) {
                // Get the item that has the node's destined ID
                $first = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__categories')
                    ->where('id', '=', 1)
                    ->first();

                if ($first && $first->id) {
                    // Get the last item in the list
                    $last = $this->db->getQuery(true)
                        ->select('*')
                        ->from('#__categories')
                        ->order('id', 'DESC')
                        ->first();

                    // Push the first to the last.
                    // This shouldn't cause issues as the nested set maintains the
                    // proper order. ID should be irrelevant except for ROOT.
                    $this->db->getQuery(true)
                        ->update('#__categories')
                        ->set(['id' => $last->id + 1])
                        ->where('id', '=', $first->id)
                        ->execute();
                }

                // Update the root node's position
                $this->db->getQuery(true)
                    ->update('#__categories')
                    ->set(['id' => 1])
                    ->where('id', '=', $root->id)
                    ->execute();

                // Update the parent ID on all the node's immediate children
                $this->db->getQuery(true)
                    ->update('#__categories')
                    ->set(['parent_id' => 1])
                    ->where('parent_id', '=', $root->id)
                    ->execute();
            }

            // Make sure the root node is public
            $this->db->getQuery(true)
                ->update('#__categories')
                ->set(['access' => 1])
                ->where('id', '=', 1)
                ->where('access', '!=', 1)
                ->execute();
        }
    }
}
