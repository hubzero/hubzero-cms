<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for remove rogue wishlists
  *
**/
class Migration20150115234546PlgGroupsWishlist extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wishlist')) {
            // Get records
            $lists = $this->db->getQuery(true)
                ->select('*')
                ->from('#__wishlist')
                ->where('category', '=', 'group')
                ->loadObjectList();

            // vars to hold counts
            $deletedLists  = 0;
            $deletedWishes = 0;

            // check to make sure each group wishlist has a valid group
            foreach ($lists as $list) {
                // load group
                $group = \Hubzero\User\Group::getInstance($list->referenceid);

                // if group doesnt exist we need to remove the list and wishes
                if (!$group || !is_object($group)) {
                    if ($schema->tableExists('#__wishlist_item')) {
                        $wishes = $this->db->getQuery(true)
                            ->select('*')
                            ->from('#__wishlist_item')
                            ->where('wishlist', '=', $list->id)
                            ->loadObjectList();

                        foreach ($wishes as $wish) {
                            if ($schema->tableExists('#__wishlist_implementation')) {
                                $this->db->getQuery(true)
                                    ->delete('#__wishlist_implementation')
                                    ->where('wishid', '=', $wish->id)
                                    ->execute();
                            }

                            if ($schema->tableExists('#__wish_attachments')) {
                                $this->db->getQuery(true)
                                    ->delete('#__wish_attachments')
                                    ->where('wish', '=', $wish->id)
                                    ->execute();
                            }

                            if ($schema->tableExists('#__wishlist_vote')) {
                                $this->db->getQuery(true)
                                    ->delete('#__wishlist_vote')
                                    ->where('wishid', '=', $wish->id)
                                    ->execute();
                            }
                        }

                        $this->db->getQuery(true)
                            ->delete('#__wishlist_item')
                            ->where('wishlist', '=', $list->id)
                            ->execute();
                    }

                    if ($schema->tableExists('#__wishlist_owners')) {
                        $this->db->getQuery(true)
                            ->delete('#__wishlist_owners')
                            ->where('wishlist', '=', $list->id)
                            ->execute();
                    }

                    if ($schema->tableExists('#__wishlist_ownergroups')) {
                        $this->db->getQuery(true)
                            ->delete('#__wishlist_ownergroups')
                            ->where('wishlist', '=', $list->id)
                            ->execute();
                    }

                    $this->db->getQuery(true)
                        ->delete('#__wishlist')
                        ->where('id', '=', $list->id)
                        ->execute();

                    $deletedLists++;
                }
            }
        }
    }
}
