<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding section grade policy id field
  *
**/
class Migration20130412000000ComCourses extends Base
{
    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__courses_offering_sections', 'grade_policy_id')) {
            $schema
                ->addColumn('#__courses_offering_sections', 'grade_policy_id')
                ->integer()
                ->notNull()
                ->default(1)
                ->execute();
        }
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_offering_sections', 'grade_policy_id')) {
            $schema->dropColumn('#__courses_offering_sections', 'grade_policy_id');
        }
    }
}
