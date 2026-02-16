<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing ticket owner field
 * to use user ID instead of username
 *
*/
class Migration20140627062357ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Support tickets
        $columns = $schema->getTableColumns('#__support_tickets');
        $ownerType = $columns['owner'] ?? '';

        // Check if column type contains 'int' (handles both 'int(11)' and 'INTEGER')
        if (stripos($ownerType, 'int') === false) {
            $owners = $this->db->getQuery(true)
                ->select('t.owner', 'username')
                ->select('u.id')
                ->from('#__support_tickets', 't')
                ->leftJoin('#__users AS u', 't.owner', 'u.username')
                ->where('t.owner', '!=', '')
                ->whereNotNull('t.owner')
                ->loadObjectList();

            if ($owners) {
                foreach ($owners as $owner) {
                    $this->db->getQuery(true)
                        ->update('#__support_tickets')
                        ->set(['owner' => $owner->id])
                        ->where('owner', '=', $owner->username)
                        ->execute();
                }
            }

            $this->db
                ->schema()
                ->modifyColumn('#__support_tickets', 'owner')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }

        // Support ticket comments
        $columns = $schema->getTableColumns('#__support_comments');
        $createdByType = $columns['created_by'] ?? '';

        // Check if column type contains 'int' (handles both 'int(11)' and 'INTEGER')
        if (stripos($createdByType, 'int') === false) {
            $creators = $this->db->getQuery(true)
                ->select('t.created_by', 'username')
                ->select('u.id')
                ->from('#__support_comments', 't')
                ->leftJoin('#__users AS u', 't.created_by', 'u.username')
                ->where('t.created_by', '!=', '')
                ->whereNotNull('t.created_by')
                ->loadObjectList();

            if ($creators) {
                foreach ($creators as $creator) {
                    $this->db->getQuery(true)
                        ->update('#__support_comments')
                        ->set(['created_by' => $creator->id])
                        ->where('created_by', '=', $creator->username)
                        ->execute();
                }
            }

            $this->db
                ->schema()
                ->modifyColumn('#__support_comments', 'created_by')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // Support tickets
        $columns = $schema->getTableColumns('#__support_tickets');
        $ownerType = $columns['owner'] ?? '';

        // Check if column type contains 'int' (handles both 'int(11)' and 'INTEGER')
        if (stripos($ownerType, 'int') !== false) {
            $owners = $this->db->getQuery(true)
                ->select('u.username')
                ->select('t.owner', 'id')
                ->from('#__support_tickets', 't')
                ->leftJoin('#__users AS u', 't.owner', 'u.id')
                ->where('t.owner', '>', 0)
                ->loadObjectList();

            if ($owners) {
                foreach ($owners as $owner) {
                    $this->db->getQuery(true)
                        ->update('#__support_tickets')
                        ->set(['owner' => $owner->username])
                        ->where('owner', '=', $owner->id)
                        ->execute();
                }
            }

            $this->db->schema()->modifyString('#__support_tickets', 'owner', 50)->notNull()->default('');
        }

        // Support ticket comments
        $columns = $schema->getTableColumns('#__support_comments');
        $createdByType = $columns['created_by'] ?? '';

        // Check if column type contains 'int' (handles both 'int(11)' and 'INTEGER')
        if (stripos($createdByType, 'int') !== false) {
            $creators = $this->db->getQuery(true)
                ->select('t.created_by', 'id')
                ->select('u.username')
                ->from('#__support_comments', 't')
                ->leftJoin('#__users AS u', 't.created_by', 'u.id')
                ->where('t.created_by', '>', 0)
                ->loadObjectList();

            if ($creators) {
                foreach ($creators as $creator) {
                    $this->db->getQuery(true)
                        ->update('#__support_comments')
                        ->set(['created_by' => $creator->username])
                        ->where('created_by', '=', $creator->id)
                        ->execute();
                }
            }

            $this->db->schema()->modifyString('#__support_comments', 'created_by', 50)->notNull()->default('');
        }
    }
}
