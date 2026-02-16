<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for messages table changes
**/
class Migration20130924000010ComMessages extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();
        $schema->setTableEngine('#__messages', 'MYISAM');
        $schema->setTableEngine('#__messages_cfg', 'MYISAM');

        if ($schema->hasColumn('#__messages', 'subject')) {
            $schema->modifyColumn('#__messages', 'subject')
                ->string(255)
                ->notNull()
                ->default('')
                ->execute();
        }
        if ($schema->hasColumn('#__messages', 'state')) {
            $schema->modifyColumn('#__messages', 'state')
                ->tinyInteger(1)
                ->notNull()
                ->default(0)
                ->execute();
        }
        if ($schema->hasColumn('#__messages', 'priority')) {
            $schema->modifyColumn('#__messages', 'priority')
                ->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
        }
        if ($schema->hasColumn('#__messages', 'folder_id')) {
            $schema->modifyColumn('#__messages', 'folder_id')
                ->tinyInteger(3)
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }
}
