<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding support query folders
**/
class Migration20141010150100ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__support_queries', 'created_by')) {
            $schema->addColumn('#__support_queries', 'created_by')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }

        if (!$schema->hasColumn('#__support_queries', 'ordering')) {
            $schema->addColumn('#__support_queries', 'ordering')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }

        if (!$schema->hasColumn('#__support_queries', 'folder_id')) {
            $schema->addColumn('#__support_queries', 'folder_id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }

        if (!$schema->tableExists('#__support_query_folders')) {
            $schema->createTable('#__support_query_folders')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('user_id')->default(0)
                ->string('title', 200)->default('')
                ->string('alias', 200)->default('')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->unsignedInteger('created_by')->default(0)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->unsignedInteger('modified_by')->default(0)
                ->integer('ordering')->default(0)
                ->unsignedTinyInteger('iscore')->default(0)
                ->primaryKey('id')
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();

            $folders = [
                [
                    'id' => 1,
                    'user_id' => 0,
                    'title' => 'Common',
                    'alias' => 'common',
                    'created' => '0000-00-00 00:00:00',
                    'created_by' => 0,
                    'modified' => '0000-00-00 00:00:00',
                    'modified_by' => 0,
                    'ordering' => 1,
                    'iscore' => 1,
                ],
                [
                    'id' => 2,
                    'user_id' => 0,
                    'title' => 'Mine',
                    'alias' => 'mine',
                    'created' => '0000-00-00 00:00:00',
                    'created_by' => 0,
                    'modified' => '0000-00-00 00:00:00',
                    'modified_by' => 0,
                    'ordering' => 2,
                    'iscore' => 1,
                ],
                [
                    'id' => 3,
                    'user_id' => 0,
                    'title' => 'Custom',
                    'alias' => 'custom',
                    'created' => '0000-00-00 00:00:00',
                    'created_by' => 0,
                    'modified' => '0000-00-00 00:00:00',
                    'modified_by' => 0,
                    'ordering' => 3,
                    'iscore' => 1,
                ],
                [
                    'id' => 4,
                    'user_id' => 0,
                    'title' => 'Common',
                    'alias' => 'common',
                    'created' => '0000-00-00 00:00:00',
                    'created_by' => 0,
                    'modified' => '0000-00-00 00:00:00',
                    'modified_by' => 0,
                    'ordering' => 1,
                    'iscore' => 2,
                ],
                [
                    'id' => 5,
                    'user_id' => 0,
                    'title' => 'Mine',
                    'alias' => 'mine',
                    'created' => '0000-00-00 00:00:00',
                    'created_by' => 0,
                    'modified' => '0000-00-00 00:00:00',
                    'modified_by' => 0,
                    'ordering' => 2,
                    'iscore' => 2
                ]
            ];

            foreach ($folders as $folder) {
                $this->db->getQuery(true)
                    ->insert('#__support_query_folders')
                    ->set($folder)
                    ->execute();
            }

            /*
                folders:
                    1 = common
                    2 = mine
                    3 = custom
                    4 = common (not in acl)
                    5 = mine (not in acl)
            */

            // Update "Common" queries
            $this->db->getQuery(true)
                ->update('#__support_queries')
                ->set(['folder_id' => 1])
                ->where('iscore', '=', 2)
                ->where('folder_id', '=', 0)
                ->execute();

            // Update "Mine" queries
            $this->db->getQuery(true)
                ->update('#__support_queries')
                ->set(['folder_id' => 2])
                ->where('iscore', '=', 1)
                ->where('folder_id', '=', 0)
                ->execute();

            // Update "Common not in ACL" queries
            $this->db->getQuery(true)
                ->update('#__support_queries')
                ->set(['folder_id' => 4])
                ->where('iscore', '=', 4)
                ->where('folder_id', '=', 0)
                ->execute();

            // Get all the "mine" queries
            $queries = $this->db->getQuery(true)
                ->select('*')
                ->from('#__support_queries')
                ->where('folder_id', '=', 2)
                ->order('id')
                ->loadObjectList();

            if ($queries) {
                $path = PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'tables'
                    . DS . 'query.php';
                if (!file_exists($path)) {
                    $path = PATH_ROOT . DS . 'administrator' . DS . 'components'
                        . DS . 'com_support' . DS . 'tables' . DS . 'query.php';
                }
                include_once $path;

                $tbl = '\\Components\\Support\\Tables\\Query';
                if (class_exists('SupportQuery')) {
                    $tbl = 'SupportQuery';
                }

                // Copy the queries to the new folder
                foreach ($queries as $k => $query) {
                    $stq = new $tbl($this->db);
                    $stq->bind($query);
                    $stq->id        = null;
                    $stq->user_id   = 0;
                    $stq->folder_id = 5;
                    $stq->ordering  = $k;
                    $stq->iscore    = 4;
                    $stq->store();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__support_queries', 'created_by')) {
            $schema->dropColumn('#__support_queries', 'created_by');
        }

        if ($schema->hasColumn('#__support_queries', 'ordering')) {
            $schema->dropColumn('#__support_queries', 'ordering');
        }

        if ($schema->hasColumn('#__support_queries', 'folder_id')) {
            $schema->dropColumn('#__support_queries', 'folder_id');
        }

        $schema->dropTable('#__support_query_folders');
    }
}
