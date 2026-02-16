<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'action_browse' column to #__user_profile_fields
 *
*/
class Migration20160522113201ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_profile_fields')) {
            if (!$schema->hasColumn('#__user_profile_fields', 'action_browse')) {
                $schema->addColumn('#__user_profile_fields', 'action_browse')->tinyInteger(2)->notNull()->default(0);

                $this->db->getQuery(true)
                    ->update('#__user_profile_fields')
                    ->set(['action_browse' => 1])
                    ->whereIn('name', ['organization', 'bio'])
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

        if ($schema->tableExists('#__user_profile_fields')) {
            if ($schema->hasColumn('#__user_profile_fields', 'action_browse')) {
                $schema->dropColumn('#__user_profile_fields', 'action_browse');
            }
        }
    }
}
