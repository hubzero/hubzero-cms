<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for media tracking indices
 *
*/
class Migration20140417203615ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__media_tracking')) {
            $schema->addIndex('#__media_tracking', 'idx_user_id', 'user_id');
            $schema->addIndex('#__media_tracking', 'idx_session_id', 'session_id');
            $schema->addIndex('#__media_tracking', 'idx_object_id', 'object_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__media_tracking')) {
            $schema->dropIndex('#__media_tracking', 'idx_user_id');
            $schema->dropIndex('#__media_tracking', 'idx_session_id');
            $schema->dropIndex('#__media_tracking', 'idx_object_id');
        }
    }
}
