<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding params field to asset groups
**/
class Migration20140131091600HubzeroComments extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__comments')) {
            $results = $this->db->getQuery(true)
                ->select('*')
                ->from('#__comments')
                ->loadObjectList();

            if ($results && count($results) > 0) {
                $parents = array();
                foreach ($results as $r) {
                    if (substr($r->category, -7) != 'comment') {
                        $parents[$r->id] = array(
                            'referenceid' => $r->referenceid,
                            'category'    => $r->category,
                            'id'          => 0
                        );
                    }
                }

                $cls = 'Hubzero_Item_Comment';
                if (class_exists('\\Hubzero\\Item\\Comment')) {
                    $cls = '\\Hubzero\\Item\\Comment';
                }

                foreach ($results as $r) {
                    $record = new $cls($this->db);

                    if ($record instanceof \Hubzero\Database\Relational) {
                        $data = array(
                            'content'    => $r->comment,
                            'created'    => $r->added,
                            'created_by' => $r->added_by,
                            'state'      => $r->state,
                            'anonymous'  => $r->anonymous,
                            'notify'     => $r->email,
                            'item_id'    => $r->referenceid,
                            'item_type'  => $r->category
                        );

                        if (substr($r->category, -7) == 'comment') {
                            if (isset($parents[$r->referenceid])) {
                                $data['parent']    = $parents[$r->referenceid]['id'];
                                $data['item_id']   = $parents[$r->referenceid]['referenceid'];
                                $data['item_type'] = $parents[$r->referenceid]['category'];
                            }
                        }

                        $record
                            ->set($data)
                            ->save();
                    } else {
                        $record->item_id    = $r->referenceid;
                        $record->item_type  = $r->category;
                        $record->content    = $r->comment;
                        $record->created    = $r->added;
                        $record->created_by = $r->added_by;
                        $record->state      = $r->state;
                        $record->anonymous  = $r->anonymous;
                        $record->notify     = $r->email;

                        if (substr($r->category, -7) == 'comment') {
                            if (isset($parents[$r->referenceid])) {
                                $record->parent    = $parents[$r->referenceid]['id'];
                                $record->item_id   = $parents[$r->referenceid]['referenceid'];
                                $record->item_type = $parents[$r->referenceid]['category'];
                            }
                        }

                        $record->store();
                    }

                    if (substr($r->category, -7) != 'comment' && isset($parents[$r->id])) {
                        $parents[$r->id] = array(
                            'referenceid' => $r->referenceid,
                            'category'    => $r->category,
                            'id'          => $record->id
                        );
                    }
                }
            }

            $schema->dropTable('#__comments');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__comments')) {
            $schema->createTable('#__comments')
                ->unsignedInteger('filter_id', ['autoIncrement' => true])
                ->string('title', 255)
                ->string('alias', 255)
                ->tinyInteger('state')->default(1)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->unsignedInteger('created_by')
                ->string('created_by_alias', 255)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->unsignedInteger('modified_by')->default(0)
                ->unsignedInteger('checked_out')->default(0)
                ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                ->unsignedInteger('map_count')->default(0)
                ->text('data')
                ->mediumText('params')->nullable()
                ->primaryKey('filter_id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
