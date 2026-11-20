<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for joomla 2.5.28 update
 *
*/
class Migration20150109180705ComUsers extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_profiles') && $schema->hasColumn('#__user_profiles', 'profile_value')) {
            $schema
                ->modifyColumn('#__user_profiles', 'profile_value')
                ->text()
                ->notNull()
                ->execute();
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_profiles') && $schema->hasColumn('#__user_profiles', 'profile_value')) {
            $schema
                ->modifyColumn('#__user_profiles', 'profile_value')
                ->string(255)
                ->notNull()
                ->execute();
        }
    }
}
