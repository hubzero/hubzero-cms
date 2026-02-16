<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding created, creator, modified,
 * and modifier info to tags table
 *
 */
class Migration20140709144527ComTags extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__tags', 'created')) {
            $schema->addColumn('#__tags', 'created')->datetime()->notNull()->default('0000-00-00 00:00:00');
        }

        if (!$schema->hasColumn('#__tags', 'created_by')) {
            $schema->addColumn('#__tags', 'created_by')->integer()->notNull()->default(0);

            $rows = $this->db->getQuery(true)
                ->select('*')
                ->from('#__tags_log')
                ->where('action', '=', 'tag_created')
                ->loadObjectList();

            if ($rows) {
                foreach ($rows as $row) {
                    $this->db->getQuery(true)
                        ->update('#__tags')
                        ->set(['created' => $row->timestamp, 'created_by' => $row->actorid])
                        ->where('id', '=', $row->tag_id)
                        ->execute();
                }
            }
        }

        if (!$schema->hasColumn('#__tags', 'modified')) {
            $schema->addColumn('#__tags', 'modified')->datetime()->notNull()->default('0000-00-00 00:00:00');
        }

        if (!$schema->hasColumn('#__tags', 'modified_by')) {
            $schema->addColumn('#__tags', 'modified_by')->integer()->notNull()->default(0);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__tags', 'created')) {
            $schema->dropColumn('#__tags', 'created');
        }

        if ($schema->hasColumn('#__tags', 'created_by')) {
            $schema->dropColumn('#__tags', 'created_by');
        }

        if ($schema->hasColumn('#__tags', 'modified')) {
            $schema->dropColumn('#__tags', 'modified');
        }

        if ($schema->hasColumn('#__tags', 'modified_by')) {
            $schema->dropColumn('#__tags', 'modified_by');
        }
    }
}
