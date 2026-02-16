<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing data type of asset group description field
**/
class Migration20131011164749ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_asset_groups')
            && $schema->hasColumn('#__courses_asset_groups', 'description')
        ) {
            $schema->modifyColumn('#__courses_asset_groups', 'description')->text()->notNull();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_asset_groups')
            && $schema->hasColumn('#__courses_asset_groups', 'description')
        ) {
            $schema->modifyColumn('#__courses_asset_groups', 'description')
                ->string(255)
                ->notNull()
                ->default('')
                ->execute();
        }
    }
}
