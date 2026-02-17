<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for getting rid of member favorites plugin
**/
class Migration20140506104910PlgMembersFavorites extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // add comment ID
        if ($schema->tableExists('#__xfavorites')) {
            $this->callback('progress', 'init', array('Running ' . __CLASS__ . '.php:'));

            // Check if there are any favorites
            $query = $this->db->getQuery(true)
                ->select('*')
                ->from('#__xfavorites')
                ->order('uid', 'ASC');
            if ($results = $query->loadObjectList()) {
                $objs  = array();
                $usrs  = array();
                $total = count($results);
                $i     = 1;

                foreach ($results as $result) {
                    // Does this user already have this favorite as a collection item?
                    $query = $this->db->getQuery(true)
                        ->select('p.id')
                        ->from('#__collections_posts', 'p')
                        ->innerJoin('#__collections_items AS i', 'p.item_id', 'i.id')
                        ->where('p.created_by', '=', $result->uid)
                        ->where('i.type', '=', 'resource')
                        ->where('i.object_id', '=', $result->oid);

                    if ($$query->doesntExist()) {
                        // No collection item

                        // Do we have a collection ID for this user?
                        if (!isset($usrs[$result->uid])) {
                            // No ID yet. Check if the user has a default collection
                            $query = $this->db->getQuery(true)
                                ->select('p.id')
                                ->from('#__collections', 'p')
                                ->where('p.object_id', '=', $result->uid)
                                ->where('p.object_type', '=', 'member')
                                ->where('p.is_default', '=', 1);
                            if (!($collection_id = $query->value('p.id'))) {
                                // No default collection.
                                // So, we make one.
                                $tbl = new \Components\Collections\Tables\Collection($this->db);
                                $tbl->setup($result->uid, 'member');
                                $usrs[$result->uid] = $tbl->id;
                            } else {
                                $usrs[$result->uid] = $collection_id;
                            }
                        }

                        // Check if we already have an item_id
                        if (!isset($objs[$result->oid])) {
                            // Check if an item entry exists
                            $b = new \Components\Collections\Tables\Item($this->db);
                            $b->loadType($result->oid, 'resource');
                            if (!$b->id) {
                                // No item entry

                                // Get some resource data
                                $query = $this->db->getQuery(true)
                                    ->select(['id', 'title', 'introtext'])
                                    ->from('#__resources')
                                    ->where('id', '=', $result->oid);
                                $resource = $query->first();
                                if (!$resource || !$resource->id) {
                                    continue;
                                }

                                // Create the item
                                $b->type        = 'resource';
                                $b->object_id   = $resource->id;
                                $b->title       = $resource->title;
                                $b->description = $resource->introtext;
                                $b->url         = 'index.php?option=com_resources&id=' . $resource->id;
                                if (!$b->check()) {
                                    continue;
                                }
                                if (!$b->store()) {
                                    continue;
                                }
                            }
                            // Set the item_id for thsi resource
                            // as it's most likely to be needed again
                            $objs[$result->oid] = $b->id;
                            unset($b);
                        }

                        // Create a post associating the item to a collection
                        $stick = new \Components\Collections\Tables\Post($this->db);
                        $stick->item_id       = $objs[$result->oid];
                        $stick->collection_id = $usrs[$result->uid];
                        $stick->created_by    = $result->uid;
                        if ($stick->check()) {
                            // Store new content
                            if (!$stick->store()) {
                                continue;
                            }
                        }
                    }

                    $progress = round($i / $total * 100);
                    $this->callback('progress', 'setProgress', array($progress));
                    $i++;
                }
            }

            $this->callback('progress', 'done');

            $schema->dropTable('#__xfavorites');

            $this->deletePluginEntry('members', 'favorites');
            $this->deletePluginEntry('resources', 'favorite');
            $this->deletePluginEntry('publications', 'favorite');
            $this->deleteModuleEntry('mod_myfavorites');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__xfavorites')) {
            $schema->createTable('#__xfavorites')
                ->id()
                ->integer('uid')->default(0)
                ->integer('oid')->default(0)
                ->string('tbl', 250)->nullable()
                ->datetime('faved')->default('0000-00-00 00:00:00')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $this->addPluginEntry('members', 'favorites');
            $this->addPluginEntry('resources', 'favorite');
            $this->addPluginEntry('publications', 'favorite');
            $this->addModuleEntry('mod_myfavorites');
        }
    }
}
