<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'dependents' column to #__user_profile_options
 *
*/
class Migration20160513211701ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_profile_options')) {
            if (!$schema->hasColumn('#__user_profile_options', 'dependents')) {
                $schema->addColumn('#__user_profile_options', 'dependents')->tinyText()->nullable();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_profile_options')) {
            if ($schema->hasColumn('#__user_profile_options', 'dependents')) {
                $schema->dropColumn('#__user_profile_options', 'dependents');
            }
        }
    }
}
